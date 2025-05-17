@extends('layouts.app')

@section('content')

<a href="{{ route('loans.index') }}">Back to Loans</a>
<br><br>
<div class="container mx-auto px-4 py-6">
    @if (session('success'))
        <div class="bg-green-200 border-green-500 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6">Détails</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
                        <div class="space-y-3">
                            <p><span class="font-medium">Membre:</span> {{ $loan->member->name }}</p>
                            <p><span class="font-medium">Caisse:</span> {{ $loan->fund->name }}</p>
                            <p><span class="font-medium">Principal:</span> {{ number_format($loan->initial_amount) }} XAF</p>
                            <p><span class="font-medium">Start Date:</span> {{ $loan->start_date->format('Y-m-d') }}</p>
                            <p><span class="font-medium">Taux d'intérêt:</span> {{ $loan->interest_rate }}% par mois</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold mb-4">Statut</h2>
                        <div class="space-y-3">
                            <p><span class="font-medium">Principal déduit:</span> {{ number_format($principalDeducted) }} XAF</p>
                            <p><span class="font-medium">Intérêts accumulés:</span> {{ number_format($balance['interest_accumulated']) }} XAF</p>
                            <p><span class="font-medium">Total à Payer:</span> {{ number_format($balance['total_amount']) }} XAF</p>
                            <p><span class="font-medium">Solde:</span> {{ number_format($loan->remaining_balance) }} XAF</p>
                            <p><span class="font-medium">Status:</span> 
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $loan->remaining_balance <= 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $loan->remaining_balance <= 0 ? 'Paid' : 'Active' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($loan->remaining_balance > 0)
        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Paiement</h2>
                <form action="{{ route('loans.repay', $loan) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Montant (XAF)</label>
                            <input type="number" name="amount" id="amount" 
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   max="{{ $loan->remaining_balance }}" required>
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2"> Date</label>
                            <input type="date" name="date" id="date" 
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Soumettre
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-bold mb-4">Historique</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intérêts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($loan->repayments->sortByDesc('date') as $repayment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $repayment->date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($repayment->amount) }} XAF</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($repayment->interest) }} XAF</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($repayment->principal_reduction) }} XAF</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('loans.repayments.destroy', [$loan, $repayment]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this repayment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const dateInput = document.getElementById('date');
    const maxBalance = {{ $loan->remaining_balance }};

    amountInput.addEventListener('input', function() {
        if (this.value > maxBalance) {
            this.value = maxBalance;
        }
    });

    dateInput.min = '{{ $loan->start_date->format('Y-m-d') }}';
    dateInput.max = '{{ date('Y-m-d') }}';
});
</script>
@endsection
