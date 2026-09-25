<div class="author-section pb-4">
    <div class="container">
        <div class="section-card">
            <div class="section-header mb-3">
                <h5 class="section-title">জনপ্রিয় লেখক</h5>
            </div>

            <div class="position-relative">
                <div class="swiper carousel"
                    data-items="2"
                    data-xl-items="7"
                    data-lg-items="6"
                    data-md-items="5"
                    data-sm-items="4"
                    data-xs-items="3"
                    data-margin="5"
                    data-dots="false"
                    data-arrows="true">

                    <div class="swiper-wrapper">
                        @foreach ($data as $item)
                            @if($item->category)
                                <div class="swiper-slide">
                                    <a href="{{ route('category.singleCategoryPage', $item->category->id) }}"
                                        class="author-card">
                                        <img loading="lazy"
                                            src="{{ asset($item->category->image) }}"
                                            alt="{{ $item->category->name }}">
                                        <p class="author-card-text">
                                            {{ $item->category->name }}
                                        </p>
                                    </a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                @include('layouts.frontend.partial.svgbutton')
            </div>
        </div>
    </div>
</div>