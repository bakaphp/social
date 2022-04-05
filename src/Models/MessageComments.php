<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

use Canvas\Contracts\EventManagerAwareTrait;
use Kanvas\Social\Comments\Models\Messages;

/**
 * @deprecated v0.4
 */
class MessageComments extends Messages
{
    use EventManagerAwareTrait;
}
