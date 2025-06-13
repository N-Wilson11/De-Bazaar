<!-- Teruggebrachte huuropties sectie -->
@if(!empty($returnedRentals) && $returnedRentals->count() > 0)
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-box-arrow-in-left me-2"></i>{{ __('Teruggebrachte Huurproducten') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Huurperiode') }}</th>
                            <th>{{ __('Teruggebracht op') }}</th>
                            <th>{{ __('Acties') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnedRentals as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->advertisement && $item->advertisement->images->count() > 0)
                                            <img src="{{ Storage::url($item->advertisement->images->first()->path) }}" 
                                                class="img-thumbnail me-2" 
                                                alt="{{ $item->title }}" 
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light text-center me-2" style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <span>{{ $item->title }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->rental_start_date)->format('d-m-Y') }} -
                                    {{ \Carbon\Carbon::parse($item->rental_end_date)->format('d-m-Y') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->returned_at)->format('d-m-Y H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('rentals.return-details', $item) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye me-1"></i>{{ __('Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
