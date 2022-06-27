<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

use Kanvas\Social\Contracts\Follows\FollowableInterface;
use Kanvas\Social\Contracts\Follows\FollowersTrait;
use Kanvas\Social\Contracts\Interactions\TotalInteractionsTrait;

class Tag extends BaseModel implements FollowableInterface
{
    use FollowersTrait;
    use TotalInteractionsTrait;

    public int $id = 1;

    public function getId() : int
    {
        return $this->id;
    }

    /**
     * initialize.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->setSource('tags');
    }
}
