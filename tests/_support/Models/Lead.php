<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

use Kanvas\Social\Contracts\Channels\ChannelsInterface;
use Kanvas\Social\Contracts\Channels\ChannelsTrait;

class Lead extends BaseModel implements ChannelsInterface
{
    use ChannelsTrait;

    public int $id = 1;

    public function getId() : int
    {
        return $this->id;
    }
}
