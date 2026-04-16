<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class ImageController extends Controller
{
    /**
     * Find resource item inside of application's storage folder
     *
     */
    public function find(string $folder, string $filename)
    {
        $path = storage_path() . "/app/private/$folder/$filename";
        if (!File::exists($path)) {
            abort(404, 'Image not found.');
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header('Content-Type', $type);
        return $response;
    }
}
