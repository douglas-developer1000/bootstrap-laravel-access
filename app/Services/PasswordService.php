<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;
use \Closure;

final class PasswordService
{
    /**
     * Define the output according to status
     *
     * @see Illuminate\Support\Facades\Password
     *
     * @return array{ok:bool, message:string}
     */
    protected function defineOutput(string $status, string $successKey)
    {
        return match ($status) {
            Password::INVALID_USER => [
                'ok' => FALSE,
                'message' => 'Endereço de e-mail inválido!'
            ],
            Password::INVALID_TOKEN => [
                'ok' => FALSE,
                'message' => 'Token de redefinição de senha inválido!'
            ],
            Password::RESET_THROTTLED => [
                'ok' => FALSE,
                'message' => 'Aguarde antes de tentar novamente!'
            ],
            PasswordBroker::PASSWORD_RESET => [
                'ok' => $successKey === PasswordBroker::PASSWORD_RESET,
                'message' => 'Sua senha foi alterada com sucesso!'
            ],
            Password::RESET_LINK_SENT => [
                'ok' => $successKey === PasswordBroker::RESET_LINK_SENT,
                'message' => 'Enviamos por e-mail seu link de redefinição de senha!'
            ]
        };
    }

    public function sendResetLink(array $inputs): array
    {
        $status = Password::sendResetLink($inputs);
        return $this->defineOutput($status, Password::RESET_LINK_SENT);
    }

    public function reset(array $credentials, Closure $callback): array
    {
        $status = Password::reset(
            $credentials,
            $callback
        );
        return $this->defineOutput($status, PasswordBroker::PASSWORD_RESET);
    }
}
