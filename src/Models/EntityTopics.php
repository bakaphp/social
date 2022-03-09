<?php

namespace Kanvas\Social\Models;

class EntityTopics extends BaseModel
{
    public int $entity_id;
    public int $apps_id;
    public string $entity_namespace;
    public int $companies_id;
    public int $topics_id;
    public int $users_id;


    /**
     * initialize.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('entity_topics');
        $this->hasMany('topics_id', Topics::class, 'id', ['alias' => 'topics']);
    }
}
