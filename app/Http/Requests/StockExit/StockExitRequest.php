<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\StockExit\Strategies\ExchangePersistence;
use App\Http\Requests\StockExit\Strategies\LossPersistence;
use App\Http\Requests\StockExit\Strategies\Persistence;
use App\Http\Requests\StockExit\Strategies\SalePersistence;
use App\Http\Requests\StockExit\Strategies\DestroyGroup;
use App\Libraries\Enums\StockExitTypeEnum;
use Exception;

final class StockExitRequest extends CustomFormRequest
{
    protected function pickChecker(): Checker
    {
        $url = url()->current();

        switch ($url) {
            case route('losses.group.destroy', [
                'key' => $this->route('key', 'key'),
                'stockExitList' => 'list'
            ]):
            case route('exchanges.group.destroy', [
                'key' => $this->route('key', 'key'),
                'exchangeList' => 'list'
            ]):
                return new DestroyGroup();
            case route('stocks.exits.store'):
                return (new Persistence(
                    $this->input('type')
                ))->pushChecker(
                    StockExitTypeEnum::SALE,
                    new SalePersistence()
                )->pushChecker(
                    StockExitTypeEnum::EXCHANGE,
                    new ExchangePersistence($this)
                )->pushChecker(
                    StockExitTypeEnum::DEMONSTRATION,
                    new LossPersistence()
                )->pushChecker(
                    StockExitTypeEnum::PERSONAL_USE,
                    new LossPersistence()
                )->pushChecker(
                    StockExitTypeEnum::LOSS,
                    new LossPersistence()
                );
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
