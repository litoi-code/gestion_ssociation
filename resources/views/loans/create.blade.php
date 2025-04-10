@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Issue New Loan</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('loans.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="member_id" class="block text-gray-700 text-sm font-bold mb-2">Member</label>
                <select name="member_id" id="member_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                    <option value="">Select Member</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="fund_id" class="block text-gray-700 text-sm font-bold mb-2">Fund</label>
                <select name="fund_id" id="fund_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                    <option value="">Select Fund</option>
                    @foreach($funds as $fund)
                        <option value="{{ $fund->id }}" {{ old('fund_id') == $fund->id ? 'selected' : '' }}>
                            {{ $fund->name }} (Balance: {{ number_format($fund->balance) }} XAF)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Loan Amount (XAF)</label>
                <input type="number" name="amount" id="amount" 
                       class="shadow border rounded w-full py-2 px-3 text-gray-700" 
                       value="{{ old('amount') }}" required>
            </div>

            <div class="mb-4">
                <label for="interest_rate" class="block text-gray-700 text-sm font-bold mb-2">Interest Rate (%)</label>
                <input type="number" name="interest_rate" id="interest_rate" 
                       class="shadow border rounded w-full py-2 px-3 text-gray-700" 
                       value="{{ old('interest_rate', 10) }}" step="0.1" required>
            </div>

            <div class="mb-6">
                <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" 
                       class="shadow border rounded w-full py-2 px-3 text-gray-700" 
                       value="{{ old('start_date', date('Y-m-d')) }}" required>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Issue Loan
                </button>
                <a href="{{ route('loans.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const interestRateInput = document.getElementById('interest_rate');
    const fundSelect = document.getElementById('fund_id');

    fundSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxBalance = parseFloat(selectedOption.textContent.match(/Balance: ([\d,]+)/)[1].replace(/,/g, ''));
        amountInput.max = maxBalance;
    });
});
</script>
@endsection

