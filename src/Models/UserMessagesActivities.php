<?php

declare(strict_types = 1);

namespace Kanvas\Social\Models;

class UserMessagesActivities extends BaseModel
{
    public int $user_messages_id;
    public int $from_entity_id;
    public string $type;
    public string $username;
    public string $text;
    
    /**

    * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('user_messages_activities');
        $this->belongsTo('user_messages_id', UserMessages::class, 'id', ['alias' => 'user_message']);
    }
}
