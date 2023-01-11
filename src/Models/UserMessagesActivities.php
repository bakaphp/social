<?php

declare(strict_types=1);

namespace Kanvas\Social\Models;

class UserMessagesActivities extends BaseModel
{
    public int $user_messages_id = 0;
    public ?string $entity_namespace = null;
    public int $from_entity_id = 0;
    public ?string $type = null;
    public ?string $username = null;
    public ?string $text = null;

    /**
     * Initialize relationship after fetch
     * since we need entity_namespace info.
     *
     * @return void
     */
    public function afterFetch()
    {
        if ($this->entity_namespace !== null) {
            $this->hasOne(
                'from_entity_id',
                $this->entity_namespace,
                'id',
                [
                    'reusable' => true,
                    'alias' => 'entityData'
                ]
            );
        }
    }

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
