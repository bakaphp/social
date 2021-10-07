<?php

declare(strict_types=1);

namespace Kanvas\Social\Contracts\Users;

/**
 * @deprecated v0.4
 */
interface UserInterface
{
    public function getDefaultCompany();

    public function getId();
}
