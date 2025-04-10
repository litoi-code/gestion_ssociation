@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">New Interest Distribution</h1>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('interest-distributions.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label for="fund_id" class="block text-sm font-medium text-gray-700 mb-2">Fund</label>
            <select id="fund_id" name="fund_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Select a fund</option>
                @foreach($funds as $fund)
                    <option value="{{ $fund->id }}">
                        {{ $fund->name }} (Balance: {{ number_format($fund->balance, 2) }} Fcfa)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="total_interest" class="block text-sm font-medium text-gray-700 mb-2">Total Interest Amount</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="number" 
                       name="total_interest" 
                       id="total_interest" 
                       step="0.01"
                       class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500" 
                       placeholder="0.00"
                       required>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-gray-500 sm:text-sm">Fcfa</span>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Distribution Date</label>
            <input type="date" 
                   id="date" 
                   name="date" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   value="{{ date('Y-m-d') }}"
                   required>
        </div>

        <div class="flex justify-between">
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Distribute Interest
            </button>
            <a href="{{ route('interest-distributions.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

