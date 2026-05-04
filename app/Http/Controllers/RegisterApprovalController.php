<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegisterApproval\RegisterApprovalRequest;
use Illuminate\Http\Request;
use App\Models\RegisterApproval;
use App\Services\PaginatorService;
use App\Services\Registration\RegisterApprovalService;

final class RegisterApprovalController extends Controller
{
    public function __construct(protected RegisterApprovalService $svc)
    {
        // ...
    }

    public function index(Request $request, PaginatorService $paginator)
    {
        $group = $paginator->buildGroup($request->only('group'));
        $search = $paginator->buildSearch($request->only('q'));
        $sort = $paginator->buildSort($request->only('sort'), ['created_at', 'id', 'email']);
        $order = $paginator->buildOrder($request->only('order'));

        $query = RegisterApproval::query();
        if ($search) {
            $search = addcslashes($search, '%_');
            $query = $query->whereLike('email', "%{$search}%");
        }
        $list = $query->orderBy($sort, $order)->paginate(
            perPage: $group,
            columns: ['id', 'email', 'phone', 'created_at']
        );

        return view('pages.register.approvals.index', ['list' => $list]);
    }

    public function destroy(int $id)
    {
        $this->svc->removeRegisterApproval($id);

        return redirect()->route(
            'register.approvals.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Aprovação removida com sucesso!'
        ]);
    }

    public function removeGroup(RegisterApprovalRequest $request)
    {
        $this->svc->removeRegisterApprovalGroup($request->validated('remotion'));

        return redirect()->route(
            'register.approvals.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Aprovações removidas com sucesso!'
        ]);
    }
}
