<?php

declare(strict_types=1);
namespace Kanvas\Social;

use Baka\Contracts\Auth\UserInterface;
use Kanvas\Social\Contracts\Follows\FollowableInterface;
use Kanvas\Social\Contracts\Messages\MessagesInterface;
use Kanvas\Social\Models\Interactions;
use Kanvas\Social\Models\UserMessages;
use Kanvas\Social\Models\UserMessagesActivities;
use Kanvas\Social\Models\UsersFollows;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\ModelInterface;

class Follow
{
    /**
     * Return the data of entities that the user follows.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     *
     * @return Simple
     */
    public static function getFollowsByUser(UserInterface $user, ModelInterface $entity) : Simple
    {
        $userFollows = UsersFollows::find([
            'conditions' => 'users_id = :user_id: AND entity_namespace = :entity: AND is_deleted = 0',
            'bind' => [
                'user_id' => $user->getId(),
                'entity' => get_class($entity),
            ]
        ]);

        return $userFollows;
    }

    /**
     * Follow and unfollow an entity if its exist.
     *
     * @param UserInterface $userFollowing User that is following
     * @param ModelInterface $entity Entity that is being followed
     *
     * @return bool
     */
    public static function userFollow(UserInterface $user, ModelInterface $entity) : bool
    {
        return self::follow($user, $entity);
    }

    /**
     * Allow a User to follow a entity.
     *
     * @param UserInterface $userFollowing
     * @param ModelInterface $entity
     *
     * @return bool
     */
    public static function follow(UserInterface $user, ModelInterface $entity) : bool
    {
        $follow = UsersFollows::getByUserAndEntity($user, $entity);

        if ($follow) {
            return $follow->unFollow();
        }

        //global following means we don't take into account the current user company
        $globalFollowing = Di::getDefault()->get('config')->social->global_following ?? true;

        $follow = new UsersFollows();
        $follow->users_id = $user->getId();
        $follow->entity_id = $entity->getId();
        $follow->entity_namespace = get_class($entity);
        $follow->companies_id = $globalFollowing ? 0 : $user->getDefaultCompany()->getId();
        $follow->companies_branches_id = $globalFollowing ? 0 : $user->currentBranchId();
        $follow->saveOrFail();

        // $follow->increment(Interactions::FOLLOWING, get_class($entity));
        // $user->increment(Interactions::FOLLOWERS, get_class($entity));

        return $follow->isFollowing();
    }

    /**
     * Unfollow an entity.
     *
     * @param UserInterface $userFollowing
     * @param ModelInterface $entity
     *
     * @return bool
     */
    public static function unFollow(UserInterface $user, ModelInterface $entity) : bool
    {
        //follows return false when it unfollow, so we reverse it
        return !self::follow($user, $entity);
    }

    /**
     * Is a user following an entity?
     *
     * @param UserInterface $userFollowing
     * @param ModelInterface $entity
     *
     * @return bool
     */
    public static function isFollowing(UserInterface $user, ModelInterface $entity) : bool
    {
        return (bool) UsersFollows::count([
            'conditions' => 'users_id = :userId: AND entity_id = :entityId: AND entity_namespace = :entityName: AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'entityId' => $entity->getId(),
                'entityName' => get_class($entity)
            ]
        ]);
    }

    /**
     * Get total followers.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     *
     * @return int
     */
    public static function getTotalFollowing(UserInterface $user, string $entityNamespace) : int
    {
        return  UsersFollows::count([
            'conditions' => 'users_id = :userId:  AND entity_namespace = :entityName: AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'entityName' => $entityNamespace
            ]
        ]);
    }

    /**
     * Get total followers of this entity.
     *
     * @param ModelInterface $entity
     *
     * @return int
     */
    public static function getTotalFollowers(ModelInterface $entity) : int
    {
        return UsersFollows::count([
            'conditions' => 'entity_id = :entityId: AND entity_namespace = :entityName: AND is_deleted = 0',
            'bind' => [
                'entityId' => $entity->getId(),
                'entityName' => get_class($entity)
            ]
        ]);
    }

    /**
     * getFollowers.
     *
     * @param  ModelInterface $entity
     *
     * @return Simple
     */
    public static function getFollowers(ModelInterface $entity) : Simple
    {
        return UsersFollows::find([
            'conditions' => 'entity_id = :entityId: AND entity_namespace = :entityName: AND is_deleted = 0',
            'bind' => [
                'entityId' => $entity->getId(),
                'entityName' => get_class($entity)
            ]
        ]);
    }

    /**
     * addToFeed.
     *
     * @param FollowableInterface $from
     * @param UserInterface $user
     * @param MessagesInterface $message
     * @param array|null $notes
     * @param array|null $activities
     *
     * @return void
     */
    public static function addToFeed(
        FollowableInterface $from,
        UserInterface $user,
        MessagesInterface $message,
        ?array $notes = null,
        ?array $activities = null
    ) : void {
        $feed = UserMessages::findFirst([
            'conditions' => 'users_id = :userId: AND messages_id = :messageId: AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'messageId' => $message->getId()
            ]
        ]);

        if (!$feed) {
            $feed = new UserMessages();
            $feed->users_id = $user->getId();
            $feed->messages_id = $message->getId();
            $feed->saveOrFail();
            $feed->set('notes', $notes);
        } else {
            if ($notes) {
                $notes = array_merge($feed->get('notes'), $notes);
                $feed->set('notes', $notes);
            }
        }

        if ($activities) {
            self::addActivities($from, $feed, $activities);
        }
        self::fillActivity($feed);
    }

    /**
     * fillActivity
     *
     * @param  UserMessages $feed
     * @return void
     */
    public static function fillActivity(UserMessages $feed): array
    {
        $lastActivity = UserMessagesActivities::findFirst([
            'conditions' => 'user_messages_id = :feedId: AND is_deleted = 0',
            'order' => 'id DESC',
            'bind' => [
                'feedId' => $feed->getId()
            ],
        ]);
        if (!$lastActivity) {
            return [];
        }
        $countActivty = $feed->countActivities([
            'conditions' => 'type = :type:',
            'bind' => [
                'type' => $lastActivity->type
            ]
        ]);
        if ($lastActivity->entity_namespace && class_exists($lastActivity->entity_namespace)) {
            $entityData = $lastActivity->entity_namespace::findFirst($lastActivity->from_entity_id);
            $username = $entityData->displayname ?? $entityData->name;
        }
        $username = $username ?? '';
        $userMessageActivity = [
            'notes' => $feed->notes,
            'message_activity_count' => $countActivty,
            'message_type_activity' => $lastActivity->type,
            'message_activity_username' => $username,
            'message_activity_text' => $lastActivity->text
        ];
        $feed->activities = json_encode($userMessageActivity);
        $feed->saveOrFail();
        return $userMessageActivity;
    }

    /**
     * feedToFollowers.
     *
     * @param  FollowableInterface $entity
     * @param  MessagesInterface $message
     * @param  array $notes
     *
     * @return void
     */
    public static function feedToFollowers(
        FollowableInterface $entity,
        MessagesInterface $message,
        ?array $notes = null,
        ?array $activities = null
    ) : void {
        $followers = self::getFollowers($entity);
        foreach ($followers as $follower) {
            if ($follower->user instanceof UserInterface) {
                self::addToFeed(
                    $entity,
                    $follower->user,
                    $message,
                    $notes,
                    $activities
                );
            }
        }
    }

    /**
     * removeToFollowers.
     *
     * @param  FollowableInterface $entity
     * @param  MessagesInterface $message
     * @param  array $activity
     *
     * @return void
     */
    public static function removeToFollowers(FollowableInterface $entity, MessagesInterface $message, array $activity) : void
    {
        $followers = self::getFollowers($entity) ;
        foreach ($followers as $follow) {
            if ($follow->user instanceof UserInterface) {
                $userMessage = UserMessages::findFirst([
                    'conditions' => 'users_id = :users_id: AND messages_id = :messages_id:',
                    'bind' => [
                        'users_id' => $follow->user->getId(),
                        'messages_id' => $message->getId(),
                    ]
                ]);
                if ($userMessage && $userMessage->getActivities()->count()) {
                    $userActivity = $userMessage->getActivities([
                        'conditions' => 'from_entity_id = :from_entity_id: AND type = :type: AND text = :text: AND username = :username:',
                        'bind' => [
                            'from_entity_id' => $entity->getId(),
                            'type' => $activity['type'],
                            'text' => $activity['text'],
                            'username' => $activity['username']
                        ]
                    ]);

                    if ($userActivity) {
                        $userActivity->delete();
                    }

                    if (!$userMessage->getActivities()->count()) {
                        $userMessage->delete();
                    }
                    continue;
                }

                if ($userMessage) {
                    $userMessage->delete();
                }
                //Di::getDefault()->get('log')->info('Delete Feed by user ' . $follow->user->id . ' Entity ' . $entity->getId());
            }
        }
    }

    /**
     * addActivities.
     *
     * @param  UserMessages $feed
     * @param  array $activities
     *
     * @return void
     */
    protected static function addActivities(FollowableInterface $from, UserMessages $feed, ?array $activity = null) : void
    {
        $activities = new UserMessagesActivities();
        $activities->user_messages_id = $feed->getId();
        $activities->from_entity_id = $from->getId();
        $activities->entity_namespace = $activity['classname'];
        $activities->type = $activity['type'];
        $activities->text = $activity['text'];
        $activities->username = $activity['username'];
        $activities->saveOrFail();
    }
}
