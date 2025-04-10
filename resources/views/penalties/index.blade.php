@extends('layouts.app')

@section('content')

<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">

    <h1 class="text-2xl font-bold mb-4">Penalités (Total: {{ number_format($totalPenalties, 2) }} Fcfa)</h1>
    <a href="{{ route('penalties.create') }}" class="bg-blue-500 text-white px-4 py-2 mb-4 inline-block">Assigner</a>
    </div>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Membre</th>
                <th class="p-2">Montant</th>
                <th class="p-2">Raison</th>
                <th class="p-2">Date</th>
                <th class="p-2">Payée</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penalties as $penalty)
            <tr class="border-b">
                <td class="p-2">{{ $penalty->member->name }}</td>
                <td class="p-2">{{ number_format($penalty->amount, 2) }} Fcfa</td>
                <td class="p-2">{{ $penalty->reason }}</td>
                 <td class="p-2">{{ $penalty->date }}</td>
                <td class="p-2">{{ $penalty->is_paid ? 'Yes' : 'No' }}</td>
                <td class="p-2">
                    @if (!$penalty->is_paid)
                    <form action="{{ route('penalties.pay', $penalty) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 text-white px-2 py-1">Pay</button>
                    </form>
                    @endif
                    <a href="{{ route('penalties.edit', $penalty->id) }}" class="text-blue-500 hover:underline">Edit</a>

                    <form action="{{ route('penalties.destroy', $penalty->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 ml-2">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
