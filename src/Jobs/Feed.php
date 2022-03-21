<?php

namespace Kanvas\Social\Jobs;

use Baka\Contracts\Queue\QueueableJobInterface;
use Baka\Jobs\Job;
use Kanvas\Social\ElasticDocuments\Messages as ElasticDocumentsMessages;
use Kanvas\Social\Models\UserMessages;
use Kanvas\Social\Services\Follow;
use Phalcon\Di;
use Phalcon\Mvc\ModelInterface;

class Feed extends Job implements QueueableJobInterface
{
    protected ElasticDocumentsMessages $elasticMessage;
    protected ModelInterface $entity;
    protected ModelInterface $message;

    /**
     * Construct.
     *
     * @param ModelInterface $entity
     * @param ModelInterface $message
     */
    public function __construct(ModelInterface $entity, ModelInterface $message)
    {
        $this->entity = $entity;
        $this->message = $message;
    }

    /**
     * Handle that delete the message contains in user Message.
     *
     * @return bool
     */
    public function handle() : bool
    {
        Di::getDefault()->get('log')->info('Feed message to elastic ' . $this->message->getId());
        $followers = Follow::getFollowers($this->entity);
        foreach ($followers as $follower) {
            $usersMessage = new UserMessages;
            $usersMessage->assign([
                'users_id' => $follower->users_id,
                'messages_id' => $this->message->getId(),
            ]);
            $usersMessage->saveOrFail();
        }

        return true;
    }
}
