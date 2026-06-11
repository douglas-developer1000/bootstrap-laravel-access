<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit;

use App\Http\Requests\Checker;
use App\Http\Requests\CustomFormRequest;
use App\Http\Requests\StockExit\Strategies\ExchangePersistence;
use App\Http\Requests\StockExit\Strategies\StockExitPersistence;
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
            case route('raw.exits.group.destroy', [
                'key' => $this->route('key', 'key'),
                'stockExitList' => 'list'
            ]):
            case route('garbages.group.destroy', [
                'key' => $this->route('key', 'key'),
                'stockExitList' => 'list'
            ]):
            case route('exchanges.group.destroy', [
                'key' => $this->route('key', 'key'),
                'exchangeList' => 'list'
            ]):
                return new DestroyGroup();
            case route('stocks.exits.store', $this->route('exitType', 0)):
                return (new Persistence(
                    $this->route('exitType')
                ))->pushChecker(
                    StockExitTypeEnum::SALE,
                    new SalePersistence()
                )->pushChecker(
                    StockExitTypeEnum::EXCHANGE,
                    new ExchangePersistence($this)
                )->pushChecker(
                    StockExitTypeEnum::DEMONSTRATION,
                    new StockExitPersistence()
                )->pushChecker(
                    StockExitTypeEnum::PERSONAL_USE,
                    new StockExitPersistence()
                )->pushChecker(
                    StockExitTypeEnum::LOSS,
                    new StockExitPersistence()
                )->pushChecker(
                    StockExitTypeEnum::RAW,
                    new StockExitPersistence()
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
