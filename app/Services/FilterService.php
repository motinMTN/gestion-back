<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FilterService
{
    public function applyFilters(Builder $query, Request $request, array $filterableFields): Builder
    {

        // Agrupamos las condiciones en un solo bloque de where
        $query->where(function ($q) use ($request, $filterableFields) {
            foreach ($filterableFields as $field => $operator) {
                // Verifica si el campo existe en el request, no es null y no es una cadena vacía
                $value = $request->get($field);
                if ($request->has($field) && !is_null($value) && $value !== '') {
                    // Log::info("Aplica filtro para: $field con valor: $value");

                    if ($operator === 'like') {
                        $value = '%' . $value . '%';
                    }

                    // Aplicar orWhere para cada filtro
                    $q->orWhere($field, $operator, $value);
                }
            }
        });

        /*$sql = $query->toSql();

        // Obtener los valores que reemplazan a los placeholders
        $bindings = $query->getBindings();

        // Reemplazar los placeholders con los valores
        $sqlWithBindings = Str::replaceArray('?', $bindings, $sql);

        // dd($sqlWithBindings);
        Log::info($sqlWithBindings);*/

        return $query;
    }



    public function applySorting(Builder $query, Request $request, $defaultSortField = 'id', $defaultSortOrder = 'asc'): Builder
    {
        $sortField = $request->input('sort_field', $defaultSortField);
        $sortOrder = $request->input('sort_order', $defaultSortOrder);

        return $query->orderBy($sortField, $sortOrder);
    }

    public function applyPagination(Builder $query, Request $request, $defaultPerPage = 10)
    {
        $perPage = $request->input('per_page', $defaultPerPage);
        $page = $request->input('page', 1);

        $query->skip(($page - 1) * $perPage)->take($perPage);

        return $query->get();
    }

    public function applyHavingFilters(Builder $query, Request $request, array $havingFilters): Builder
    {
        foreach ($havingFilters as $field => $conditions) {
            foreach ($conditions as $operator => $inputKey) {
                if ($request->has($inputKey)) {
                    $query->having($field, $operator, $request->input($inputKey));
                }
            }
        }

        return $query;
    }
}
