<?php
declare(strict_types=1);

namespace Kanvas\Social\Comments\Models;

use Kanvas\Social\Comments\Model;

class Entity extends Model
{
    public ?string $entity_id = null;
    public ?string $entity_namespace = null;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('entity_comments');

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
                'alias' => 'entity',
                'params' => [
                    'conditions' => 'is_deleted = 0'
                ]
            ]
        );
    }
}
