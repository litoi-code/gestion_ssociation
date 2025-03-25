@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Create Transfer</h1>
    <form action="{{ route('transfers.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="sender_id" class="block text-sm font-medium mb-2">Sender</label>
            <select id="sender_id" name="sender_id" class="border p-2 w-full" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="receiver_id" class="block text-sm font-medium mb-2">Receiver</label>
            <select id="receiver_id" name="receiver_id" class="border p-2 w-full" required>
                @foreach($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium mb-2">Amount</label>
            <input type="number" step="0.01" id="amount" name="amount" class="border p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="date" class="block text-sm font-medium mb-2">Date</label>
            <input type="date" id="date" name="date" class="border p-2 w-full" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium mb-2">Description</label>
            <textarea id="description" name="description" rows="3" class="border p-2 w-full"></textarea>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Create Transfer</button>
    </form>
</div>
@endsection
