<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

class Companies extends BaseModel
{
    public string $language = 'EN';

    public int $id = 1;

    public function getId() : int
    {
        return $this->id;
    }
}
