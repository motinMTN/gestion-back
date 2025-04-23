<?php

namespace App\Repositories\API\V1;

use App\Helpers\LogHelper;
use App\Interfaces\API\V1\ImagesRepositoryInterface;
use App\Models\Images;
use App\Services\AzureBlobStorageService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ImagesRepository implements ImagesRepositoryInterface
{
    protected $storageService;

    public function __construct(AzureBlobStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function storeImagesByModelAndObject(string $modelClass, int $objectId, array $filesData, string $uploadPath): array
    {
        $paths = [];

        return LogHelper::executeWithLogging(function() use ($modelClass,$objectId,$filesData,$uploadPath){
            $path = "/TocDoc/images/" . $uploadPath;

            foreach ($filesData as $file)
            {
                $url = $this->storageService->upload($file, $path);

                Images::create([
                    'model_id' => $objectId,
                    'model_type' => $modelClass,
                    'path' => $url,
                ]);

                $paths[] = $url;
            }
            // Log::info([$paths]);
            return $paths;
        }, "storeImagesByModelAndObject","ImagesRepository", function () use (&$paths) {
            // En caso de error, llamamos al servicio para eliminar las imágenes subidas
            $this->storageService->deleteMultiple($paths);
        });
    }

    public function getByModelAndObject($model) : Collection
    {
        return LogHelper::executeWithLogging(function() use ($model){
            return Images::where('model_id', $model->id)
            ->where('model_type', get_class($model))
            ->get();
        }, "getByModelAndObject","ImagesRepository");
    }

    public function deleteByModelAndObject($model, $image_id) : bool
    {
        return LogHelper::executeWithLogging(function() use ($model, $image_id){
            $image = Images::where([
                        ['model_id', $model->id],
                        ['model_type', get_class($model)],
                        ['id', $image_id]
                    ])->firstOrFail();

            // Eliminar la imagen del almacenamiento
            $this->storageService->delete($image->path);

            // Eliminar el registro de la base de datos
            return $image->delete();
        }, "deleteByModelAndObject","ImagesRepository");

    }
}
