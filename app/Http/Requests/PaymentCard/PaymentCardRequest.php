<?php

declare(strict_types=1);

namespace App\Http\Requests\PaymentCard;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\PaymentCard\Strategies\Persistence;
use App\Http\Requests\PaymentCard\Strategies\Update;
use App\Http\Requests\PaymentCard\Strategies\DestroyGroup;
use App\Http\Requests\PaymentCard\Strategies\RestoreGroup;
use Closure;
use Exception;

final class PaymentCardRequest extends CustomFormRequest
{

    protected function pickChecker(): Checker
    {
        $url = url()->current();
        switch ($url) {
            case route('payment-cards.store'):
                return new Persistence();
            case route('payment-cards.update', $this->route('card', 0)):
                return new Update($this->route('card', 0));
            case route('payment-cards.group.destroy', [
                'key' => $this->route('key', 'key'),
                'paymentCardList' => 'list'
            ]):
                return new DestroyGroup();
            case route('payment-cards.group.restore', [
                'key' => $this->route('key', 'key'),
                'paymentCardList' => 'trashed'
            ]):
                return new RestoreGroup();
            default:
                throw new Exception("Method Not Implemented", 1);
        }
    }

    public function rules(): array
    {
        return $this->pickChecker()->rules();
    }

    public function messages(): array
    {
        return $this->pickChecker()->messages();
    }
}
