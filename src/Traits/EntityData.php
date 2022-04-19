<?php

declare(strict_types=1);

namespace Kanvas\Social\Traits;

use Phalcon\Mvc\ModelInterface;

trait EntityData
{
    /**
     * retrieveEntityData.
     *
     * @return ModelInterface
     */
    public function retrieveEntityData() : ModelInterface
    {
        return $this->entity_namespace::findFirst($this->entity_id);
    }
}
