<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

use Canvas\Models\Users as ModelsUsers;
use Kanvas\Social\Contracts\Follows\FollowableInterface;
use Kanvas\Social\Contracts\Follows\FollowersTrait;
use Kanvas\Social\Contracts\Interactions\UsersInteractionsTrait;

class Users extends ModelsUsers implements FollowableInterface
{
    use FollowersTrait;
    use UsersInteractionsTrait;
}
