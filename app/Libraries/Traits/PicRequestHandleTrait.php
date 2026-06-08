<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\ProfileService;
use App\Services\Contracts\ImgStoragerInterface;

trait PicRequestHandleTrait
{
    protected function handleImg(Request $request, string $key, string $folderKey, ?Model $model = NULL): string|null
    {
        return (new ProfileService(
            app(ImgStoragerInterface::class, [
                'model' => $model,
                'key' => $key,
                'lastFolderName' => $folderKey
            ])
        ))->storageProfileImg($request);
    }

    protected function attachImgInput(
        array $base,
        Request $request,
        string $folderKey,
        string $modelKey,
        ?Model $model = NULL
    ): array {
        $imgPath = $this->handleImg($request, $modelKey, $folderKey, $model);
        if ($imgPath) {
            return collect($base)->put($modelKey, $imgPath)->all();
        }
        return $base;
    }

    protected function extractFolderKey(Model $model, string $modelKey): string
    {
        $list = collect(
            explode(DIRECTORY_SEPARATOR, $model->$modelKey)
        );
        return $list->offsetGet($list->count() - 2);
    }

    protected function removeStoredImg(string $modelKey, ?Model $model = NULL): bool
    {
        if ($model === NULL || $model->$modelKey === NULL) {
            return false;
        }

        return (new ProfileService(
            app(ImgStoragerInterface::class, [
                'model' => $model,
                'key' => $modelKey,
                'lastFolderName' => $this->extractFolderKey(
                    $model,
                    $modelKey
                )
            ])
        ))->remove();
    }
}
