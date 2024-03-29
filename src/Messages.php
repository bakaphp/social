<?php

declare(strict_types=1);

namespace Kanvas\Social;

use Baka\Contracts\Auth\UserInterface;
use Baka\Contracts\Database\ModelInterface;
use Canvas\Models\Companies;
use Kanvas\Social\Contracts\Messages\MessagesInterface;
use Kanvas\Social\Jobs\GenerateTags;
use Kanvas\Social\Jobs\RemoveMessagesFeed;
use Kanvas\Social\Models\AppModuleMessage;
use Kanvas\Social\Models\ChannelMessages;
use Kanvas\Social\Models\Channels;
use Kanvas\Social\Models\Messages as MessagesModel;
use Kanvas\Social\Models\MessageTypes as MessageTypesModel;
use Kanvas\Social\Models\UserMessages;
use Kanvas\Social\UserMessages as SocialUserMessages;
use Phalcon\Di;
use Phalcon\Mvc\Model\Resultset\Simple;

class Messages
{
    /**
     * Return a Message object by its id.
     *
     * @param string $id
     *
     * @return MessagesInterface
     */
    public static function getMessage(string $id) : MessagesInterface
    {
        return MessagesModel::getByIdOrFail($id);
    }

    /**
     * Return a Message object by its uuid.
     *
     * @param string $uuid
     *
     * @return MessagesInterface
     */
    public static function getMessageByUuid(string $uuid) : MessagesInterface
    {
        return MessagesModel::findFirstOrFail([
            'conditions' => 'uuid = :uuid: AND is_deleted = 0',
            'bind' => ['uuid' => $uuid]
        ]);
    }

    /**
     * Get all the messages of a user.
     *
     * @param UserInterface $user
     * @param int $limit
     * @param int $page
     *
     * @deprecated  v2
     *
     * @return Simple
     */
    public static function getByUser(UserInterface $user, int $page = 1, int $limit = 25) : Simple
    {
        return SocialUserMessages::getAll($user, $page, $limit);
    }

    /**
     * Get all the messages of a channel.
     *
     * @param Channels $user
     * @param array $filter
     *
     * @deprecated
     *
     * @return Simple
     */
    public static function getByChannel(
        Channels $channel,
        int $page = 1,
        int $limit = 25,
        string $orderBy = 'id',
        string $sort = 'DESC',
        ?string $messageTypeId = null
    ) : Simple {
        $feed = new ChannelMessages();
        return $feed->getMessagesByChannel($channel, $page, $limit, $orderBy, $sort, $messageTypeId);
    }

    /**
     * Create a new Msg.
     *
     * @param UserInterface $user
     * @param string $verb
     * @param array $message
     * @param array $object contains the entity object + its id.
     * @param string $distribution
     *
     * @return UserMessages
     */
    public static function create(
        UserInterface $user,
        string $verb,
        array $message = [],
        ?ModelInterface $object = null,
        bool $sendToUserFeeds = true,
        ?Companies $company = null
    ) : MessagesInterface {
        $newMessage = new MessagesModel();
        $newMessage->apps_id = Di::getDefault()->get('app')->getId();
        $newMessage->companies_id = $company === null ? $user->getDefaultCompany()->getId() : $company->getId();
        $newMessage->users_id = (int) $user->getId();
        $newMessage->message_types_id = MessageTypesModel::getTypeByVerb($verb)->getId();
        $newMessage->message = json_encode($message);
        $newMessage->created_at = date('Y-m-d H:i:s');
        $newMessage->saveOrFail();

        if ($object) {
            $newMessage->addSystemModules($object);
        }
        $newMessage->fireToQueue('messages:created', $newMessage, ['entity' => $object]);

        if ($sendToUserFeeds) {
            Distributions::sendToUsersFeeds($newMessage, $user);
        }

        GenerateTags::dispatch($user, $newMessage);

        return $newMessage;
    }

    /**
     * Create a new msg from a Object.
     *
     * @param UserInterface $user
     * @param string $verb
     * @param array $message
     * @param array $object contains the entity object + its id.
     * @param string $distribution
     *
     * @return UserMessages
     */
    public static function createByObject(
        UserInterface $user,
        string $verb,
        MessagesInterface $newMessage,
        ModelInterface $object,
        bool $sendToUserFeeds = true,
        ?Companies $company = null
    ) : MessagesInterface {
        $newMessage->apps_id = Di::getDefault()->get('app')->getId();
        $newMessage->companies_id = $company === null ? $user->getDefaultCompany()->getId() : $company->getId();
        $newMessage->users_id = (int) $user->getId();
        $newMessage->message_types_id = MessageTypesModel::getTypeByVerb($verb)->getId();
        $newMessage->created_at = date('Y-m-d H:i:s');
        $newMessage->saveOrFail();

        $newMessage->addSystemModules($object);

        if ($sendToUserFeeds) {
            Distributions::sendToUsersFeeds($newMessage, $user);
        }

        GenerateTags::dispatch($user, $newMessage);

        return $newMessage;
    }

    /**
     * To be describe.
     *
     * @param string $uuid
     * @param array $message
     *
     * @return void
     */
    public static function update(string $uuid, array $message)
    {
    }

    /**
     * Delete the message and remove it from the users feeds.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function delete(string $uuid) : bool
    {
        $message = MessagesModel::getByIdOrFail($uuid);

        RemoveMessagesFeed::dispatch($message);

        return (bool) $message->softDelete();
    }

    /**
     * Get the message from an MessagesInterface if exist.
     *
     * @param MessagesInterface $object
     *
     * @return MessagesModel
     */
    public static function getMessageFrom(ModelInterface $object) : MessagesModel
    {
        $module = AppModuleMessage::findFirstOrFail([
            'conditions' => 'system_modules = :objectNamespace: AND entity_id = :entityId: AND
                            apps_id = :appId: AND is_deleted = 0',
            'bind' => [
                'objectNamespace' => get_class($object),
                'entityId' => $object->getId(),
                'appId' => Di::getDefault()->get('app')->getId(),
            ]
        ]);

        return $module->getMessage();
    }

    /**
     * Return the App Module Message data from a message.
     *
     * @param MessagesModel $message
     *
     * @return AppModuleMessage
     */
    public static function getAppModuleMessageFromMessage(MessagesModel $message) : AppModuleMessage
    {
        return $message->getAppModuleMessage([
            'conditions' => 'is_deleted = 0'
        ]);
    }
}
