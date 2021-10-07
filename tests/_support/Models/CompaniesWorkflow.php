<?php
declare(strict_types=1);

namespace Kanvas\Social\Test\Support\Models;

use Canvas\Models\Companies as KanvasCompanies;
use Kanvas\Social\WorkflowsRules\Contracts\WorkflowsEntityInterfaces;
use Kanvas\Social\WorkflowsRules\Traits\CanUseRules;

class CompaniesWorkflow extends KanvasCompanies implements WorkflowsEntityInterfaces
{
    use CanUseRules;
}
