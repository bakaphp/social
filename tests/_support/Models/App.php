<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

class App extends BaseModel
{
    public int $id = 1;

    public function getId() : int
    {
        return $this->id;
    }
}
