@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Gestion des prêts</h1>
            <a href="{{ route('loans.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                New Loan
            </a>
        </div>

        <!-- Search Filters -->
        <div class="mb-6 flex flex-wrap gap-4">
            <!-- Name Search -->
            <div class="flex-1 min-w-[200px]">
                <input type="text" id="searchInput" placeholder="Rechercher par nom..."
                    class="border rounded px-3 py-2 w-full">
            </div>

            <!-- Date Filter -->
            <div class="flex-1 min-w-[200px]">
                <input type="date" id="dateFilter" class="border rounded px-3 py-2 w-full">
            </div>

            <!-- Status Filter -->
            <div class="flex-1 min-w-[200px]">
                <select id="statusFilter" class="border rounded px-3 py-2 w-full">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="paid">Payés</option>
                </select>
            </div>
        </div>

        <!-- Summary Information -->
        <div class="mb-6 flex flex-wrap justify-between gap-4">
            <div class="bg-white shadow rounded-lg p-4 flex-1 min-w-[200px]">
                <h3 class="text-lg font-semibold mb-2">Total des prêts</h3>
                <p class="text-2xl font-bold" id="totalLoanAmount">{{ number_format($totalLoanAmount) }} Fcfa</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4 flex-1 min-w-[200px]">
                <h3 class="text-lg font-semibold mb-2">Total des intérêts</h3>
                <p class="text-2xl font-bold" id="totalInterest">{{ number_format($totalInterest) }} Fcfa</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4 flex-1 min-w-[200px]">
                <h3 class="text-lg font-semibold mb-2">Total à rembourser</h3>
                <p class="text-2xl font-bold" id="totalToRepay">{{ number_format($totalToRepay) }} Fcfa</p>
            </div>
            <div class="bg-white shadow rounded-lg p-4 flex-1 min-w-[200px]">
                <h3 class="text-lg font-semibold mb-2">Nombre de prêts</h3>
                <p class="text-2xl font-bold" id="loanCount">{{ $loans->count() }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table id="loansTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fund</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Principal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">A Payer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="loansTableBody" class="bg-white divide-y divide-gray-200">
                    @foreach ($loans as $loan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loan->member->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loan->fund->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($loan->amount) }}
                                @if (config('app.debug'))
                                    <small class="text-gray-500">
                                        (Remaining: {{ number_format($loan->remaining_balance) }})
                                    </small>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loan->interest_rate }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($loan->current_total) }}
                                @if (config('app.debug'))
                                    <small class="text-gray-500">
                                        (Interest: {{ number_format($loan->current_interest) }})
                                    </small>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $loan->start_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $loan->remaining_balance <= 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $loan->remaining_balance <= 0 ? 'Paid' : 'Active' }}
                                </span>
                                @if (config('app.debug'))
                                    <small class="text-gray-500">
                                        (Balance: {{ $loan->remaining_balance }})
                                    </small>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('loans.show', $loan) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to delete this loan?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript for Real-Time Search -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const dateFilter = document.getElementById('dateFilter');
            const statusFilter = document.getElementById('statusFilter');
            const loansTableBody = document.getElementById('loansTableBody');

            // Function to format number with commas
            function formatNumber(number) {
                return number.toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }

            // Trigger search on page load
            updateSearch();

            // Function to get all current filter values and perform search
            function updateSearch() {
                const query = searchInput.value.trim();
                const date = dateFilter.value.trim();
                const status = statusFilter.value.trim();

                console.log('Search parameters:', {
                    query,
                    date,
                    status
                });
                performSearch(query, date, status);
            }

            // Function to perform AJAX search
            function performSearch(query, date, status) {
                // Create a URLSearchParams object for proper URL parameter handling
                const params = new URLSearchParams();

                // Add parameters if they have values
                if (query) params.append('query', query);
                if (date) params.append('date', date);
                if (status !== '') params.append('status', status);

                // Build the URL
                const url = `/loans/search?${params.toString()}`;

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

                        // Update summary information
                        document.getElementById('totalLoanAmount').textContent = formatNumber(data
                            .totalLoanAmount) + ' Fcfa';
                        document.getElementById('totalInterest').textContent = formatNumber(data
                            .totalInterest) + ' Fcfa';
                        document.getElementById('totalToRepay').textContent = formatNumber(data
                            .totalToRepay) + ' Fcfa';
                        document.getElementById('loanCount').textContent = data.loans.length;

                        // Clear and update the table body
                        let html = '';
                        if (data.loans.length === 0) {
                            html =
                                `<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Aucun prêt trouvé</td></tr>`;
                        } else {
                            data.loans.forEach(loan => {
                                html += `
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">${loan.member_name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${loan.fund_name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${formatNumber(loan.amount)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${loan.interest_rate}%</td>
                                <td class="px-6 py-4 whitespace-nowrap">${formatNumber(loan.current_total)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${loan.start_date}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        ${loan.status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                        ${loan.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/loans/${loan.id}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <form action="/loans/${loan.id}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure you want to delete this loan?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                            });
                        }
                        loansTableBody.innerHTML = html;
                    })
                    .catch(error => console.error('Error:', error));
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
        });
    </script>
@endsection
