<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LoanController extends Controller
{
    // Display all loans
    public function index()
    {
        $loans = Loan::with(['member', 'fund'])->get();
        return view('loans.index', compact('loans'));
    }

    // Show form to create a new loan
    public function create()
    {
        $members = Member::all();
        $funds = Fund::all();
        return view('loans.create', compact('members', 'funds'));
    }

    // Store a new loan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'fund_id' => 'required|exists:funds,id',
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        // Ensure fund balance is sufficient
        $fund = Fund::find($validated['fund_id']);
        if ($validated['amount'] > $fund->balance) {
            return redirect()->route('loans.create')->with('error', 'Requested loan amount exceeds fund balance.');
        }

        $amount = $validated['amount'];
        $interestRate = $validated['interest_rate'];
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $currentDate = \Carbon\Carbon::now();
        $elapsedMonths = ($currentDate->format('Y') - $startDate->format('Y')) * 12 + ($currentDate->format('m') - $startDate->format('m'));
        if ($elapsedMonths < 0) {
            $elapsedMonths = 0;
        }
        $totalInterest = ($amount * $interestRate / 100) * $elapsedMonths;
        if ($elapsedMonths == 0) {
            $totalAmount = $amount;
            $remainingBalance = $amount;
        } else {
            $totalAmount = $amount + $totalInterest;
            $remainingBalance = $totalAmount;
        }

        // Create the loan
        $loan = Loan::create(array_merge($validated, [
            'total_amount' => $totalAmount,
            'remaining_balance' => $remainingBalance,
        ]));

        // Update fund balance (deduct loan amount)
        $fund = Fund::find($validated['fund_id']);
        $fund->balance -= $validated['amount'];
        $fund->save();

        return redirect()->route('loans.index')->with('success', 'Loan issued successfully.');
    }

    // Repay a loan
    public function repay(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $repaymentAmount = $validated['amount'];

        // Ensure repayment amount does not exceed remaining balance
        if ($repaymentAmount > $loan->remaining_balance) {
            return redirect()->back()->with('error', 'Repayment amount exceeds remaining balance.');
        }

        // Deduct repayment from remaining balance
        $loan->remaining_balance -= $repaymentAmount;

        //Recalculate interest
        $amount = $loan->amount;
        $interestRate = $loan->interest_rate;
        $startDate = \Carbon\Carbon::parse($loan->start_date);
        $currentDate = \Carbon\Carbon::now();
        $elapsedMonths = ($currentDate->format('Y') - $startDate->format('Y')) * 12 + ($currentDate->format('m') - $startDate->format('m'));
        if ($elapsedMonths < 0) {
            $elapsedMonths = 0;
        }
        $totalInterest = ($loan->remaining_balance * $interestRate / 100) * $elapsedMonths;
        $totalAmount = $amount + $totalInterest;

        $loan->total_amount = $totalAmount;

        // Update fund balance (add repayment amount)
        $fund = Fund::find($loan->fund_id);
        $fund->balance += $repaymentAmount;
        $fund->save();

        // Create a repayment record
        $repayment = new \App\Models\Repayment();
        $repayment->loan_id = $loan->id;
        $repayment->amount = $repaymentAmount;
        $repayment->date = now();
        $repayment->save();

        // Mark loan as repaid if remaining balance is zero
        if ($loan->remaining_balance <= 0) {
            $loan->remaining_balance = 0;
        }

        $loan->save();
        return redirect()->route('loans.index')->with('success', 'Loan repayment recorded successfully.');
    }

    // Delete a loan
    public function destroy(Loan $loan)
    {
        // Restore fund balance
        $fund = Fund::find($loan->fund_id);
        $fund->balance += $loan->amount;
        $fund->save();

        $loan->delete();
        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }
}
