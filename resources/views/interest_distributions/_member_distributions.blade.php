<div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Interest Earnings</h2>
        <form action="{{ route('interest-distributions.calculate', $member->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Calculate Interest Share
            </button>
        </form>
    </div>

    @if($interestDistributions->isEmpty())
        <p class="text-gray-500">No interest distributions found for this member.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Fund</th>
                        <th class="px-4 py-2 text-right">Share %</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($interestDistributions as $distribution)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $distribution->date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2">{{ $distribution->fund->name }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($distribution->share_percentage, 2) }}%</td>
                            <td class="px-4 py-2 text-right">{{ number_format($distribution->interest_amount, 2) }} Fcfa</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="px-4 py-2 text-right">Total Earnings:</td>
                        <td class="px-4 py-2 text-right">
                            {{ number_format($interestDistributions->sum('interest_amount'), 2) }} Fcfa
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>