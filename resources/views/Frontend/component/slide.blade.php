@php
    use App\Enums\SlideEnum;
    $slideKeyword = SlideEnum::MAIN->value; // ✅ lấy "main-slide"
@endphp

@if(!empty($slides[$slideKeyword]['item']))
<div class="panel-slide page-setup" data-setting="{{ json_encode($slides[$slideKeyword]['setting'] ?? []) }}">
    <div class="swiper-container">
        <div style="display: none" class="swiper-button-next"></div>
        <div style="display: none" class="swiper-button-prev"></div>
        <div class="swiper-wrapper">
            @foreach($slides[$slideKeyword]['item'] ?? [] as $key => $val)
                <div class="swiper-slide">
                    <div class="slide-item">
                        <span class="image img-cover">
                            <img class="img-cover" src="{{ $val['image'] }}" alt="{{ $val['name'] }}">
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>
</div>
@endif
