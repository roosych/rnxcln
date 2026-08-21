<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeadStatusRequest;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = $this->filtered($request)
            ->paginate(25)
            ->withQueryString();

        return view('admin.leads.index', compact('leads'));
    }

    public function update(LeadStatusRequest $request, Lead $lead): RedirectResponse
    {
        $lead->update($request->validated());

        return back()->with('status', 'Lead updated.');
    }

    /** Same filters as index() — export downloads exactly what's on screen, not every lead. */
    private function filtered(Request $request)
    {
        return Lead::query()
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('phone', 'like', $term));
            })
            ->latest();
    }

    public function export(Request $request): Response
    {
        $leads = $this->filtered($request)->get();

        $csv = "Date,Source,Status,Name,Phone,Service,ZIP,Preferred date,Message\n";
        foreach ($leads as $lead) {
            $csv .= collect([
                $lead->created_at->format('Y-m-d H:i'),
                $lead->source,
                $lead->status,
                $lead->name,
                $lead->phone,
                $lead->service,
                $lead->zip,
                $lead->preferred_date,
                $lead->message,
            ])->map(fn ($v) => '"'.str_replace('"', '""', $this->escapeCsvFormula((string) $v)).'"')->implode(',')."\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads.csv"',
        ]);
    }

    /**
     * Every field here comes from an anonymous public form submission. A
     * value starting with =, +, -, @ or a tab is interpreted as a formula
     * by Excel/Sheets when the export is opened — prefixing it with a
     * quote keeps it as inert text instead (standard CSV-injection fix).
     */
    private function escapeCsvFormula(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
