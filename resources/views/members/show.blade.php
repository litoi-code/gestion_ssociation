@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- ... existing member info ... -->

    @include('interest_distributions._member_distributions', [
        'member' => $member,
        'interestDistributions' => $interestDistributions
    ])

    <!-- ... other member details ... -->
</div>
@endsection




