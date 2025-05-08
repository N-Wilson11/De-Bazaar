@extends('layouts.app')

@section('title', __('general.dashboard'))
@section('content')
<div class="container mt-4">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('general.dashboard') }}</span>
                    @if(Auth::user()->user_type === 'zakelijk')
                        <a href="{{ route('contract.generate', Auth::user()->id) }}" class="btn btn-primary btn-sm">
                            {{ __('general.download_contract') }}
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <h3>{{ __('general.welcome') }}, {{ Auth::user()->name }}</h3>
                    <p>{{ __('general.successfulllogin') }}</p>
                    
                    <div class="mt-4">
                        <h4>{{ __('general.account_details') }}</h4>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>{{ __('general.name') }}</th>
                                    <td>{{ Auth::user()->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.email') }}</th>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.user_type') }}</th>
                                    <td>
                                        @if(Auth::user()->user_type === 'particulier')
                                            {{ __('general.private_user') }}
                                        @else
                                            {{ __('general.business_user') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('general.registered_on') }}</th>
                                    <td>{{ Auth::user()->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    @if(Auth::user()->user_type === 'zakelijk')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.business_info') }}</h5>
                            <p>{{ __('general.business_features') }}</p>
                            <ul>
                                <li>{{ __('general.unlimited_ads') }}</li>
                                <li>{{ __('general.stats') }}</li>
                                <li>{{ __('general.priority_search') }}</li>
                            </ul>
                            <p>{{ __('general.download_contract_info') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection