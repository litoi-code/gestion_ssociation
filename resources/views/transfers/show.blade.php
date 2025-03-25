@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-semibold mb-4">Transfer Details</h1>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h5 class="text-xl font-semibold mb-2">Sender: {{ $transfer->sender->name }}</h5>
            <h5 class="text-xl font-semibold mb-2">Receiver: {{ $transfer->receiver->name }}</h5>
            <p class="text-gray-700 text-base mb-2">Amount: {{ $transfer->amount }}</p>
            <p class="text-gray-700 text-base mb-2">Date: {{ $transfer->date }}</p>
            <p class="text-gray-700 text-base mb-4">Description: {{ $transfer->description }}</p>
        </div>

        <a href="{{ route('transfers.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Back</a>
    </div>
@endsection
