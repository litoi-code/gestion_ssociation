@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Penalités (Total: <span id="totalPenalties">{{ number_format($totalPenalties, 2) }}</span> Fcfa)</h1>
        <a href="{{ route('penalties.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Assigner</a>
    </div>

    <!-- Search Filters -->
    <div class="mb-4 flex flex-wrap gap-4">
        <!-- Name Search -->
        <div class="flex-1 min-w-[200px]">
            <input
                type="text"
                id="searchInput"
                placeholder="Rechercher par nom..."
                class="border rounded px-3 py-2 w-full"
            >
        </div>

        <!-- Date Filter -->
        <div class="flex-1 min-w-[200px]">
            <input
                type="date"
                id="dateFilter"
                class="border rounded px-3 py-2 w-full"
                value="{{ now()->format('Y-m-d') }}"
            >
        </div>

        <!-- Status Filter -->
        <div class="flex-1 min-w-[200px]">
            <select
                id="statusFilter"
                class="border rounded px-3 py-2 w-full"
            >
                <option value="">Tous les statuts</option>
                <option value="0">Non payées</option>
                <option value="1">Payées</option>
            </select>
        </div>
    </div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Membre</th>
                <th class="p-2">Montant</th>
                <th class="p-2">Raison</th>
                <th class="p-2">Date</th>
                <th class="p-2">Payée</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody id="penaltiesTableBody">
            @foreach ($penalties as $penalty)
                <tr class="border-b">
                    <td class="p-2">{{ $penalty->member->name }}</td>
                    <td class="p-2">{{ number_format($penalty->amount, 2) }} Fcfa</td>
                    <td class="p-2">{{ $penalty->reason }}</td>
                    <td class="p-2">{{ $penalty->date }}</td>
                    <td class="p-2">{{ $penalty->is_paid ? 'Yes' : 'No' }}</td>
                    <td class="p-2">
                        @if (!$penalty->is_paid)
                            <form action="{{ route('penalties.pay', $penalty) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Pay</button>
                            </form>
                        @endif
                        <a href="{{ route('penalties.edit', $penalty->id) }}" class="text-blue-500 hover:underline">Edit</a>
                        <form action="{{ route('penalties.destroy', $penalty->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 ml-2">Supprimer</button>
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
    const statusFilter = document.getElementById('statusFilter');
    const penaltiesTableBody = document.getElementById('penaltiesTableBody');

    // Function to format number with commas and 2 decimal places
    function formatNumber(number) {
        return number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Trigger search on page load with the default date
    const defaultDate = dateFilter.value.trim();
    performSearch('', defaultDate, '');

    // Function to perform AJAX search
    function performSearch(query, date, status) {
        // Create a URLSearchParams object for proper URL parameter handling
        const params = new URLSearchParams();

        // Add parameters if they have values
        if (query) params.append('query', query);
        if (date) params.append('date', date);
        if (status !== '') params.append('status', status);

        // Build the URL
        const url = `/penalties/search?${params.toString()}`;

        console.log('Search URL:', url);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);

                // Update total penalties
                document.getElementById('totalPenalties').textContent = formatNumber(data.totalPenalties);

                // Clear and update the table body
                let html = '';
                if (data.penalties.length === 0) {
                    html = `<tr><td colspan="6" class="p-4 text-center text-gray-500">Aucune pénalité trouvée</td></tr>`;
                } else {
                    data.penalties.forEach(penalty => {
                        html += `
                            <tr class="border-b">
                                <td class="p-2">${penalty.member_name}</td>
                                <td class="p-2">${formatNumber(penalty.amount)} Fcfa</td>
                                <td class="p-2">${penalty.reason}</td>
                                <td class="p-2">${penalty.date}</td>
                                <td class="p-2">${penalty.is_paid ? 'Yes' : 'No'}</td>
                                <td class="p-2">
                                    ${!penalty.is_paid ? `
                                        <form action="/penalties/${penalty.id}/pay" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Pay</button>
                                        </form>
                                    ` : ''}
                                    <a href="/penalties/${penalty.id}/edit" class="text-blue-500 hover:underline">Edit</a>
                                    <form action="/penalties/${penalty.id}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 ml-2">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }
                penaltiesTableBody.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function to get all current filter values and perform search
    function updateSearch() {
        const query = searchInput.value.trim();
        const date = dateFilter.value.trim();
        const status = statusFilter.value.trim();

        console.log('Search parameters:', { query, date, status });
        performSearch(query, date, status);
    }

    // Attach event listener to search input with debounce
    searchInput.addEventListener('input', debounce(function() {
        updateSearch();
    }, 300));

    // Attach event listener to date filter
    dateFilter.addEventListener('change', function() {
        updateSearch();
    });

    // Attach event listener to status filter
    statusFilter.addEventListener('change', function() {
        updateSearch();
    });

    // Also listen for keyup on search input for immediate feedback
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            updateSearch();
        }
    });
});
</script>
@endsection


