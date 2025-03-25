@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold mb-4">Transferts</h1>
        <a href="{{ route('transfers.create') }}" class="bg-blue-500 text-white px-4 py-2 mb-4 inline-block"> Transférer</a>
    </div>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Source</th>
                <th class="p-2">Bénéficiaire</th>
                <th class="p-2">Montant</th>
                <th class="p-2">Date</th>
                <th class="p-2">Description</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfers as $transfer)
            <tr class="border-b">
                <td class="p-2">{{ $transfer->sender->name }}</td>
                <td class="p-2">{{ $transfer->receiver->name }}</td>
                <td class="p-2">{{ $transfer->amount }} Fcfa</td>
                <td class="p-2">{{ $transfer->date }}</td>
                <td class="p-2">{{ $transfer->description }}</td>
                <td class="p-2">
                    <a href="{{ route('transfers.show', $transfer->id) }}" class="text-blue-500 hover:underline">View</a>
                    <a href="{{ route('transfers.edit', $transfer->id) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
