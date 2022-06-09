<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

use Baka\Contracts\Auth\UserInterface;
use Canvas\Contracts\EventManagerAwareTrait;
use Kanvas\Social\Traits\EntityData;
use Phalcon\Mvc\ModelInterface;

class UsersInteractions extends BaseModel
{
    use EventManagerAwareTrait;
    use EntityData;

    public int $users_id = 0;
    public int $entity_id = 0;
    public string $entity_namespace = '';
    public int $interactions_id = 0;
    public ?string $notes = null;

    const LIKE = 'like';
    const SAVE = 'save';
    const COMMENT = 'comment';
    const REPLIED = 'reply';
    const FOLLOWING = 'follow';
    const REACTION = 'reaction';
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
                'alias' => 'entityData',
                'params' => [
                    'conditions' => 'is_deleted = 0'
                ]
            ]
        );
        $this->belongsTo(
            'interactions_id',
            Interactions::class,
            'id',
            [
                'alias' => 'interactions'
            ]
        );
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('users_interactions');
    }

    /**
     * Given the entity and its interaction check if user interact with it.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     * @param Interactions $interaction
     *
     * @return self|null
     */
    public static function getByEntityInteraction(UserInterface $user, ModelInterface $entity, Interactions $interaction) : ?self
    {
        return self::findFirst([
            'conditions' => 'users_id = :userId: 
                                AND interactions_id = :interactionId: 
                                AND entity_namespace = :namespace: 
                                AND entity_id = :entityId:',
            'bind' => [
                'userId' => $user->getId(),
                'interactionId' => $interaction->getId(),
                'namespace' => get_class($entity),
                'entityId' => $entity->getId(),
            ]
        ]);
    }

    /**
     * After create.
     *
     * @return void
     */
    public function afterCreate()
    {
        if (method_exists(get_parent_class($this), 'afterCreate')) {
            parent::afterCreate();
        }
        $this->fireToQueue('kanvas.social.interactions:afterCreate', $this);
    }

    /**
     * After create.
     *
     * @return void
     */
    public function afterSave()
    {
        if (method_exists(get_parent_class($this), 'afterSave')) {
            parent::afterSave();
        }
        $this->fireToQueue('kanvas.social.interactions:afterSave', $this);
    }
}
