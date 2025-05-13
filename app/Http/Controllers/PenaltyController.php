<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\Member;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    // Display all penalties
    public function index(Request $request)
    {
        $search = $request->input('search');

        $penalties = Penalty::with('member')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            })
            ->get();

        $totalPenalties = $penalties->sum('amount');

        return view('penalties.index', compact('penalties', 'totalPenalties', 'search'));
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

        return redirect()->route('penalties.create')->with('success', 'Penalty assigned successfully.');
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

    // Add this new method for real-time search
    public function search(Request $request)
    {
        // Get search parameters
        $memberName = $request->input('query');
        $date = $request->input('date');
        $status = $request->input('status');

        // Start with all penalties and eager load the member relationship
        $query = Penalty::with('member');

        // If member name is provided, join with members table and filter
        if ($memberName) {
            $query->join('members', 'penalties.member_id', '=', 'members.id')
                  ->where('members.name', 'LIKE', '%' . $memberName . '%')
                  ->select('penalties.*'); // Make sure we only get penalties columns
        }

        // Filter by date if provided
        if ($date) {
            $query->where('penalties.date', $date);
        }

        // Filter by payment status if provided
        if ($status !== null && $status !== '') {
            $query->where('penalties.is_paid', $status);
        }

        // Execute the query
        $penalties = $query->get();

        // Format the results
        $formattedPenalties = [];
        foreach ($penalties as $penalty) {
            $formattedPenalties[] = [
                'id' => $penalty->id,
                'member_name' => $penalty->member->name,
                'amount' => $penalty->amount,
                'reason' => $penalty->reason,
                'date' => $penalty->date,
                'is_paid' => $penalty->is_paid,
            ];
        }

        // Calculate total amount
        $totalAmount = $penalties->sum('amount');

        return response()->json([
            'penalties' => $formattedPenalties,
            'totalPenalties' => $totalAmount
        ]);
    }
}


