<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

use Canvas\Models\Users;
use Kanvas\Social\Interactions;
use Kanvas\Social\Traits\EntityData;

class UsersReactions extends BaseModel
{
    use EntityData;

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
        $this->belongsTo(
            'users_id',
            Users::class,
            'id',
            [
                'alias' => 'users',
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


    /**
     * afterSave.
     *
     * @return void
     */
    public function afterSave()
    {
        $users = Users::findFirstOrFail($this->users_id);
        Interactions::add($users, $this->retrieveEntityData(), UsersInteractions::REACTION);
    }
}
