<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Libraries\Enums\LicenseStatusEnum;
use App\Models\License;
use App\Services\LicenseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class LicenseController extends Controller
{
    public function __construct(protected LicenseService $svc)
    {
        // ...
    }

    public function index(Request $request): View
    {
        return view('pages.licenses.index', [
            'title' => 'Licenças',
            'list' => $this->svc->prepareIndex($request),
            'models' => fn (LengthAwarePaginator $pagination) => (
                $this->svc->hydrateLicense($pagination->all())
            ),
            'parseStatus' => fn (LicenseStatusEnum $status) => (
                Str::of($status->toString())->ucfirst()
            ),
            'checkboxesData' => $this->svc->defineLicenseStatusFilter(),
            'qs' => collect($request->query->all()),

            'licensableRoute' => $this->svc->defineLicensableRoute(...),
        ]);
    }

    public function show(License $license): View
    {
        return view('pages.licenses.show', [
            'license' => $license->load(['plan', 'licensable']),
            'finalPrice' => $license->invoices()->sum('amount'),
            'licensableRoute' => $this->svc->defineLicensableRoute(...),
        ]);
    }

    public function cancel(License $license): RedirectResponse
    {
        $this->svc->cancelLicense($license);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Licença cancelada com sucesso!',
        ]);
    }

    public function activate(License $license): RedirectResponse
    {
        $this->svc->activateLicense($license);

        return redirect()->back()->with([
            'toastShow' => true,
            'toastMsg' => 'Licença ativada com sucesso!',
        ]);
    }
}
