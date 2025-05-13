@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Contributions</h1>

    <!-- Search and Add Contribution -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
           <!-- Search Input -->
            <select
                id="searchInput"
                class="border p-2 w-full"
            >
                <option value="">All</option>
                <optgroup label="Members">
                    @foreach($members as $member)
                        <option value="{{ $member->name }}">{{ $member->name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Funds">
                    @foreach($funds as $fund)
                        <option value="{{ $fund->name }}">{{ $fund->name }}</option>
                    @endforeach
                </optgroup>
            </select>

            <!-- Date Filter -->
            <input
                type="date"
                id="dateFilter"
                class="border p-2 w-full"
                value="{{ now()->format('Y-m-d') }}"


            >
        </div>
         <!-- Total Funds Display -->
    {{-- <div class="bg-gray-100 p-4 rounded-lg text-center">
        <h3 class="font-bold mb-2">Total Funds (First 4)</h3>
        <p class="text-2xl font-bold" id="totalFirstFourFunds">0.00 Fcfa</p>
    </div> --}}

        <!-- Add Contribution Button -->
        <a href="{{ route('contributions.create') }}" class="bg-blue-500 text-white px-4 py-2">Ajout Contribution</a>
    </div>

    <!-- Total Balance Per Fund -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Caisses</h2>
        <h2 class="text-xl font-bold">Total: <span id="totalFunds">0.00</span> Fcfa</h2>
        <h2 class="text-xl font-bold">Hôte(sse): <span id="totalFirstFourFunds">0.00</span> </h2>
        <h2 class="text-xl font-bold">Membres: <span id="totalMembers">0</span></h2>
    </div>
    <div id="fundBalancesGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        @foreach ($funds as $fund)
        <div class="bg-gray-100 p-4 rounded-lg text-center">
            <h3 class="font-bold mb-2">{{ $fund->name }} </h3>
            <p class="text-2xl font-bold">{{ number_format($fund->balance, 2) }} Fcfa</p>
        </div>
        @endforeach
    </div>


    <!-- Contributions Table -->
    <table id="contributionsTable" class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Membre</th>
                <th class="p-2">Caisse</th>
                <th class="p-2">Montant</th>
                <th class="p-2">Date</th>
                <th class="p-2">Hôte</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contributions as $contribution)
            <tr class="border-b">
                <td class="p-2">{{ $contribution->member->name }}</td>
                <td class="p-2">{{ $contribution->fund->name }}</td>
                <td class="p-2">{{ number_format($contribution->amount, 2) }} Fcfa</td>
                <td class="p-2">{{ $contribution->date }}</td>
                <td class="p-2">{{ $contribution->host ?? '-' }}</td>
                <td class="p-2 space-x-2">
                    <a href="{{ route('contributions.edit', $contribution) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('contributions.destroy', $contribution) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- JavaScript for Real-Time Search -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const dateFilter = document.getElementById('dateFilter');
    const contributionsTable = document.getElementById('contributionsTable');
    const fundBalancesGrid = document.getElementById('fundBalancesGrid');

    // Trigger search on page load with the default date
    const defaultDate = dateFilter.value.trim();
    performSearch('', defaultDate);

    // Function to perform AJAX search
    function performSearch(query, date) {
        let url = `/contributions/search?query=${encodeURIComponent(query)}`;
        if (date) {
            url += `&date=${encodeURIComponent(date)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Update the contributions table
                const tbody = contributionsTable.querySelector('tbody');
                tbody.innerHTML = ''; // Clear existing rows

                if (data.contributions.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center">Aucune contribution enregistrée.</td></tr>';
                } else {
                    data.contributions.forEach(contribution => {
                        const row = `
                            <tr class="border-b">
                                <td class="p-2">${contribution.member_name}</td>
                                <td class="p-2">${contribution.fund_name}</td>
                                <td class="p-2">${parseFloat(contribution.amount).toFixed(2)} Fcfa</td>
                                <td class="p-2">${contribution.date}</td>
                                <td class="p-2">${contribution.host || '-'}</td>
                                <td class="p-2 space-x-2">
                                    <a href="/contributions/${contribution.id}/edit" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="/contributions/${contribution.id}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                }

                // Update the total fund balances
                fundBalancesGrid.innerHTML = ''; // Clear existing fund cards
                data.fundBalances.forEach(fund => {
                    const card = `
                        <div class="bg-gray-100 p-4 rounded-lg text-center">
                            <h3 class="font-bold mb-2">${fund.name} </h3>
                            <p class="text-2xl font-bold">${parseFloat(fund.balance).toFixed(2)} Fcfa</p>
                        </div>
                    `;
                    fundBalancesGrid.insertAdjacentHTML('beforeend', card);
                });

            // Calculate total of first four funds
                let totalFirstFourFunds = 0;
                for (let i = 0; i < Math.min(2, data.fundBalances.length); i++) {
                    totalFirstFourFunds += parseFloat(data.fundBalances[i].balance);
                }
                document.getElementById('totalFirstFourFunds').textContent = totalFirstFourFunds.toFixed(2) + ' Fcfa';

                // Update the total funds
                document.getElementById('totalFunds').textContent = parseFloat(data.totalFunds).toFixed(2);

                // Update the total members count
                document.getElementById('totalMembers').textContent = data.totalMembers;
            })
            .catch(error => console.error('Error fetching search results:', error));
    }

    // Attach event listener to search input
    searchInput.addEventListener('change', function () {
        const query = this.value.trim();
        const date = dateFilter.value.trim();
        performSearch(query, date);
    });

    // Attach event listener to date filter
    dateFilter.addEventListener('input', function () {
        const query = searchInput.value.trim();
        const date = this.value.trim();
        performSearch(query, date);
    });
});
</script>
@endsection
