<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Libraries\Registration\RegisterApprovalHandler;
use App\Services\Contracts\RegistrationServiceInterface;
use App\Http\Requests\RegisterOrder\RegisterOrderRequest;
use App\Libraries\Registration\RegisterOrderHandler;
use App\Libraries\Utils\Paginator;
use App\Libraries\Utils\TokenBuilder;
use App\Models\RegisterOrder;
use App\Services\Registration\RegisterApprovalService;
use App\Services\Registration\RegisterOrderService;
use Illuminate\Http\Request;

class RegisterOrderController extends Controller
{
    public function __construct(
        protected readonly RegistrationServiceInterface $registrationService,
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
        $group = Paginator::buildGroup($request->only('group'));
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'email']);
        $order = Paginator::buildOrder($request->only('order'));

        $query = RegisterOrder::query();
        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('email', "%{$search}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'email', 'phone', 'created_at']
        );

        return view('pages.register.orders.index', ['list' => $list]);
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
            $this->registrationService->handleRegister($email, $request->input('phone'));
        }
        return redirect()->route('register.orders.create')->with([
            'toastShow' => true,
            'toastMsg' => 'Solicitação realizada com sucesso!'
        ]);
    }

    public function approve(RegisterOrder $order)
    {
        $this->registerOrderService->delete($order->id);
        $token = TokenBuilder::build();
        $fields = [
            'email' => $order->email,
            'token' => $token,
            'expiration_data' => now()->addHours(
                \intval(
                    config('registration.timeout.token')
                )
            )
        ];
        if ($order->phone) {
            $fields['phone'] = $order->phone;
        }
        $this->registerApprovalService->create($fields);
        $this->registrationService->sendApprovalMail($order->email, $token);

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedido aprovado com sucesso!'
        ]);
    }

    public function destroy(RegisterOrder $order)
    {
        $order->delete();

        return redirect()->route(
            'register.orders.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Pedido removido com sucesso!'
        ]);
    }
}
