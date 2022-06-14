<?php

declare(strict_types=1);

namespace Kanvas\Social\Contracts\Users;

use Baka\Contracts\Auth\UserInterface as AuthUserInterface;

/**
 * @deprecated v0.4
 */
interface UserInterface extends AuthUserInterface
{
    public function getDefaultCompany();

    public function getId();
}