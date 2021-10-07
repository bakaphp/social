<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

class MessageTags extends BaseModel
{
    public $id;
    public int $message_id;
    public int $tags_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('message_tags');
    }
}
