<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Utils\Paginator;
use App\Models\RegisterApproval;

class RegisterApprovalController extends Controller
{
    public function index(Request $request)
    {
        $group = Paginator::buildGroup($request->only('group'));
        $search = Paginator::buildSearch($request->only('q'));
        $sort = Paginator::buildSort($request->only('sort'), ['created_at', 'id', 'email']);
        $order = Paginator::buildOrder($request->only('order'));

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

    public function destroy(RegisterApproval $approval)
    {
        $approval->delete();

        return redirect()->route(
            'register.approvals.index',
            request()->query() ?? []
        )->with([
            'toastShow' => true,
            'toastMsg' => 'Aprovação removida com sucesso!'
        ]);
    }
}
