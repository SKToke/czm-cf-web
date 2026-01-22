@php
    use Carbon\Carbon;
@endphp
@if($nisab)
    <div class="card nisab-card">
        <div class="card-header">
            <h5 class="nisab-title">Nisab Value</h5>
        </div>
        <div class="nisab-card-body">
            <p>
                Today's Nisab (zakat threshold) for 21 Carat GOLD and Traditional SILVER (cadmium) per gram are*
            </p>
            <div class="price-info">
                <h6>GOLD</h6>
                <div class="price">
                    BDT {{ number_format($nisab->gold_price, 0) }}
                </div>
                <p>This is equal to 85 grams at BDT {{ number_format($nisab->gold_value, 0) }} per gram</p>

                <h6 class="mt-40">SILVER</h6>
                <div class="price">
                    BDT {{ number_format($nisab->silver_price, 0) }}
                </div>
                <p>This is equal to 595 grams at BDT {{ number_format($nisab->silver_value, 0) }} per gram</p>
            </div>
        </div>
        <div class="card-footer">
            <small>*Last Updated on: {{ Carbon::parse($nisab->nisab_update_date)->format('d M Y') }}</small>
            <h5>Need some help?</h5>
            <div class="text-center w-100 mt-30 mb-40">
                <a href="{{ route('zakat-faq') }}" class="faq-btn">Learn more about Zakat</a>
            </div>
        </div>
    </div>
@endif
