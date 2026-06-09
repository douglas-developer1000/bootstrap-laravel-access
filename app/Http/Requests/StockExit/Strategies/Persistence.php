<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Support\Collection;

final class Persistence implements Checker
{
    /**
     * @var Collection<string, array{rules: array, messages: array}>
     */
    protected Collection $persistences;

    public function __construct(protected StockExitTypeEnum $type)
    {
        $this->persistences = collect([]);
    }

    public function rules(): array
    {
        return $this->getRulesByType();
    }

    public function messages(): array
    {
        return $this->getMessagesByType();
    }

    public function pushChecker(StockExitTypeEnum $enum, Checker $checker): Persistence
    {
        $this->persistences->offsetSet($enum->value, [
            'rules' => $checker->rules(),
            'messages' => $checker->messages()
        ]);
        return $this;
    }

    protected function getRulesByType(): array
    {
        return $this->persistences->get($this->type->value ?? '')['rules'] ?? [];
    }

    protected function getMessagesByType()
    {
        return $this->persistences->get($this->type->value ?? '')['messages'] ?? [];
    }
}
