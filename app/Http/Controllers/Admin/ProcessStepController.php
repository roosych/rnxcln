<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessStepRequest;
use App\Models\ProcessStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcessStepController extends Controller
{
    /**
     * Steps for a specific service ("How we clean it") are edited from that
     * service's own form (Admin\ServiceController) instead — this page only
     * manages the two remaining shared, page-level step lists.
     */
    public const GROUPS = ['home', 'services'];

    public function index(): View
    {
        $steps = ProcessStep::query()->orderBy('group')->orderBy('sort_order')->get()->groupBy('group');

        return view('admin.process-steps.index', ['steps' => $steps, 'groups' => self::GROUPS]);
    }

    public function store(ProcessStepRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['sort_order'] = ProcessStep::where('group', $data['group'])->max('sort_order') + 1;

        ProcessStep::create($data);

        return redirect()->route('admin.process-steps.index')->with('status', 'Step created.');
    }

    public function edit(ProcessStep $processStep): View
    {
        return view('admin.process-steps.form', ['step' => $processStep, 'groups' => self::GROUPS]);
    }

    public function update(ProcessStepRequest $request, ProcessStep $processStep): RedirectResponse
    {
        // sort_order isn't part of this form — only the drag-to-reorder list
        // on the index page changes it — so it's left out of the update data.
        $data = $request->validated();
        unset($data['sort_order']);

        $processStep->update($data);

        return redirect()->route('admin.process-steps.index')->with('status', 'Step updated.');
    }

    public function destroy(ProcessStep $processStep): RedirectResponse
    {
        $processStep->delete();

        return back()->with('status', 'Step deleted.');
    }

    public function reorder(Request $request, string $group): void
    {
        abort_unless(in_array($group, self::GROUPS, true), 404);

        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach (array_values($ids) as $order => $id) {
            ProcessStep::where('group', $group)->whereKey($id)->update(['sort_order' => $order]);
        }
    }

}
