<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

class UsersReactions extends BaseModel
{
    public $id;
    public int $users_id;
    public int $entity_id;
    public string $entity_namespace;

    /**
     * Initialize relationship after fetch
     * since we need entity_namespace info.
     *
     * @return void
     */
    public function afterFetch()
    {
        $this->hasOne(
            'entity_id',
            $this->entity_namespace,
            'id',
            [
                'reusable' => true,
                'alias' => 'entityData'
            ]
        );
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('users_reactions');
    }
}
