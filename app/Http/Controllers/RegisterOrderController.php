<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Libraries\Registration\RegisterApprovalHandler;
use App\Libraries\Values\PhoneValue;
use App\Services\Contracts\RegistrationInterface;
use App\Http\Requests\RegisterOrder\RegisterOrderRequest;
use App\Libraries\Registration\RegisterOrderHandler;
use App\Models\RegisterOrder;
use App\Models\RegisterApproval;
use App\Notifications\RegisterApprovalNotification;
use App\Services\Registration\RegisterApprovalService;
use App\Services\Registration\RegisterOrderService;
use Illuminate\Http\Request;

final class RegisterOrderController extends Controller
{
    public function __construct(
        protected readonly RegistrationInterface $registrationService,
        protected readonly RegisterOrderService $registerOrderService,
        protected readonly RegisterApprovalService $registerApprovalService
    ) {
        $this->registrationService->setHandlers(
            new RegisterOrderHandler($this->registrationService),
            new RegisterApprovalHandler($this->registrationService)
        );
    }

    public function index(Request $request)
    {
        return view('pages.register.orders.index', [
            'list' => $this->registerOrderService->prepareIndex(
                $request
            )
        ]);
    }

    public function create()
    {
        return view('pages.register.orders.create');
    }

    /**
     * Execute the logic from register request form submit.
     */
    public function store(RegisterOrderRequest $request)
    {
        $email = $request->input('email');
        if (!$this->registrationService->existsUserByEmail($email)) {
            $this->registrationService->handleRegister($email, new PhoneValue($request->input('phone')));
        }
        return redirect()->route('register.orders.create')->with([
            'toastShow' => true,
            'toastMsg' => 'Solicitação realizada com sucesso!'
        ]);
    }

    protected function approveOrder(RegisterOrder $order)
    {
        /** @var RegisterApproval $registerApproval */
        $registerApproval = $this->registerApprovalService->create(
            $this->registerOrderService->prepareRegisterApproval($order)
        );
        $registerApproval->notify(new RegisterApprovalNotification);
    }

    public function approve(RegisterOrder $order)
    {
        $this->approveOrder($order);

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedido aprovado com sucesso!'
        ]);
    }

    public function destroy(int $id)
    {
        $this->registerOrderService->removeRegisterOrder($id);

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedido removido com sucesso!'
        ]);
    }

    public function removeGroup(RegisterOrderRequest $request)
    {
        $this->registerOrderService->removeRegisterOrderGroup(
            $request->validated('remotion')
        );

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedidos removidos com sucesso!'
        ]);
    }

    public function approveGroup(RegisterOrderRequest $request)
    {
        $this->registerOrderService->findOrdersToApprove(
            $request->validated('approvement')
        )->each(fn(RegisterOrder $order) => $this->approveOrder($order));

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedidos aprovados com sucesso!'
        ]);
    }
}
