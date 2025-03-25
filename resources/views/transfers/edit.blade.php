@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Transfer</h1>
    <form action="{{ route('transfers.update', $transfer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="sender_id" class="block text-sm font-medium mb-2">Sender</label>
            <select id="sender_id" name="sender_id" class="border p-2 w-full @error('sender_id') border-red-500 @enderror" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ $transfer->sender_id == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
            @error('sender_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="receiver_id" class="block text-sm font-medium mb-2">Receiver</label>
            <select id="receiver_id" name="receiver_id" class="border p-2 w-full @error('receiver_id') border-red-500 @enderror" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ $transfer->receiver_id == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
            @error('receiver_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium mb-2">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" value="{{ $transfer->amount }}" class="border p-2 w-full @error('amount') border-red-500 @enderror" required>
            @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="date" class="block text-sm font-medium mb-2">Date</label>
            <input type="date" id="date" name="date" value="{{ $transfer->date }}" class="border p-2 w-full @error('date') border-red-500 @enderror" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium mb-2">Description</label>
            <textarea id="description" name="description" class="border p-2 w-full @error('description') border-red-500 @enderror">{{ $transfer->description }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Update Transfer</button>
    </form>
</div>
@endsection
