<?php

declare(strict_types=1);

namespace Kanvas\Social;

use Baka\Contracts\Auth\UserInterface;
use Kanvas\Social\Models\Messages as MessagesModel;
use Kanvas\Social\Models\UserMessages as UserMessagesModel;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset\Simple;

class UserMessages
{
    /**
     * Get all the messages of a user.
     */
    public static function getAllAsUserMessages(UserInterface $user, int $page = 1, int $limit = 25): Simple
    {
        $appData = Di::getDefault()->get('app');

        $offSet = ($page - 1) * $limit;
        $message = new UserMessagesModel();

        return new Simple(
            null,
            $message,
            $message->getReadConnection()->query(
                'SELECT user_messages.* 
            FROM 
                user_messages 
                LEFT JOIN 
                messages on messages.id = user_messages.messages_id 
            WHERE user_messages.users_id = :userId
            AND user_messages.is_deleted = 0 
            AND messages.is_deleted = 0
            AND messages.apps_id = :appId
            ORDER BY user_messages.created_at DESC
            LIMIT :limit OFFSET :offset',
                [
                    'userId' => $user->getId(),
                    'limit' => $limit,
                    'offset' => $offSet,
                    'appId' => $appData->getId(),

                ]
            )
        );
    }

    /**
     * Get all the messages of a user.
     */
    public static function getAll(UserInterface $user, int $page = 1, int $limit = 25): Simple
    {
        $appData = Di::getDefault()->get('app');

        $offSet = ($page - 1) * $limit;
        $message = new MessagesModel();

        return new Simple(
            null,
            $message,
            $message->getReadConnection()->query(
                'SELECT DISTINCT user_messages.messages_id, 
                user_messages.users_id,
                user_messages.notes, 
                user_messages.is_liked, 
                user_messages.is_saved, 
                user_messages.is_shared, 
                user_messages.is_reported, 
                user_messages.reactions, 
                user_messages.saved_lists, 
                user_messages.activities, 
            FROM 
                user_messages 
                LEFT JOIN 
                messages on messages.id = user_messages.messages_id 
            WHERE user_messages.users_id = :userId
            AND user_messages.is_deleted = 0 
            AND messages.is_deleted = 0
            AND messages.apps_id = :appId
            ORDER BY user_messages.created_at DESC
            LIMIT :limit OFFSET :offset',
                [
                    'userId' => $user->getId(),
                    'limit' => $limit,
                    'offset' => $offSet,
                    'appId' => $appData->getId(),

                ]
            )
        );
    }

    /**
     * getInteractions.
     */
    public static function getInteractions(UserInterface $user, MessagesModel $message): array
    {
        $userMessages = UserMessagesModel::findFirst([
            'conditions' => 'users_id = :userId: AND messages_id = :messageId: AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'messageId' => $message->getId(),
            ],
        ]);

        $activity = $userMessages->getActivities([
            'sort' => 'id ASC',
        ]);
        $activity = $activity ? $activity->getFirst() : null;
        $count = $userMessages->countActivities([
            'sort' => 'id DESC',
            'conditions' => 'type = :type:',
            'bind' => [
                'type' => $activity ? $activity->type : null,
            ],
        ]);

        return  [
            'notes' => $userMessages->notes,
            'is_liked' => $userMessages->is_liked,
            'is_saved' => $userMessages->is_saved,
            'is_shared' => $userMessages->is_shared,
            'is_reported' => $userMessages->is_reported,
            'message_activity_count' => $count,
            'message_type_activity' => $activity ? $activity->type : '',
            'message_activity_username' => $activity ? $activity->username : '',
            'message_activity_text' => $activity ? $activity->text : '',
        ];
    }

    /**
     * Get the count of all user messages.
     */
    public static function getCount(UserInterface $user): int
    {
        $appData = Di::getDefault()->get('app');
        $message = new MessagesModel();

        $result = new Simple(
            null,
            $message,
            $message->getReadConnection()->query(
                'SELECT count(*) as count
            FROM 
                user_messages 
                LEFT JOIN 
                messages on messages.id = user_messages.messages_id 
            WHERE user_messages.users_id = :userId
            AND user_messages.is_deleted = 0 
            AND messages.is_deleted = 0
            AND messages.apps_id = :appId',
                [
                    'userId' => 333,
                    'appId' => $appData->getId(),

                ]
            )
        );

        return $result[0]->count;
    }

    /**
     * like.
     *
     * @param  MessagesModel $model
     */
    public static function like(UserInterface $user, MessagesModel $message): void
    {
        $userMessages = UserMessagesModel::findFirstOrCreate(
            [
                'conditions' => 'users_id = :userId: AND messages_id = :messageId:',
                'bind' => [
                    'userId' => $user->getId(),
                    'messageId' => $message->getId(),
                ],
            ],
            [
                'is_deleted' => 1,
                'users_id' => $user->getId(),
                'messages_id' => $message->getId(),
            ]
        );
        $userMessages->is_liked = $userMessages->is_liked ? 0 : 1;
        $userMessages->saveOrFail();
    }

    /**
     * save.
     */
    public static function save(UserInterface $user, MessagesModel $message): void
    {
        $userMessages = UserMessagesModel::findFirstOrCreate(
            [
                'conditions' => 'users_id = :userId: AND messages_id = :messageId:',
                'bind' => [
                    'userId' => $user->getId(),
                    'messageId' => $message->getId(),
                ],
            ],
            [
                'is_deleted' => 1,
                'users_id' => $user->getId(),
                'messages_id' => $message->getId(),
            ]
        );
        $userMessages->is_saved = $userMessages->is_liked ? 0 : 1;
        $userMessages->saveOrFail();
    }
}
