<?php

declare(strict_types=1);

namespace Kanvas\Social\Contracts\Feeds;

use Baka\Contracts\Controllers\ProcessOutputMapperElasticTrait;
use Baka\Contracts\Http\Api\CrudElasticBehaviorTrait;
use Baka\Contracts\Http\Api\ResponseTrait;
use Kanvas\Social\Dto\Messages;
use Kanvas\Social\ElasticDocuments\Messages as ElasticDocumentsMessages;
use Kanvas\Social\Mappers\Messages as MappersMessages;

/**
 * Channels Trait.
 */
trait ChannelsTrait
{
    use ProcessOutputMapperElasticTrait, CrudElasticBehaviorTrait{
        ProcessOutputMapperElasticTrait::processOutput insteadof CrudElasticBehaviorTrait;
    }
    use ResponseTrait;

    /**
     * set objects.
     *
     * @return void
     */
    public function onConstruct() : void
    {
        $this->model = new ElasticDocumentsMessages();
        $this->dto = Messages::class;
        $this->dtoMapper = new MappersMessages();

        $this->additionalSearchFields = [
            $this->di->has('userData') ? ['companies_id', ':',  $this->userData->getDefaultCompany()->getId()] : ['companies_id', '!',   0],
            ['is_deleted', ':', 0],
        ];
    }
}
