<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

use Baka\Contracts\Auth\UserInterface;
use function Baka\isJson;
use Canvas\Contracts\CustomFields\CustomFieldsTrait;
use Illuminate\Support\Collection;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Paginator\Adapter\Model;

class UserMessages extends BaseModel
{
    use CustomFieldsTrait;

    public int $messages_id;
    public int $users_id;
    public ?string $notes = null;
    public int $is_liked = 0;
    public int $is_saved = 0;
    public int $is_shared = 0;
    public int $is_reported = 0;
    public ?string $reactions = null;
    public ?string $saved_lists = null;
    public ?string $activities = null;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('user_messages');
        $this->belongsTo('messages_id', Messages::class, 'id', ['alias' => 'message', 'reusable' => true]);
        $this->hasMany('id', UserMessagesActivities::class, 'user_messages_id', ['alias' => 'activities',  'reusable' => true]);
    }

    /**
     * Return all the messages that the user have in its feed.
     *
     * @param UserInterface $user
     * @param int $limit
     * @param int $page
     *
     * @return Simple
     */
    public function getUserFeeds(UserInterface $user, int $limit = 25, int $page = 1) : Simple
    {
        $appData = Di::getDefault()->get('app');

        $offSet = ($page - 1) * $limit;

        $userFeeds = new Simple(
            null,
            new Messages(),
            $this->getReadConnection()->query(
                "SELECT 
            * 
            from 
                user_messages 
                left join 
                messages on messages.id = user_messages.messages_id 
            where user_messages.users_id = {$user->getId()}
            and user_messages.is_deleted = 0 
            and messages.apps_id = {$appData->getId()}
            ORDER id DESC
            limit {$limit} OFFSET {$offSet}"
            )
        );

        return $userFeeds;
    }

    /**
     * getActivity.
     *
     * @return ?Collection
     */
    public function getActivity() : ?Collection
    {
        if (isJson($this->activities)) {
            return collect(json_decode($this->activities, true));
        }
        return null;
    }
}
