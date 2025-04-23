<?php

namespace App\Interfaces\API\V1;

use Illuminate\Database\Eloquent\Builder;

interface ActivityLogsRepositoryInterface
{
    public function builderActivityLogs(Builder $query, int $modelId, string $stringModel): Builder;
}
