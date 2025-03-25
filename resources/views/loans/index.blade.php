@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">

        <h1 class="text-2xl font-bold mb-4">Prêts</h1>
        <a href="{{ route('loans.create') }}" class="bg-blue-500 text-white px-4 py-2 mb-4 inline-block">Ajouter</a>
    </div>
    
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Membre</th>
                <th class="p-2">Caisse</th>
                <th class="p-2">Principal</th>
                <th class="p-2">Taux intérêts</th>
                <th class="p-2">Total à payer</th>
                <th class="p-2">Solde</th>
                <th class="p-2">Date Début</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
            <tr class="border-b">
                <td class="p-2">{{ $loan->member->name }}</td>
                <td class="p-2">{{ $loan->fund->name }}</td>
                <td class="p-2">${{ number_format($loan->amount, 2) }}</td>
                <td class="p-2">{{ $loan->interest_rate }}%</td>
                <td class="p-2">${{ number_format($loan->total_amount, 2) }}</td>
                <td class="p-2">${{ number_format($loan->remaining_balance, 2) }}</td>
                <td class="p-2">{{ $loan->start_date }}</td>
                <td class="p-2 flex space-x-2">
                    <!-- Repayment Form -->
                    <form action="{{ route('loans.repay', $loan) }}" method="POST" class="inline">
                        @csrf
                        <div class="flex items-center space-x-2">
                            <input 
                                type="number" 
                                step="0.01" 
                                name="amount" 
                                min="0" 
                                max="{{ $loan->remaining_balance }}" 
                                class="border p-2 w-24" 
                                placeholder="Amount" 
                                required
                            >
                            <button type="submit" class="bg-green-500 text-white px-2 py-1">Repay</button>
                        </div>
                    </form>

                    <!-- Delete Loan -->
                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="p-2">
                    <details>
                        <summary class="cursor-pointer text-blue-500">View Repayment History</summary>
                        <table class="w-full mt-2 border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-2">Date</th>
                                    <th class="p-2">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan->repayments as $repayment)
                                <tr class="border-b">
                                    <td class="p-2">{{ $repayment->date }}</td>
                                    <td class="p-2">${{ number_format($repayment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </details>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
