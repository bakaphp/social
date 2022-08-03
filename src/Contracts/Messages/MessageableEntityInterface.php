<?php

declare(strict_types=1);

namespace Kanvas\Social\Contracts\Messages;

use Baka\Contracts\Database\ModelInterface;

interface MessageableEntityInterface extends ModelInterface
{
    public function getId();
}
