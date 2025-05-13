@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Member Basic Info -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">{{ $member->name }}</h1>
            <div class="text-lg">
                <span class="font-medium">Solde Total:</span>
                <span class="font-bold">{{ number_format($member->balance, 2) }} Fcfa</span>
            </div>
        </div>
    </div>

    <!-- Interest Distributions Section -->
    @if($investmentFund)
        @include('interest_distributions._member_distributions', [
            'member' => $member,
            'interestDistributions' => $interestDistributions,
            'investmentFund' => $investmentFund
        ])
    @endif

    <!-- Contributions Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Contributions</h2>
        @if($contributions->isEmpty())
            <p class="text-gray-500">Aucune contribution enregistrée.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Caisse</th>
                            <th class="px-4 py-2 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contributions as $contribution)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ date('Y-m-d', strtotime($contribution->created_at)) }}</td>
                                <td class="px-4 py-2">{{ $contribution->fund->name }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($contribution->amount, 2) }} Fcfa</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Loans Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Prêts</h2>
        @if($loans->isEmpty())
            <p class="text-gray-500">Aucun prêt enregistré.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Caisse</th>
                            <th class="px-4 py-2 text-right">Montant</th>
                            <th class="px-4 py-2 text-center">Taux</th>
                            <th class="px-4 py-2 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ date('Y-m-d', strtotime($loan->start_date)) }}</td>
                                <td class="px-4 py-2">{{ $loan->fund->name }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($loan->amount, 2) }} Fcfa</td>
                                <td class="px-4 py-2 text-center">{{ $loan->interest_rate }}%</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-sm {{ $loan->is_paid ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $loan->is_paid ? 'Remboursé' : 'En cours' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Penalties Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Pénalités</h2>
        @if($penalties->isEmpty())
            <p class="text-gray-500">Aucune pénalité enregistrée.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Raison</th>
                            <th class="px-4 py-2 text-right">Montant</th>
                            <th class="px-4 py-2 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penalties as $penalty)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ date('Y-m-d', strtotime($penalty->date)) }}</td>
                                <td class="px-4 py-2">{{ $penalty->reason }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($penalty->amount, 2) }} Fcfa</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="px-2 py-1 rounded text-sm {{ $penalty->is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $penalty->is_paid ? 'Payée' : 'Non payée' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection



