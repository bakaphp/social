<?php
declare(strict_types=1);

namespace Kanvas\Social\Comments;

use Baka\Contracts\Auth\UserInterface;
use Canvas\Contracts\EventManagerAwareTrait;
use Canvas\Contracts\FileSystemModelTrait;
use Canvas\Models\Users;
use Kanvas\Social\Contracts\Comments\Comments;
use Kanvas\Social\Contracts\Interactions\EntityInteractionsTrait;
use Kanvas\Social\Interactions;
use Kanvas\Social\Models\BaseModel;
use Kanvas\Social\Models\UsersInteractions;

class Model extends BaseModel implements Comments
{
    use EntityInteractionsTrait;
    use FileSystemModelTrait;
    use EventManagerAwareTrait;

    public $id;
    public int $apps_id;
    public int $companies_id;
    public int $users_id = 0;
    public string $message = '';
    public int $reactions_count = 0;
    public int $parent_id = 0;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->belongsTo(
            'users_id',
            Users::class,
            'id',
            [
                'alias' => 'users',
                'reusable' => true
            ]
        );

        $this->hasMany(
            'id',
            UsersInteractions::class,
            'entity_id',
            [
                'reusable' => true,
                'alias' => 'interactions',
                'params' => [
                    'conditions' => 'entity_namespace = :namespace:',
                    'bind' => [
                        'namespace' => get_class($this)
                    ]
                ]
            ]
        );

        $this->hasOne(
            'id',
            UsersInteractions::class,
            'entity_id',
            [
                'reusable' => true,
                'alias' => 'interaction',
                'params' => [
                    'conditions' => 'entity_namespace = :namespace:',
                    'bind' => [
                        'namespace' => get_class($this)
                    ]
                ]
            ]
        );

        $this->hasMany(
            'id',
            UsersReactions::class,
            'entity_id',
            [
                'reusable' => true,
                'alias' => 'reactions',
                'params' => [
                    'conditions' => 'entity_namespace = :namespace: AND is_deleted = 0',
                    'bind' => [
                        'namespace' => get_class($this)
                    ]
                ]
            ]
        );

        $this->hasOne(
            'id',
            UsersReactions::class,
            'entity_id',
            [
                'reusable' => true,
                'alias' => 'reaction',
                'params' => [
                    'conditions' => 'entity_namespace = :namespace: AND is_deleted = 0',
                    'bind' => [
                        'namespace' => get_class($this)
                    ]
                ]
            ]
        );
    }

    /**
     * Return the id of the parent in case that comment is a reply.
     *
     * @return int
     */
    public function getParentId() : int
    {
        return $this->parent_id == 0 ? $this->getId() : $this->parent_id;
    }

    /**
     * Check if comment is parent.
     *
     * @return bool
     */
    public function isParent() : bool
    {
        return $this->parent_id == 0;
    }

    /**
     * Create a comment for a message.
     *
     * @param string $messageId
     * @param string $message
     *
     * @return self
     */
    public function reply(string $message, UserInterface $user) : self
    {
        $comment = new static();
        $comment->message_id = $this->message_id;
        $comment->apps_id = $this->apps_id;
        $comment->companies_id = $this->companies_id;
        $comment->users_id = $user->getId();
        $comment->message = $message;
        $comment->parent_id = $this->getParentId();
        $comment->saveOrFail();

        return $comment;
    }


    /**
     * After update.
     *
     * @return void
     */
    public function afterSave()
    {
        $this->associateFileSystem();
        $users = Users::findFirstOrFail($this->users_id);
        Interactions::add($users, $this, UsersInteractions::COMMENT);
    }
}
