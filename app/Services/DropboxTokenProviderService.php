<?php

declare(strict_types=1);

namespace App\Services;

use Spatie\Dropbox\TokenProvider;
use GuzzleHttp\Client;

final class DropboxTokenProviderService implements TokenProvider
{

    public function getToken(): string
    {
        return cache()->remember('dropbox_token', 12600, function () {
            $client = new Client();
            $response = $client->post('https://api.dropbox.com/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => config('services.dropbox.refresh_token'),
                    'client_id' => config('services.dropbox.app_key'),
                    'client_secret' => config('services.dropbox.app_secret'),
                ],
            ]);
            return json_decode((string)$response->getBody(), true)['access_token'];
        });
    }
}
