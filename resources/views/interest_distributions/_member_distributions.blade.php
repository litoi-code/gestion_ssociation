@if($investmentFund)
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Investment Fund Interest Earnings</h2>
            <div class="flex space-x-4">
                <span class="text-gray-600">
                    Available Interest: {{ number_format($investmentFund->total_interest, 2) }} XAF
                </span>
                @if($investmentFund->total_interest > 0)
                    <form action="{{ route('interest-distributions.calculate', $member->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            Calculate Interest Share
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($interestDistributions->isEmpty())
            <p class="text-gray-500">No interest distributions found for this member.</p>
        @else
            <div class="mb-6">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-sm font-medium text-gray-500">Total Investment Contribution</h3>
                        <p class="mt-1 text-lg font-bold">
                            {{ number_format($member->contributions()
                                ->whereHas('fund', fn($q) => $q->where('type', 'investment'))
                                ->sum('amount'), 2) }} XAF
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-sm font-medium text-gray-500">Latest Share Percentage</h3>
                        <p class="mt-1 text-lg font-bold">
                            {{ number_format($interestDistributions->first()->share_percentage ?? 0, 2) }}%
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded">
                        <h3 class="text-sm font-medium text-gray-500">Total Interest Earned</h3>
                        <p class="mt-1 text-lg font-bold">
                            {{ number_format($interestDistributions->sum('interest_amount'), 2) }} XAF
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left">Distribution Date</th>
                            <th class="px-4 py-2 text-right">Share %</th>
                            <th class="px-4 py-2 text-right">Interest Amount</th>
                            <th class="px-4 py-2 text-right">Cumulative Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $cumulativeTotal = 0; @endphp
                        @foreach($interestDistributions->sortByDesc('date') as $distribution)
                            @php $cumulativeTotal += $distribution->interest_amount; @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $distribution->date->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($distribution->share_percentage, 2) }}%</td>
                                <td class="px-4 py-2 text-right">{{ number_format($distribution->interest_amount, 2) }} XAF</td>
                                <td class="px-4 py-2 text-right">{{ number_format($cumulativeTotal, 2) }} XAF</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    No investment fund has been configured yet. Please create a fund with type 'investment' first.
                </p>
            </div>
        </div>
    </div>
@endif



