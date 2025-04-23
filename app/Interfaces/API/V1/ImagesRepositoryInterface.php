<?php

namespace App\Interfaces\API\V1;

use Illuminate\Database\Eloquent\Collection;

interface ImagesRepositoryInterface
{
    public function storeImagesByModelAndObject(string $modelClass, int $objectId, array $filesData, string $uploadPath): array;
    public function getByModelAndObject(string $modelClass): Collection;
    public function deleteByModelAndObject(string $modelClass, int $objectId): bool;
}
