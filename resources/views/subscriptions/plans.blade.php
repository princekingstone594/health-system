@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Choose Your Plan</h2>

    <div class="row mt-4">

        <div class="col-md-4">
            <div class="card p-4">
                <h4>Basic</h4>
                <h3>$10/mo</h3>
                <a href="{{ route('subscribe', 'basic') }}" class="btn btn-primary">Subscribe</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4">
                <h4>Pro</h4>
                <h3>$25/mo</h3>
                <a href="{{ route('subscribe', 'pro') }}" class="btn btn-success">Subscribe</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-4">
                <h4>Enterprise</h4>
                <h3>$50/mo</h3>
                <a href="{{ route('subscribe', 'enterprise') }}" class="btn btn-dark">Subscribe</a>
            </div>
        </div>

    </div>
</div>
@endsection