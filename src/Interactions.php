<?php

declare(strict_types=1);

namespace Kanvas\Social;

use Baka\Contracts\Auth\UserInterface;
use Kanvas\Social\Models\Interactions as InteractionsModel;
use Kanvas\Social\Models\UsersInteractions;
use Phalcon\Mvc\ModelInterface;

class Interactions
{
    /**
     * Create an interaction of a user and a entity.
     *
     * @param ModelInterface $entity
     * @param int $interactionId
     *
     * @return bool
     */
    public static function add(UserInterface $user, ModelInterface $entity, string $interactionName, ?string $notes = null) : UsersInteractions
    {
        $interaction = InteractionsModel::getByName($interactionName);

        $userInteraction = UsersInteractions::getByEntityInteraction($user, $entity, $interaction);

        if ($userInteraction && !InteractionsModel::isComment($interaction->getId())) {
            self::removeInteraction($userInteraction);
            return $userInteraction;
        } elseif (!$userInteraction) {
            $userInteraction = new UsersInteractions();
            $userInteraction->users_id = $user->getId();
            $userInteraction->entity_namespace = get_class($entity);
            $userInteraction->entity_id = $entity->getId();
            $userInteraction->interactions_id = $interaction->getId();
            $userInteraction->notes = $notes;
            $userInteraction->saveOrFail();
            $userInteraction->fireToQueue('interaction:created', $userInteraction);
        }

        //if is_deleted = 0 means it was added
        return $userInteraction;
    }

    /**
     * Determine if the user has a interaction of this type with the entity.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     * @param string $interactionName
     *
     * @return bool
     */
    public static function has(UserInterface $user, ModelInterface $entity, string $interactionName) : bool
    {
        $interaction = InteractionsModel::getByName($interactionName);

        return  (bool) UsersInteractions::count([
            'conditions' => 'users_id = :userId:  
                            AND interactions_id = :interactionId:  
                            AND entity_namespace = :namespace: 
                            AND entity_id = :entityId:
                            AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'interactionId' => $interaction->getId(),
                'namespace' => get_class($entity),
                'entityId' => $entity->getId(),
            ]
        ]);
    }

    /**
     * Remove user interaction.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     * @param string $interactionName
     *
     * @return bool
     */
    public static function remove(UserInterface $user, ModelInterface $entity, string $interactionName) : bool
    {
        $interaction = InteractionsModel::getByName($interactionName);

        $userInteraction = UsersInteractions::getByEntityInteraction($user, $entity, $interaction);

        self::removeInteraction($userInteraction);

        return (bool) $userInteraction->is_deleted;
    }

    /**
     * Get interaction object by its name.
     *
     * @param string $interactionName
     *
     * @return InteractionsModel
     */
    public static function getInteractionByName(string $interactionName) : InteractionsModel
    {
        return InteractionsModel::findFirstOrFail([
            'conditions' => 'name = :name: AND is_deleted = 0',
            'bind' => [
                'name' => $interactionName
            ]
        ]);
    }

    /**
     * Remove the user interaction by update is_deleted.
     *
     * @return void
     */
    public static function removeInteraction(UsersInteractions $interaction) : void
    {
        if ($interaction->is_deleted) {
            $interaction->is_deleted = 0;
        } else {
            $interaction->is_deleted = 1;
        }

        $interaction->saveOrFail();
    }

    /**
     * Get total followers.
     *
     * @param UserInterface $user
     * @param ModelInterface $entity
     *
     * @return int
     */
    public static function getTotalByUser(UserInterface $user, string $entityNamespace, string $interactionName) : int
    {
        $interaction = InteractionsModel::getByName($interactionName);

        return  UsersInteractions::count([
            'conditions' => 'users_id = :userId:  
                            AND interactions_id = :interactionId:  
                            AND entity_namespace = :namespace: 
                            AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'interactionId' => $interaction->getId(),
                'namespace' => $entityNamespace,
            ]
        ]);
    }

    /**
     * Get the total interaction for a specific entity.
     *
     * @param string $entity
     * @param string $interactionName
     *
     * @return int
     */
    public static function getTotalByEntity(ModelInterface $entity, string $interactionName) : int
    {
        $interaction = InteractionsModel::getByName($interactionName);

        return  UsersInteractions::count([
            'conditions' => 'interactions_id = :interactionId:  
                            AND entity_namespace = :namespace: 
                            AND entity_id = :entityId:
                            AND is_deleted = 0',
            'bind' => [
                'interactionId' => $interaction->getId(),
                'namespace' => get_class($entity),
                'entityId' => $entity->getId(),
            ]
        ]);
    }

    /**
     * getEntityByInteractionType.
     *
     * @param  UserInterface $user
     * @param  string $interactionName
     *
     * @return array
     */
    public static function getEntityByInteractionType(UserInterface $user, string $interactionName) : array
    {
        $interaction = InteractionsModel::getByName($interactionName);

        $usersInteractions = UsersInteractions::find([
            'conditions' => 'users_id = :userId:  
                            AND interactions_id = :interactionId:  
                            AND is_deleted = 0',
            'bind' => [
                'userId' => $user->getId(),
                'interactionId' => $interaction->getId()
            ]
        ]);

        $entities = [];
        foreach ($usersInteractions as $userInteraction) {
            $entities[] = $userInteraction->entityData;
        }
        return $entities;
    }

    /**
     * getInteractionByEntity.
     *
     * @param  UserInterface $user
     * @param  ModelInterface $entity
     * @param  string $interactionName
     *
     * @return UsersInteractions
     */
    public static function getInteractionByEntity(UserInterface $user, ModelInterface $entity, string $interactionName) : UsersInteractions
    {
        $interaction = InteractionsModel::getByName($interactionName);

        return UsersInteractions::findFirstOrFail([
            'conditions' => 'entity_namespace = :namespace: 
                            AND users_id = :userId:
                            AND entity_id = :entityId:
                            AND interactions_id = :interactionId:
                            AND is_deleted = 0',

            'bind' => [
                'namespace' => get_class($entity),
                'entityId' => $entity->getId(),
                'interactionId' => $interaction->getId(),
                'userId' => $user->getId(),
            ]
        ]);
    }
}
