@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit Penalty</h1>
    <form action="{{ route('penalties.update', $penalty->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="member_id" class="block text-sm font-medium mb-2">Member</label>
            <select name="member_id" id="member_id" class="border p-2 w-full @error('member_id') border-red-500 @enderror" required>
                @foreach ($members as $member)
                    <option value="{{ $member->id }}" {{ $member->id == $penalty->member_id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
            @error('member_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="amount" class="block text-sm font-medium mb-2">Amount</label>
            <input type="number" name="amount" id="amount" class="border p-2 w-full @error('amount') border-red-500 @enderror" step="0.01" value="{{ $penalty->amount }}" required>
            @error('amount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="reason" class="block text-sm font-medium mb-2">Reason</label>
            <input type="text" name="reason" id="reason" class="border p-2 w-full @error('reason') border-red-500 @enderror" value="{{ $penalty->reason }}" required>
            @error('reason')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="date" class="block text-sm font-medium mb-2">Date</label>
            <input type="date" name="date" id="date" class="border p-2 w-full @error('date') border-red-500 @enderror" value="{{ $penalty->date }}" required>
            @error('date')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="is_paid" class="block text-sm font-medium mb-2">Is Paid?</label>
            <select name="is_paid" id="is_paid" class="border p-2 w-full @error('is_paid') border-red-500 @enderror" required>
                <option value="0" {{ $penalty->is_paid == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ $penalty->is_paid == 1 ? 'selected' : '' }}>Yes</option>
            </select>
            @error('is_paid')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Update</button>
    </form>
</div>
@endsection
