@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Interest Distributions</h1>
        <a href="{{ route('interest-distributions.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            New Distribution
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($distributions->isEmpty())
        <p class="text-gray-500 text-center py-4">No interest distributions found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Member</th>
                        <th class="px-4 py-2 text-left">Fund</th>
                        <th class="px-4 py-2 text-right">Share %</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distributions as $distribution)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $distribution->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $distribution->member->name }}</td>
                            <td class="px-4 py-2">{{ $distribution->fund->name }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($distribution->share_percentage, 2) }}%</td>
                            <td class="px-4 py-2 text-right">{{ number_format($distribution->interest_amount, 2) }} Fcfa</td>
                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('interest-distributions.show', $distribution) }}" 
                                   class="text-blue-500 hover:text-blue-700">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($distributions->hasPages())
            <div class="mt-4">
                {{ $distributions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection


