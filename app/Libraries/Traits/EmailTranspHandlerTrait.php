<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

trait EmailTranspHandlerTrait
{
    public function handleEmailTransport(Closure $callback): void
    {
        try {
            $callback();
        } catch (TransportException $tr) {
            $msg = Str::of("Exceção de email lançada.\n")->append(
                'Mensagem: '
            )->append($tr->getMessage())->toString();

            Log::channel('slack')->info($msg);
        } catch (Throwable $th) {
            throw $th;
        }
    }
}
