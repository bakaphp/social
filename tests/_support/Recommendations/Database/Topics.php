<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Recommendations\Database;

use Kanvas\Social\Recommendations\Drivers\Recombee\Database as RecombeeDatabase;

class Topics extends RecombeeDatabase
{
    /**
     * Set database source.
     */
    public function __construct()
    {
        $this->setSource('kanvas-prod');
        $this->setItemsType('topic');
    }
}
