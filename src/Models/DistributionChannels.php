<?php
declare(strict_types=1);

namespace Kanvas\Social\Models;

class DistributionChannels extends BaseModel
{
    public $id;
    public string $channel;
    public string $queues;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('distribution_channels');
    }
}
