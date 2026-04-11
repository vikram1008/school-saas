<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\FeeHead;
use Illuminate\Http\Request;

class FeeHeadController extends Controller
{
    public function index()
    {
        $feeHeads = FeeHead::orderBy('sort_order')->get();
        return view('tenant.fees.heads.index', compact('feeHeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'name_hi'   => ['nullable', 'string', 'max:100'],
            'frequency' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
        ]);

        FeeHead::create([
            'name'        => $request->name,
            'name_hi'     => $request->name_hi,
            'frequency'   => $request->frequency,
            'type'        => 'custom',
            'is_optional' => $request->boolean('is_optional'),
            'is_active'   => true,
            'sort_order'  => FeeHead::max('sort_order') + 1,
        ]);

        return redirect()
            ->route('tenant.fees.heads.index')
            ->with('success', "Fee head \"{$request->name}\" created.");
    }

    public function update(Request $request, FeeHead $feeHead)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'name_hi'   => ['nullable', 'string', 'max:100'],
            'frequency' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
        ]);

        $feeHead->update([
            'name'        => $request->name,
            'name_hi'     => $request->name_hi,
            'frequency'   => $request->frequency,
            'is_optional' => $request->boolean('is_optional'),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('tenant.fees.heads.index')
            ->with('success', 'Fee head updated.');
    }

    public function destroy(FeeHead $feeHead)
    {
        if ($feeHead->type === 'preset') {
            return back()->withErrors(['error' => 'Preset fee heads cannot be deleted.']);
        }
        $feeHead->delete();
        return redirect()
            ->route('tenant.fees.heads.index')
            ->with('success', 'Fee head deleted.');
    }
}