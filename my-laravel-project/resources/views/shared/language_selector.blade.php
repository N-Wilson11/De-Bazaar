<div class="dropdown">
    <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('general.language') }}: {{ app()->getLocale() == 'nl' ? __('general.dutch') : __('general.english') }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="languageDropdown">
        <li><a class="dropdown-item {{ app()->getLocale() == 'nl' ? 'active' : '' }}" href="{{ route('language.switch', 'nl') }}">{{ __('general.dutch') }}</a></li>
        <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">{{ __('general.english') }}</a></li>
    </ul>
</div>