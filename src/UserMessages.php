<?php

declare(strict_types=1);

namespace Kanvas\Social;

use Baka\Contracts\Auth\UserInterface;
use Kanvas\Social\Models\Messages as MessagesModel;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset\Simple;

class UserMessages
{
    /**
     * Get all the messages of a user.
     *
     * @param UserInterface $user
     * @param int $limit
     * @param int $page
     *
     * @return Simple
     */
    public static function getAll(UserInterface $user, int $page = 1, int $limit = 25) : Simple
    {
        $appData = Di::getDefault()->get('app');

        $offSet = ($page - 1) * $limit;
        $message = new MessagesModel();

        return new Simple(
            null,
            $message,
            $message->getReadConnection()->query(
                'SELECT  * 
            FROM 
                user_messages 
                LEFT JOIN 
                messages on messages.id = user_messages.messages_id 
            WHERE user_messages.users_id = :userId
            AND user_messages.is_deleted = 0 
            AND messages.is_deleted = 0
            AND messages.apps_id = :appId
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset',
                [
                    'userId' => $user->getId(),
                    'limit' => $limit,
                    'offset' => $offSet,
                    'appId' => $appData->getId()

                ]
            )
        );
    }

    /**
     * Get the count of all user messages.
     *
     * @param UserInterface $user
     *
     * @return int
     */
    public static function getCount(UserInterface $user) : int
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
                    'appId' => $appData->getId()

                ]
            )
        );

        return $result[0]->count;
    }
}
