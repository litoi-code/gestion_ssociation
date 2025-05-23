@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-4">

        {{-- <h1 class="text-2xl font-bold mb-4">Add Contribution</h1> --}}
        

    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form id="contributionForm" action="{{ route('contributions.store') }}" method="POST">
        @csrf

        <!-- Member and Total Amount -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Member Selection -->
            <div>
                <label for="member_id" class="block text-sm font-medium mb-2">Member</label>
                <select id="member_id" name="member_id" class="border p-2 w-full" required>
                    @foreach ($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Total Amount to Contribute -->
            <div>
                <label for="total_amount" class="block text-sm font-medium mb-2">Montant total à distribuer</label>
                <input 
                    type="text" 
                    id="total_amount" 
                    readonly 
                    class="border p-2 w-full bg-gray-100 cursor-not-allowed"
                    placeholder="Total will update automatically"
                >
            </div>
        </div>

        <!-- Host and Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Host Selection -->
            <div>
                <label for="host" class="block text-sm font-medium mb-2">Hôte (Optional)</label>
                <select id="host" name="host" class="border p-2 w-full">
                    <option value="">None</option>
                    @foreach ($members as $member)
                        @if ($loop->iteration == 1)
                            <option value="{{ $member->name }}" selected>{{ $member->name }}</option>
                        @else
                            <option value="{{ $member->name }}">{{ $member->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Date Field -->
            <div>
                <label for="date" class="block text-sm font-medium mb-2">Date</label>
                <input 
                    type="date" 
                    id="date" 
                    name="date" 
                    class="border p-2 w-full" 
                    value="{{ now()->format('Y-m-d') }}"
                    {{-- value="2025-02-22"  --}}
                    required
                >
            </div>
        </div>

        <!-- Location Field -->
        {{-- <div class="mb-4">
            <label for="location" class="block text-sm font-medium mb-2">Location (Optional)</label>
            <input type="text" id="location" name="location" class="border p-2 w-full">
        </div> --}}

        <!-- Funds Grid -->
        {{-- <h2 class="text-xl font-bold mb-4">Select Funds to Contribute To</h2> --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($funds as $fund)
            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="font-bold mb-2">{{ $fund->name }}</h3>
                {{-- <h3 class="font-bold mb-2">{{ $fund->name }} ({{ ucfirst($fund->type) }})</h3> --}}
                <div class="flex items-center space-x-2">
                    <input 
                        type="number" 
                        step="0.01" 
                        name="amounts[{{ $fund->id }}]" 
                        id="fund_{{ $fund->id }}" 
                        class="border p-2 w-full fund-input" 
                        placeholder="Enter amount"
                    >
                </div>
            </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save Contribution</button>
    </form>
</div>

<!-- JavaScript for Dynamic Total Calculation -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalAmountField = document.getElementById('total_amount');
    const fundInputs = document.querySelectorAll('.fund-input');
    const secondFundInput = fundInputs[1]; // Assuming the second fund is at index 1

    // Set default value for the second fund
    if (secondFundInput) {
        secondFundInput.value = 3500;
    }

    // Function to calculate total amount
    function calculateTotal() {
        let total = 0;
        fundInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        totalAmountField.value = total.toFixed(2);
    }

    // Attach event listeners to fund inputs
    fundInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation to reflect the default value
    calculateTotal();
});
</script>
@endsection
