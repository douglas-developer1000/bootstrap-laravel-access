<?php

namespace App\Services;

use App\Services\Contracts\ImgStoragerServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LocalImgStoragerService implements ImgStoragerServiceInterface
{
    protected Model $model;
    protected string $key;
    protected string $lastFolderName;

    public function __construct(Model $model, string $key, string $lastFolderName)
    {
        $this->model = $model;
        $this->key = $key;
        $this->lastFolderName = $lastFolderName;
    }

    public function persist(Request $request): ?string
    {
        if ($request->hasFile($this->key) && $request->file($this->key)->isValid()) {
            $key = $this->key;
            // By default, upload files will be saved
            // into 'storage/app/private/{$this->folderName}/'
            $pathPhotoNew = $request->$key->store($this->lastFolderName);
            // Output: '/storage/app/{$this->folderName}/newFilename.ext'
            return $this->buildPath('', $this->makeStoragePath(false, $pathPhotoNew));
        }
        return NULL;
    }

    // public function getOrCreateFolder(): ?string
    // {
    //     $folderPath = implode(
    //         DIRECTORY_SEPARATOR,
    //         [storage_path(), 'app', $this->lastFolderName]
    //     );
    //     try {
    //         if (!is_dir($folderPath)) {
    //             mkdir(directory: $folderPath, permissions: 0644, recursive: true);
    //         }
    //     } catch (\Throwable $th) {
    //         return null;
    //     }
    //     return $folderPath;
    // }

    public function remove(): bool
    {
        $key = $this->key;
        $filepath = $this->model->$key;
        if ($filepath) {
            $pathPhotoRecent = $this->makeStoragePath(
                true,
                'private',
                Str::of($filepath)->remove(
                    $this->buildPath('', 'storage', 'app')
                )->toString()
            );
            if (File::exists($pathPhotoRecent)) {
                File::delete($pathPhotoRecent);
                return TRUE;
            }
        }
        return FALSE;
    }

    protected function makeStoragePath(bool $absolute, string ...$remain)
    {
        $base = $absolute ? storage_path() : 'storage';
        $list = [$base, 'app'];

        if ($remain !== NULL) {
            $list = [...$list, ...$remain];
        }
        return $this->buildPath(...$list);;
    }

    protected function buildPath(string ...$list): string
    {
        return implode(DIRECTORY_SEPARATOR, $list);
    }
}
