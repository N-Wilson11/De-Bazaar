<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('general.register') }} - De Bazaar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="d-flex justify-content-end mb-3">
                    @include('shared.language_selector')
                </div>
                
                <div class="card">
                    <div class="card-header">{{ __('general.register') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('general.name') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('general.email') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('general.advertiser_type') }}</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="user_type" id="particulier" value="particulier" {{ old('user_type', 'particulier') === 'particulier' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="particulier">
                                        {{ __('general.private_user') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="user_type" id="zakelijk" value="zakelijk" {{ old('user_type') === 'zakelijk' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="zakelijk">
                                        {{ __('general.business_user') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="user_type" id="normaal" value="normaal" {{ old('user_type') === 'normaal' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="normaal">
                                        {{ __('general.normal_user') }}
                                    </label>
                                </div>
                                @error('user_type')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div id="company_selection" class="mb-3" style="display: none;">
                                <label for="company_id" class="form-label">{{ __('Selecteer een bedrijf') }}</label>
                                <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                    <option value="">{{ __('-- Selecteer een bedrijf --') }}</option>
                                    @foreach(\App\Models\Company::where('is_active', true)->get() as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('general.password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">{{ __('general.confirm_password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="mb-0">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('general.register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    {{ __('general.already_registered') }} <a href="{{ route('login') }}">{{ __('general.login') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Functie om bedrijfsselectie te tonen/verbergen op basis van gebruikerstype
        function toggleCompanySelection() {
            const userType = document.querySelector('input[name="user_type"]:checked').value;
            const companySelection = document.getElementById('company_selection');
            
            if (userType === 'normaal') {
                companySelection.style.display = 'block';
            } else {
                companySelection.style.display = 'none';
                // Reset de waarde wanneer niet zichtbaar
                document.getElementById('company_id').value = '';
            }
        }

        // Event listeners voor radio buttons
        document.querySelectorAll('input[name="user_type"]').forEach(radio => {
            radio.addEventListener('change', toggleCompanySelection);
        });

        // Initiële controle bij het laden van de pagina
        document.addEventListener('DOMContentLoaded', () => {
            toggleCompanySelection();
        });
    </script>
</body>
</html>