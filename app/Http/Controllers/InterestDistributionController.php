<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\InterestDistribution;
use App\Models\Fund;
use Illuminate\Http\Request;

class InterestDistributionController extends Controller
{
    public function index()
    {
        $distributions = InterestDistribution::with(['member', 'fund'])
            ->latest()
            ->paginate(10);
        
        return view('interest_distributions.index', compact('distributions'));
    }

    public function create()
    {
        $members = Member::all();
        $funds = Fund::all();
        return view('interest_distributions.create', compact('members', 'funds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'fund_id' => 'required|exists:funds,id',
            'date' => 'required|date',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'interest_amount' => 'required|numeric|min:0',
        ]);

        InterestDistribution::create($validated);

        return redirect()->route('interest-distributions.index')
            ->with('success', 'Interest distribution created successfully.');
    }

    public function show(InterestDistribution $interestDistribution)
    {
        return view('interest_distributions.show', compact('interestDistribution'));
    }

    public function edit(InterestDistribution $interestDistribution)
    {
        $members = Member::all();
        $funds = Fund::all();
        return view('interest_distributions.edit', 
            compact('interestDistribution', 'members', 'funds'));
    }

    public function update(Request $request, InterestDistribution $interestDistribution)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'fund_id' => 'required|exists:funds,id',
            'date' => 'required|date',
            'share_percentage' => 'required|numeric|min:0|max:100',
            'interest_amount' => 'required|numeric|min:0',
        ]);

        $interestDistribution->update($validated);

        return redirect()->route('interest-distributions.index')
            ->with('success', 'Interest distribution updated successfully.');
    }

    public function destroy(InterestDistribution $interestDistribution)
    {
        $interestDistribution->delete();

        return redirect()->route('interest-distributions.index')
            ->with('success', 'Interest distribution deleted successfully.');
    }

    public function calculateMemberShare(Member $member)
    {
        // Calculate member's share based on their contributions and loans
        // This is a placeholder - implement your actual calculation logic
        $totalContributions = $member->contributions()->sum('amount');
        $totalLoans = $member->loans()->sum('amount');
        $sharePercentage = $this->calculateSharePercentage($totalContributions, $totalLoans);
        
        // Create new interest distribution
        InterestDistribution::create([
            'member_id' => $member->id,
            'fund_id' => Fund::first()->id, // You might want to modify this
            'date' => now(),
            'share_percentage' => $sharePercentage,
            'interest_amount' => $this->calculateInterestAmount($sharePercentage),
        ]);

        return back()->with('success', 'Interest share calculated and distributed successfully.');
    }

    private function calculateSharePercentage($contributions, $loans)
    {
        // Implement your share percentage calculation logic
        // This is a simple example
        $total = $contributions - $loans;
        return max(0, min(100, ($total / 1000) * 100));
    }

    private function calculateInterestAmount($sharePercentage)
    {
        // Implement your interest amount calculation logic
        // This is a simple example
        return ($sharePercentage / 100) * 1000;
    }
}



