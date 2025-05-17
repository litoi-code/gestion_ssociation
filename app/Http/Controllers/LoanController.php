<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Fund;
use App\Models\Repayment;  // Add this line
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LoanController extends Controller
{
    // Display all loans
    public function index()
    {
        $loans = Loan::with(['member', 'fund', 'repayments'])->orderBy('start_date', 'asc')->get();

        $totalLoanAmount = 0;
        $totalInterest = 0;
        $totalToRepay = 0;

        foreach ($loans as $loan) {
            $balance = $this->calculateLoanBalance($loan);

            // Set values using the accessor methods
            $loan->current_total = $balance['total_amount'];
            $loan->current_interest = $balance['interest_accumulated'];

            // Calculate totals
            $totalLoanAmount += $loan->amount;
            $totalInterest += $balance['interest_accumulated'];
            $totalToRepay += $balance['total_amount'];
        }

        return view('loans.index', compact('loans', 'totalLoanAmount', 'totalInterest', 'totalToRepay'));
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
       $amount = $validated['amount'];
       $startDate = Carbon::parse($validated['start_date']);
       $fund = Fund::find($validated['fund_id']);
       

        // Allow loans even with insufficient balance
        // Update fund balance when creating a loan
        $fund->balance -= $amount;
        $fund->save();

        // Calculate initial loan state
        $initialBalance = $this->calculateLoanBalance(new Loan([
            'amount' => $amount,
            'interest_rate' => $validated['interest_rate'],
            'start_date' => $startDate,
            'remaining_balance' => $amount, // Add this line
        ]));

        // Create the loan
        $loan = Loan::create([
            'member_id' => $validated['member_id'],
            'fund_id' => $validated['fund_id'],
            'amount' => $amount,
            'initial_amount' => $amount,
            'interest_rate' => $validated['interest_rate'],
            'start_date' => $startDate,
            'remaining_balance' => $amount,
            'total_amount' => $initialBalance['total_amount'],
        ]);
       return redirect()->route('loans.index')
            ->with('success', 'Loan issued successfully.');
    }

    // Calculate loan balance and interest
    private function calculateLoanBalance(Loan $loan, $asOfDate = null)
    {
        // Ensure we're working with Carbon instances
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : Carbon::now();
        $startDate = $loan->start_date instanceof Carbon ? $loan->start_date : Carbon::parse($loan->start_date);

        // If loan start date is in the future
        if ($startDate->gt($asOfDate)) {
            return [
                'principal' => $loan->remaining_balance,
                'elapsed_months' => 0,
                'interest_accumulated' => 0,
                'total_amount' => $loan->remaining_balance
            ];
        }

        // Get all repayments up to the asOfDate
        $lastRepayment = $loan->repayments()
            ->where('date', '<=', $asOfDate->toDateString())
            ->orderBy('date', 'desc')
            ->first();

        $calculationStartDate = $lastRepayment ?
            Carbon::parse($lastRepayment->date) :
            $startDate;

        // Calculate exact months including partial months
        $elapsedMonths = round($calculationStartDate->floatDiffInMonths($asOfDate));

        // Calculate interest based on remaining balance with proper rounding
        $monthlyInterestRate = round((float)$loan->interest_rate / 100, 4); // Round to 4 decimal places
        $remainingBalance = round($loan->remaining_balance, 2);

        // Calculate total interest
        $totalInterest = round($remainingBalance * $monthlyInterestRate * $elapsedMonths, 2);
        $totalAmount = round($remainingBalance + $totalInterest, 2);

        return [
            'principal' => $remainingBalance,
            'elapsed_months' => $elapsedMonths,
            'interest_accumulated' => $totalInterest,
            'total_amount' => $totalAmount
        ];
    }

    private function calculatePrincipalDeducted(Loan $loan, $asOfDate = null)
    {
        $asOfDate = $asOfDate ? Carbon::parse($asOfDate) : Carbon::now();

        $lastRepayment = $loan->repayments()
            ->where('date', '<=', $asOfDate->toDateString())
            ->orderBy('date', 'desc')
            ->first();

        $totalPrincipalReduction = 0;
        foreach ($loan->repayments as $repayment) {
            $totalPrincipalReduction += $repayment->principal_reduction;
        }

        return $totalPrincipalReduction;
    }

    // Repay a loan
    public function repay(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $repaymentAmount = $validated['amount'];
        $repaymentDate = Carbon::parse($validated['date']);

        // Get current loan balance
        $currentBalance = $this->calculateLoanBalance($loan, $repaymentDate);

        // Ensure repayment amount doesn't exceed current balance
        if ($repaymentAmount > $currentBalance['total_amount']) {
            return redirect()->back()
                ->with('error', 'Repayment amount exceeds current balance with accumulated interest.');
        }

        // Get the date of the last repayment or loan start date if no repayments
        $lastRepaymentDate = $loan->repayments()
            ->orderBy('date', 'desc')
            ->first()?->date ?? $loan->start_date;

        $lastRepaymentDate = Carbon::parse($lastRepaymentDate);

        // Validate repayment date
        if ($repaymentDate->lt($lastRepaymentDate)) {
            return redirect()->back()
                ->with('error', 'Repayment date cannot be earlier than the last repayment or loan start date.');
        }

        // Calculate interest for the period
        $monthsElapsed = $lastRepaymentDate->diffInMonths($repaymentDate);
        $monthlyInterestRate = $loan->interest_rate / 100;
        $interest = $loan->amount * $monthlyInterestRate * $monthsElapsed;

        // Calculate principal reduction
        $principalReduction = $repaymentAmount - $interest;
        if ($principalReduction < 0) {
            $principalReduction = 0;
            $interest = $repaymentAmount;
        }

        // Check if this repayment will fully pay off the loan
        $newRemainingBalance = $currentBalance['total_amount'] - $repaymentAmount;
        if ($newRemainingBalance <= 0) {
            // If overpaid, adjust the interest and principal reduction
            if ($repaymentAmount > $currentBalance['total_amount']) {
                $repaymentAmount = $currentBalance['total_amount'];
                $interest = $currentBalance['interest_accumulated'];
                $principalReduction = $currentBalance['principal'];
            }

            // Set all balances to 0 when loan is fully paid
            $loan->remaining_balance = 0;
            $loan->amount = 0;
            $loan->total_amount = 0;
        } else {
            // Update loan balances for partial payment
            $loan->remaining_balance -= $principalReduction;
            $loan->total_amount = $loan->remaining_balance + $currentBalance['interest_accumulated'];
        }

        // Get the fund
        $fund = Fund::find($loan->fund_id);

        // Update fund balance when repaying a loan
        $fund->balance += $repaymentAmount;
        $fund->save();

        // Add interest to the fund
        $fund->addInterest($interest);

        // Create repayment record
        Repayment::create([
            'loan_id' => $loan->id,
            'amount' => $repaymentAmount,
            'interest' => $interest,
            'principal_reduction' => $principalReduction,
            'date' => $repaymentDate
        ]);

        $loan->save();
        return redirect()->route('loans.index')
            ->with('success', 'Loan repayment recorded successfully.');
    }

    // Show loan details
    public function show(Loan $loan)
    {
        $balance = $this->calculateLoanBalance($loan);
        $principalDeducted = $this->calculatePrincipalDeducted($loan);
        return view('loans.show', compact('loan', 'balance', 'principalDeducted'));
    }

    // Delete a loan
    public function destroy(Loan $loan)
    {
        // Restore fund balance when deleting a loan
        $fund = Fund::find($loan->fund_id);
        $fund->balance += $loan->amount;
        $fund->save();

        $loan->delete();
        return redirect()->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }

    /**
     * Search for loans with filters
     */
    public function search(Request $request)
    {
        // Get search parameters
        $memberName = $request->input('query');
        $date = $request->input('date');
        $status = $request->input('status');

        // Start with all loans and eager load relationships
        $query = Loan::with(['member', 'fund', 'repayments']);

        // If member name is provided, join with members table and filter
        if ($memberName) {
            $query->join('members', 'loans.member_id', '=', 'members.id')
                  ->where('members.name', 'LIKE', '%' . $memberName . '%')
                  ->select('loans.*'); // Make sure we only get loans columns
        }

        // Filter by date if provided
        if ($date) {
            $query->whereDate('loans.start_date', $date);
        }

        // Filter by status if provided
        if ($status !== null && $status !== '') {
            if ($status === 'paid') {
                $query->where('loans.remaining_balance', '<=', 0);
            } else if ($status === 'active') {
                $query->where('loans.remaining_balance', '>', 0);
            }
        }

        // Get the loans
        $loans = $query->orderBy('start_date', 'asc')->get();

        // Calculate current balance and interest for each loan
        foreach ($loans as $loan) {
            $balance = $this->calculateLoanBalance($loan);
            $loan->current_total = $balance['total_amount'];
            $loan->current_interest = $balance['interest_accumulated'];
        }

        // Format the results
        $formattedLoans = $loans->map(function ($loan) {
            return [
                'id' => $loan->id,
                'member_name' => $loan->member->name,
                'fund_name' => $loan->fund->name,
                'amount' => $loan->amount,
                'interest_rate' => $loan->interest_rate,
                'remaining_balance' => $loan->remaining_balance,
                'current_total' => $loan->current_total,
                'current_interest' => $loan->current_interest,
                'start_date' => $loan->start_date->format('Y-m-d'),
                'status' => $loan->remaining_balance <= 0 ? 'Paid' : 'Active',
            ];
        });

        // Calculate totals
        $totalLoanAmount = $formattedLoans->sum('amount');
        $totalInterest = $formattedLoans->sum('current_interest');
        $totalToRepay = $formattedLoans->sum('current_total');

        return response()->json([
            'loans' => $formattedLoans,
            'totalLoanAmount' => $totalLoanAmount,
            'totalInterest' => $totalInterest,
            'totalToRepay' => $totalToRepay
        ]);
    }
}
