<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\Member;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    // Display all penalties
    public function index()
    {
        $penalties = Penalty::with('member')->get();
        $totalPenalties = $penalties->sum('amount');
        return view('penalties.index', compact('penalties', 'totalPenalties'));
    }

    // Show form to create a new penalty
    public function create()
    {
        $members = Member::all();
        return view('penalties.create', compact('members'));
    }

    // Store a new penalty
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        Penalty::create($validated);

        return redirect()->route('penalties.index')->with('success', 'Penalty assigned successfully.');
    }

    public function edit(Penalty $penalty)
    {
        $members = Member::all();
        return view('penalties.edit', compact('penalty', 'members'));
    }

    public function update(Request $request, Penalty $penalty)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $penalty->update($validated);

        return redirect()->route('penalties.index')->with('success', 'Penalty updated successfully.');
    }

    // Pay a penalty
    public function pay(Penalty $penalty)
    {
        //----- Deduct penalty amount from member's balance ----
        // $member = Member::find($penalty->member_id);
        // $member->balance -= $penalty->amount;
        // $member->save();

        // Mark penalty as paid
        $penalty->is_paid = true;
        $penalty->save();

        return redirect()->route('penalties.index')->with('success', 'Penalty paid successfully.');
    }

    // Delete a penalty
    public function destroy(Penalty $penalty)
    {
        $penalty->delete();
        return redirect()->route('penalties.index')->with('success', 'Penalty deleted successfully.');
    }
}
