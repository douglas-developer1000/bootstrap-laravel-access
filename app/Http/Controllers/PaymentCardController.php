<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCard\PaymentCardRequest;
use App\Models\PaymentCard;
use App\Models\User;
use App\Services\PaymentCardService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class PaymentCardController extends Controller
{
    protected User $user;
    protected bool $isSuperAdmin;
    public function __construct(protected PaymentCardService $svc)
    {
        /** @var User $user */
        $this->user = Auth::user();
        $this->isSuperAdmin = $this->user->hasRole('super-admin');
    }

    public function index(Request $request)
    {
        return view('pages.payment-cards.index', [
            'list' => $this->svc->prepareIndex($request),

            'models' => fn(LengthAwarePaginator $pagination) => (
                $this->svc->hydratePaymentCard($pagination->all())
            ),
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function show(PaymentCard $card)
    {
        return view('pages.payment-cards.show', [
            'card' => $card,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function edit(PaymentCard $card)
    {
        return view('pages.payment-cards.edit', [
            'card' => $card,
            'isSuperAdmin' => $this->isSuperAdmin,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function destroy(PaymentCard $card)
    {
        $this->svc->removePaymentCard($card);

        return redirect()->route('payment-cards.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cartão removido com sucesso!'
        ]);
    }

    public function update(PaymentCardRequest $request, PaymentCard $card)
    {
        $this->svc->updatePaymentCard(
            $this->svc->extractPaymentCardParams($request),
            $card,
        );

        return redirect()->route('payment-cards.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cartão editado com sucesso!'
        ]);
    }

    public function create()
    {
        return view('pages.payment-cards.create', [
            'isSuperAdmin' => $this->isSuperAdmin,
            'hasAccess' => $this->user->can(...),
        ]);
    }

    public function store(PaymentCardRequest $request)
    {
        $this->svc->createPaymentCard(
            $this->svc->extractPaymentCardParams($request)
        );

        return redirect()->route('payment-cards.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cartão criado com sucesso!'
        ]);
    }

    public function removeGroup(PaymentCardRequest $request, string $key, array $paymentCardList)
    {
        $this->svc->removePaymentCardGroup($paymentCardList);

        return redirect()->route('payment-cards.index')->with([
            'toastShow' => true,
            'toastMsg' => 'Cartões removidos com sucesso!'
        ]);
    }

    public function restore(PaymentCard $payCardDeleted)
    {
        $this->svc->restorePaymentCard($payCardDeleted);

        return redirect()->route(
            'payment-cards.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cartão restaurado com sucesso!'
        ]);
    }

    public function restoreGroup(PaymentCardRequest $request, string $key, array $paymentCardList)
    {
        $this->svc->restorePaymentCardGroup($paymentCardList);

        return redirect()->route(
            'payment-cards.index',
            ['trashed' => 1]
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Cartões restaurados com sucesso!'
        ]);
    }
}
