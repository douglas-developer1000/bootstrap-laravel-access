<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\ImgStoragerInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client;
use Spatie\Dropbox\TokenProvider;
use Exception;

class DropboxImgStoragerService implements ImgStoragerInterface
{
    protected Model $model;
    protected string $key;
    protected string $lastFolderName;

    protected $client;

    public function __construct(Model $model, string $key, string $lastFolderName)
    {
        $this->model = $model;
        $this->key = $key;
        $this->lastFolderName = $lastFolderName;

        $this->client = new Client(
            app(TokenProvider::class)
        );
    }

    public function persist(Request $request): ?string
    {
        if (!$request->hasFile($this->key) || !$request->file($this->key)->isValid()) {
            return NULL;
        }
        $parentFolder = $this->getOrCreateFolder("/{$this->lastFolderName}");
        if ($parentFolder === NULL) {
            return NULL;
        }
        try {
            $file = $request->file($this->key);
            $resource = fopen($file->getRealPath(), 'r');

            $extension = $request->file($this->key)->extension();
            $filename = $this->generateRandomFilename($extension);
            $path = $this->buildPath("{$parentFolder}", $filename);
            $this->client->upload($path, $resource);
            if (\is_resource($resource)) {
                fclose($resource);
            }

            $sharedLink = $this->makeSharedLink($path);
            if ($sharedLink === NULL) {
                return NULL;
            }
            return $this->formatDirectUrl($sharedLink);
        } catch (ClientException $exception) {
            Log::warning("DropboxResponseError (uploading file): ");
            Log::warning($exception->getMessage());
        } catch (\Throwable $exception) {
            Log::warning("Error (uploading file): ");
            Log::warning($exception->getMessage());
        }
        return NULL;
    }

    public function remove(): bool
    {
        $key = $this->key;
        $path = $this->model->$key;
        if ($path === NULL) {
            return false;
        }
        $url = $this->revertDirectUrlFormated($path);
        try {
            $metadata = $this->client->rpcEndpointRequest('sharing/get_shared_link_metadata', [
                'url' => $url,
            ]);
            $filename = $metadata['name'] ?? NULL;
            if ($filename === NULL) {
                return false;
            }
            $parentFolder = $this->getOrCreateFolder("/{$this->lastFolderName}");
            if ($parentFolder === NULL) {
                return false;
            }
            $path = $this->buildPath($parentFolder, $filename);
            $this->revokeSharedImage($path);

            try {
                $this->client->delete($path);
                return true;
            } catch (ClientException $exception) {
                Log::warning("DropboxResponseError (deleting file): ");
                Log::warning($exception->getMessage());
            } catch (\Throwable $exception) {
                Log::warning("Error (deleting file): ");
                Log::warning($exception->getMessage());
            }
            return false;
        } catch (ClientException $exception) {
            Log::warning("DropboxResponseError (getting shared link metadata): ");
            Log::warning($exception->getMessage());
        } catch (\Throwable $exception) {
            Log::warning("Error (getting shared link metadata)");
            Log::warning($exception->getMessage());
        }
        return false;
    }

    protected function getOrCreateFolder(string $folderPath): ?string
    {
        try {
            $metadata = $this->client->getMetadata($folderPath);
            // Check if the item found is specifically a folder
            if ($metadata['.tag'] !== 'folder') {
                Log::warning(
                    'DropboxResponseError (creating folder): Invalid Folder -> That\'s a file'
                );
                return NULL;
            }
            // Here: Folder already exists!
        } catch (Exception $e) {
            $this->client->createFolder($folderPath);
            // Here: Folder does not exists!
            // ALTER TABLE public.users ALTER COLUMN photo TYPE VARCHAR(200);
        }
        return $folderPath;
    }

    protected function makeSharedLink(string $filePath): ?string
    {
        try {
            $output = $this->client->createSharedLinkWithSettings($filePath);
            return $output['url'] ?? NULL;
        } catch (ClientException $exception) {
            if ($exception->getCode() === 409) {
                $output = $this->client->listSharedLinks($filePath);

                return $output['links'][0]['url'] ?? NULL;
            } else {
                Log::warning("DropboxResponseError (creating shared link): ");
                Log::warning($exception->getMessage());
            }
        } catch (\Throwable $exception) {
            Log::warning("Error (creating shared link): ");
            Log::warning($exception->getMessage());
        }
        return NULL;
    }

    protected function formatDirectUrl(string $sharedUrl): ?string
    {
        return Str::of($sharedUrl)
            ->replace('www.dropbox.com', 'dl.dropboxusercontent.com')
            ->replace('?dl=0', '?raw=1')->toString();
    }

    protected function revertDirectUrlFormated(string $directSharedUrl): ?string
    {
        return Str::of($directSharedUrl)
            ->replace('dl.dropboxusercontent.com', 'www.dropbox.com')
            ->replace('?raw=1', '?dl=0')->toString();
    }

    /**
     * Revoke the image's shared link
     * used before image remotion
     */
    protected function revokeSharedImage(string $path): bool
    {
        try {
            $links = $this->client->listSharedLinks($path);

            foreach ($links as $link) {
                try {
                    $this->client->rpcEndpointRequest('sharing/revoke_shared_link', [
                        'url' => $link['url'],
                    ]);
                } catch (ClientException $exception) {
                    Log::warning("DropboxResponseError (revoking shared link): ");
                    Log::warning($exception->getMessage());
                } catch (\Throwable $exception) {
                    Log::warning("Error (revoking shared Link): ");
                    Log::warning($exception->getMessage());
                }
                return false;
            }
            return true;
        } catch (ClientException $exception) {
            Log::warning("DropboxResponseError (listing shared links): ");
            Log::warning($exception->getMessage());
        } catch (\Throwable $exception) {
            Log::warning("Error (listing shared links): ");
            Log::warning($exception->getMessage());
        }
        return false;
    }

    protected function generateRandomFilename($extension = 'png', $length = 16)
    {
        // Generate secure random bytes and convert to hex
        // Note: bin2hex doubles the length of bytes provided
        $randomString = bin2hex(random_bytes($length / 2));
        return implode('.', [$randomString, $extension]);
    }

    protected function buildPath(string ...$list): string
    {
        return implode(DIRECTORY_SEPARATOR, $list);
    }
}
