<?php

namespace Kanvas\Social\Traits\Topics;

use Canvas\Models\Companies;
use Kanvas\Social\Models\EntityTopics;
use Kanvas\Social\Models\Topics;
use Phalcon\Di;

trait HasTopic
{
    /**
     * addTopic.
     *
     * @param  string|object $topic
     *
     * @return void
     */
    public function addTopic($topic) : void
    {
        $this->addSingleTopic($topic);
    }

    /**
     * addTopics.
     *
     * @param  array $topics
     *
     * @return void
     */
    public function addTopics(array $topics) : void
    {
        foreach ($topics as $topic) {
            $this->addSingleTopic($topic);
        }
    }


    /**
     * addSingleTopic.
     *
     * @param  string|object $topic
     *
     * @return void
     */
    protected function addSingleTopic($topic) : void
    {
        $topic = $this->searchTopic($topic);
        EntityTopics::findFirstOrCreate(
            [
                'conditions' => 'entity_id = :entity_id: AND entity_namespace = :entity_namespace: AND topics_id = :topics_id: AND apps_id = :apps_id: AND companies_id = :companies_id: AND users_id = :users_id:',
                'bind' => [
                    'entity_id' => $this->id,
                    'entity_namespace' => get_class($this),
                    'topics_id' => $topic->id,
                    'apps_id' => Di::getDefault()->get('app')->getId(),
                    'companies_id' => Di::getDefault()->get('userData')->getDefaultCompany()->getId(),
                    'users_id' => Di::getDefault()->get('userData')->getId(),
                ]
            ],
            [
                'entity_id' => $this->id,
                'entity_namespace' => get_class($this),
                'topics_id' => $topic->id,
                'apps_id' => Di::getDefault()->get('app')->getId(),
                'companies_id' => Di::getDefault()->get('userData')->getDefaultCompany()->getId(),
                'users_id' => Di::getDefault()->get('userData')->getId(),
            ]
        );
    }

    /**
     * getTopics.
     *
     * @return
     */
    public function getTopics()
    {
        $this->hasManyToMany(
            'id',
            EntityTopics::class,
            'entity_id',
            'topics_id',
            Topics::class,
            'id',
            [
                'alias' => 'topics',
                'params' => [
                    'conditions' => 'entity_namespace = :entity_namespace:',
                    'bind' => [
                        'entity_namespace' => get_class($this),
                    ]
                ]
            ]
        );
        return $this->topics;
    }


    /**
     * sync.
     *
     * @param  mixed $topics
     *
     * @return void
     */
    public function sync($topics)
    {
        $this->removeTopics();
        $this->addTopics($topics);
    }

    /**
     * removeTopics.
     *
     * @return void
     */
    public function removeTopics()
    {
        $entities = EntityTopics::find([
            'conditions' => 'entity_id = :entity_id: AND entity_namespace = :entity_namespace:',
            'bind' => [
                'entity_id' => $this->id,
                'entity_namespace' => get_class($this),
            ]
        ]);
        foreach ($entities as $entity) {
            $entity->delete();
        }
    }

    /**
     * removeTopic.
     *
     * @param  string|Topic $topic
     *
     * @return void
     */
    public function removeTopic($topic)
    {
        $topic = $this->searchTopic($topic);
        $topic->delete();
    }


    /**
     * searchTopic.
     *
     * @param  string|Topics $topic
     *
     * @return Topics
     */
    protected function searchTopic($topic) : Topics
    {
        if (is_string($topic)) {
            $topic = $this->getTopicByName($topic);

            $topic = Topics::find([
                'conditions' => 'name = :name: AND apps_id = :apps_id: AND companies_id IN (:companies_id:, :global_companies_id:)',
                'bind' => [
                    'name' => $topic,
                    'apps_id' => Di::getDefault()->get('app')->getId(),
                    'companies_id' => Di::getDefault()->get('userData')->getDefaultCompany()->getId(),
                    'global_companies_id' => Companies::GLOBAL_COMPANIES_ID,
                ]
            ]);
        }

        if (!$topic instanceof Topics) {
            throw new \Exception('Topic must be a string or an instance of Kanvas\Social\Models\Topics');
        }

        return $topic;
    }
}
