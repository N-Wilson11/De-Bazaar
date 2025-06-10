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
                                        @elseif(Auth::user()->user_type === 'zakelijk')
                                            {{ __('general.business_user') }}
                                        @elseif(Auth::user()->user_type === 'normaal')
                                            {{ __('general.normal_user') }}
                                        @else
                                            {{ Auth::user()->user_type }}
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
                    @elseif(Auth::user()->user_type === 'normaal')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.normal_user_info') }}</h5>
                            <p>{{ __('general.normal_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.basic_ads') }}</li>
                                <li>{{ __('general.standard_search') }}</li>
                            </ul>
                        </div>
                    @elseif(Auth::user()->user_type === 'particulier')
                        <div class="mt-4 p-3 bg-light rounded">
                            <h5>{{ __('general.private_user_info') }}</h5>
                            <p>{{ __('general.private_user_features') }}</p>
                            <ul>
                                <li>{{ __('general.limited_ads') }}</li>
                                <li>{{ __('general.standard_search') }}</li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('general.advertisements') }}</span>
                    <a href="{{ route('advertisements.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> {{ __('general.new_advertisement') }}
                    </a>
                </div>
                <div class="card-body">
                    <p>{{ __('Plaats je advertenties en bereik kopers op De Bazaar.') }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('advertisements.index') }}" class="btn btn-outline-primary">
                            {{ __('general.my_advertisements') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('general.rentals') }}</span>
                    <a href="{{ route('rentals.create') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> {{ __('general.new_rental') }}
                    </a>
                </div>
                <div class="card-body">
                    <p>{{ __('Verhuur je spullen en verdien extra inkomsten.') }}</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('advertisements.index') }}" class="btn btn-outline-success">
                            {{ __('Mijn advertenties') }}
                        </a>
                        <a href="{{ route('rentals.index') }}" class="btn btn-outline-info">
                            {{ __('Bekijk verhuuraanbod') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection