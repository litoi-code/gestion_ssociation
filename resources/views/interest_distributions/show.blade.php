@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Interest Distribution Details</h1>
        <a href="{{ route('interest-distributions.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-50 p-4 rounded">
            <h2 class="text-sm font-medium text-gray-500">Date</h2>
            <p class="mt-1 text-lg">{{ $distribution->date->format('Y-m-d') }}</p>
        </div>

        <div class="bg-gray-50 p-4 rounded">
            <h2 class="text-sm font-medium text-gray-500">Fund</h2>
            <p class="mt-1 text-lg">{{ $distribution->fund->name }}</p>
        </div>

        <div class="bg-gray-50 p-4 rounded">
            <h2 class="text-sm font-medium text-gray-500">Member</h2>
            <p class="mt-1 text-lg">{{ $distribution->member->name }}</p>
        </div>

        <div class="bg-gray-50 p-4 rounded">
            <h2 class="text-sm font-medium text-gray-500">Share Percentage</h2>
            <p class="mt-1 text-lg">{{ number_format($distribution->share_percentage, 2) }}%</p>
        </div>

        <div class="bg-gray-50 p-4 rounded col-span-2">
            <h2 class="text-sm font-medium text-gray-500">Interest Amount</h2>
            <p class="mt-1 text-lg font-bold text-blue-600">
                {{ number_format($distribution->interest_amount, 2) }} Fcfa
            </p>
        </div>
    </div>

    <div class="border-t pt-6">
        <h2 class="text-lg font-semibold mb-4">Distribution Summary</h2>
        <div class="bg-gray-50 p-4 rounded">
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Member's Previous Balance</dt>
                    <dd class="mt-1">{{ number_format($distribution->member->balance - $distribution->interest_amount, 2) }} Fcfa</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Interest Added</dt>
                    <dd class="mt-1 text-green-600">+ {{ number_format($distribution->interest_amount, 2) }} Fcfa</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Current Balance</dt>
                    <dd class="mt-1 text-lg font-bold">{{ number_format($distribution->member->balance, 2) }} Fcfa</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection