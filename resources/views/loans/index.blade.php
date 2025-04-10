@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestion des prêts</h1>
        <a href="{{ route('loans.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            New Loan
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fund</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Payer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($loans as $loan)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loan->member->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loan->fund->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ number_format($loan->amount) }} 
                        @if(config('app.debug'))
                        <small class="text-gray-500">
                            (Remaining: {{ number_format($loan->remaining_balance) }})
                        </small>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loan->interest_rate }}%</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ number_format($loan->current_total) }}
                        @if(config('app.debug'))
                        <small class="text-gray-500">
                            (Interest: {{ number_format($loan->current_interest) }})
                        </small>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $loan->start_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $loan->remaining_balance <= 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $loan->remaining_balance <= 0 ? 'Paid' : 'Active' }}
                        </span>
                        @if(config('app.debug'))
                        <small class="text-gray-500">
                            (Balance: {{ $loan->remaining_balance }})
                        </small>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                        <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Are you sure you want to delete this loan?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection




