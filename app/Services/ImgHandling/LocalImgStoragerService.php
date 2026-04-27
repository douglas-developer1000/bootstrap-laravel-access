<?php

namespace App\Services\ImgHandling;

use App\Services\Abstracts\AbstractImgConverter;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LocalImgStoragerService extends AbstractImgConverter
{
    protected Model $model;
    protected string $lastFolderName;
    public function __construct(Model $model, string $key, string $lastFolderName)
    {
        parent::__construct($key);

        $this->model = $model;
        $this->lastFolderName = $lastFolderName;
    }

    public function persist(Request $request): ?string
    {
        if (!$request->hasFile($this->key) || !$request->file($this->key)->isValid()) {
            return NULL;
        }
        $parentFolder = $this->getOrCreateFolder($this->buildPath('private', $this->lastFolderName));
        if ($parentFolder === NULL) {
            return NULL;
        }
        try {
            $sourceStream = $this->processImg($request);
            if ($sourceStream === false) {
                return NULL;
            }
            // '{$this->folderName}/newFilename.ext'
            $endFilepath = $this->buildPath(
                $this->lastFolderName,
                $this->generateRandomFilename('webp')
            );
            // '/{storage_fullpath}/app/private/{$this->folderName}/newFilename.ext'
            $destPath = $this->makeStoragePath(
                true,
                'private',
                $endFilepath
            );
            $destStream = fopen($destPath, 'w');
            if ($sourceStream && $destStream) {
                stream_copy_to_stream($sourceStream, $destStream);
                fclose($sourceStream);
                fclose($destStream);
            }
            // Output: '/storage/app/{$this->folderName}/newFilename.ext'
            return $this->buildPath('', $this->makeStoragePath(false, $endFilepath));
        } catch (\Throwable $th) {
            Log::warning("Error (uploading file): ");
            Log::warning($th->getMessage());
        }
        return NULL;
    }

    protected function getOrCreateFolder(string $folderPath): ?string
    {
        $path = $this->makeStoragePath(true, $folderPath);
        try {
            if (!file_exists($path)) {
                mkdir(directory: $path, permissions: 0755, recursive: true);
            } else if (!is_dir($path)) {
                throw new \Exception("DirectoryErrot: path must me a folder", 1);
            }
            return $path;
        } catch (\Throwable $th) {
            Log::warning("Error (creating folder): ");
            Log::warning($th->getMessage());
        }
        return NULL;
    }

    public function remove(): bool
    {
        $key = $this->key;
        // $filepath: '/storage/app/{$this->key}/newFilename.ext'
        $filepath = $this->model->$key;
        if ($filepath) {
            $pathPhotoRecent = $this->makeStoragePath(
                true,
                'private',
                Str::of($filepath)->remove(
                    // Removed: '/storage/app'
                    $this->buildPath('', 'storage', 'app')
                )->toString()
            );
            // $pathPhotoRecent: '/{storage_fullpath}/app/private/{$this->key}/newFilename.ext'
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
}
