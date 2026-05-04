<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegisterApproval\RegisterApprovalRequest;
use Illuminate\Http\Request;
use App\Services\Registration\RegisterApprovalService;

final class RegisterApprovalController extends Controller
{
    public function __construct(protected RegisterApprovalService $svc)
    {
        // ...
    }

    public function index(Request $request)
    {
        return view('pages.register.approvals.index', [
            'list' => $this->svc->prepareIndex($request)
        ]);
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
