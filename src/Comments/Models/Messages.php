<?php
declare(strict_types=1);

namespace Kanvas\Social\Comments\Models;

use Kanvas\Social\Comments\Model;
use Kanvas\Social\ElasticDocuments\Messages as ElasticMessagesDocument;
use Kanvas\Social\Jobs\ElasticMessages;
use Kanvas\Social\Models\Messages as MessagesModel;

class Messages extends Model
{
    public int $message_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('message_comments');

        $this->belongsTo(
            'message_id',
            MessagesModel::class,
            'id',
            [
                'reusable' => true,
                'alias' => 'messages',
                'params' => [
                    'conditions' => 'is_deleted = 0'
                ]
            ]
        );

        $this->hasMany(
            'id',
            self::class,
            'parent_id',
            [
                'reusable' => true,
                'alias' => 'replies',
                'params' => [
                    'conditions' => 'is_deleted = 0',
                ]
            ]
        );
    }

    /**
     * Verify if this comment has message.
     *
     * @param Messages $message
     *
     * @return bool
     */
    public function hasMessage(Messages $message) : bool
    {
        return $this->message_id == $message->getId();
    }

    /**
     * After update.
     *
     * @return void
     */
    public function afterSave()
    {
        parent::afterSave();
        $elasticMessage = new ElasticMessagesDocument();
        $elasticMessage->updateCommentsCount($this->messages);
        ElasticMessages::dispatch($this->messages);
    }
}
