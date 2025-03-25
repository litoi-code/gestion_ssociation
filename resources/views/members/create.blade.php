@extends('layouts.app')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Add Member</h1>

    @if (session('success'))
        <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative fade-out" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('members.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium mb-2">Name</label>
            <input type="text" id="name" name="name" class="border p-2 w-full" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">Save</button>
    </form>
</div>
@endsection

@section('scripts')
    <script>
        window.onload = function() {
            setTimeout(function() {
                var element = document.getElementById('success-message');
                if (element) {
                    element.style.opacity = 0;
                    element.style.transition = 'opacity 0.5s';
                }
            }, 2000); // 2000 milliseconds = 2 seconds
        };
    </script>
@endsection
