<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Discount\DiscountRequest;
use App\Libraries\Enums\DiscountTypeEnum;
use App\Models\Discount;
use App\Models\User;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class DiscountController extends Controller
{
    protected User $user;

    public function __construct(protected DiscountService $svc)
    {
        /** @var User $user */
        $this->user = Auth::user();
    }

    public function index(Request $request)
    {
        return view('pages.discounts.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydrateDiscount($pagination->all())
            ),
            'parseDiscountEnum' => fn(string $type, float|int $value) => (
                DiscountTypeEnum::parseDiscountValue($type, $value)
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function create()
    {
        return view('pages.discounts.create', [
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function edit(Discount $discount)
    {
        return view('pages.discounts.edit', [
            'discount' => $discount,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function store(DiscountRequest $request)
    {
        $this->svc->createDiscount(
            $this->svc->extractDiscountParams($request)
        );

        return redirect()->route('discounts.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Desconto criado com sucesso!'
        ]);
    }

    public function update(DiscountRequest $request, Discount $discount)
    {
        $this->svc->updateDiscount(
            $this->svc->extractDiscountParams($request),
            $discount,
        );

        return redirect()->route('discounts.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Desconto atualizado com sucesso!'
        ]);
    }

    public function destroy(Discount $discount)
    {
        $this->svc->removeDiscount($discount);

        return redirect()->route('discounts.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Desconto removido com sucesso!'
        ]);
    }

    /**
     * @param Discount[] $discountList
     */
    public function removeGroup(DiscountRequest $request, string $key, array $discountList)
    {
        $this->svc->removeDiscountList($discountList);

        return redirect()->route('discounts.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Descontos removidos com sucesso!'
        ]);
    }

    public function restore(Discount $discountDeleted)
    {
        $this->svc->restoreDiscount($discountDeleted);

        return redirect()->route('discounts.index', [
            'trashed' => '1'
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Desconto restaurado com sucesso!'
        ]);
    }

    /**
     * Restore a soft-deleted product list
     *
     * @param Discount[] $discountList
     */
    public function restoreGroup(DiscountRequest $request, string $key, array $discountList)
    {
        $this->svc->restoreGroup($discountList);

        return redirect()->route('discounts.index', [
            'trashed' => '1'
        ])->with([
            'toastShow' => true,
            'toastMsg' => 'Descontos restaurados com sucesso!'
        ]);
    }

    public function flushPersistence()
    {
        $now = now();
        collect([
            10,
            15,
            20,
            25,
            30,
            35,
            40,
            50
        ])->map(fn(int $val) => [
            'type' => DiscountTypeEnum::PERCENTAGE->value,
            'value' => $val,
            'native' => 1,
            'user_id' => $this->user->id,
            'created_at' => $now,
        ])->each(function (array $data) {
            if (
                !Discount::where([
                    'type' => $data['type'],
                    'value' => $data['value'],
                    'native' => $data['native'],
                ])->exists()
            ) {
                Discount::insert($data);
            }
        });

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Descontos atualizados com sucesso!'
        ]);
    }
}
