@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">💰 Revenue Dashboard</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Total Revenue</h5>
                <h3>${{ $totalRevenue }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>Today</h5>
                <h3>${{ $todayRevenue }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm">
                <h5>This Month</h5>
                <h3>${{ $monthlyRevenue }}</h3>
            </div>
        </div>
    </div>

    <hr>

    <h4>Recent Payments</h4>

    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($recentPayments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->doctor->name ?? '-' }}</td>
                    <td>{{ $payment->patient->name ?? '-' }}</td>
                    <td>${{ $payment->price }}</td>
                    <td>{{ $payment->updated_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection