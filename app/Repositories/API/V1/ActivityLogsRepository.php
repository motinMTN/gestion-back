<?php

namespace App\Repositories\API\V1;

use App\Helpers\LogHelper;
use App\Interfaces\API\V1\ActivityLogsRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ActivityLogsRepository implements ActivityLogsRepositoryInterface
{
    /**
     * @param Builder $query
     * @param int $modelId
     * @param string $stringModel
     * @return Builder

     */
    public function builderActivityLogs(Builder $query, int $modelId, string $stringModel): Builder
    {
        return LogHelper::executeWithLogging(function () use ($query, $modelId, $stringModel) {
            return
            $query->leftJoin('activity_logs', function ($join) use ($modelId, $stringModel) {
                $join->on('activity_logs.object_id', '=', "$stringModel.id")
                ->where('activity_logs.model_id', '=', $modelId);
            })
            ->selectRaw("$stringModel.*, MAX(activity_logs.activity_date) as last_updated")
            ->groupBy([
                "$stringModel.id",
                'locations.street',
                'locations.indoor_number',
                'locations.outdoor_number',
                'countries.name',
                'states.name',
                'municipalities.name',
                'cities.name',
                'postal_codes.code',
                'neighborhoods.name'
            ]);
        }, "builderActivityLogs", "ActivityLogsRepository");
    }
}
