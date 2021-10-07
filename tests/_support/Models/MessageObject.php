<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

use Kanvas\Social\Contracts\Messages\MessageableEntityInterface;
use Kanvas\Social\Contracts\Messages\MessagesInterface;

class MessageObject implements MessagesInterface, MessageableEntityInterface
{
    public int $id = 1;

    public function getId() : int
    {
        return $this->id;
    }
}
