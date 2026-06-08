<?php

declare(strict_types=1);

namespace App\Http\Requests\StockExit\Strategies;

use App\Http\Requests\Checker;
use App\Libraries\Enums\StockExitTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

final class Persistence implements Checker
{
    /**
     * @var Collection<string, array{rules: array, messages: array}>
     */
    protected Collection $persistences;

    public function __construct(
        protected ?string $type,
    ) {
        $this->persistences = collect([]);
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::enum(StockExitTypeEnum::class),
            ],
            ...$this->getRulesByType(),
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Requisição inválida',
            'type.enum' => 'Requisição inválida',
            ...$this->getMessagesByType(),
        ];
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
        return $this->persistences->get($this->type ?? '')['rules'] ?? [];
    }

    protected function getMessagesByType()
    {
        return $this->persistences->get($this->type ?? '')['messages'] ?? [];
    }
}
