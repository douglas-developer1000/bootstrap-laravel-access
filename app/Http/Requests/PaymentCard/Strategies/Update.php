<?php

declare(strict_types=1);

namespace App\Http\Requests\PaymentCard\Strategies;

use App\Models\PaymentCard;
use Illuminate\Validation\Rule;
use Override;

final class Update extends Persistence
{
    public function __construct(protected PaymentCard $card)
    {
        parent::__construct();
    }

    #[Override]
    public function rules(): array
    {
        $rules = parent::rules();
        return [
            ...$rules,
            'img' => $this->pickImgRules(
                required: false
            )['img'],
            'flag' => [
                'required',
                "min:{$this->flagMinSize}",
                "max:{$this->flagMaxSize}",
                Rule::unique('payment_cards', 'flag')->ignore($this->card->id ?? 0, 'id')
            ]
        ];
    }
}
