<section class="slider_section mb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="accordion" id="sidebarAccordion">
                    <!-- DANH MỤC -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCats">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseCats" style="background-color: rgb(51, 51, 51); color: #fff;">
                                DANH MỤC
                            </button>
                        </h2>
                        <div id="collapseCats" class="accordion-collapse collapse">
                            <div class="accordion-body p-0">
                                <ul class="list-unstyled mb-0"
                                    style="max-height: 168px; overflow-y: auto; border: 1px solid #ddd; padding: 0;">
                                    @foreach ($category as $item)
                                        @if ($item->id_parent == 0)
                                            <li class="categorie_list" style="padding: 5px 15px;">
                                                <a href="{{ route('client.shop', ['categories' => [$item->id]]) }}"
                                                    style="color: #333; text-decoration: none; display: block; transition: all 0.3s;">
                                                    {{ $item->name }} <span
                                                        style="color: #999;">({{ $item->products_count }})</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- THƯƠNG HIỆU -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBrands">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseBrands"
                                style="background-color: rgb(51, 51, 51); color: #fff;">
                                THƯƠNG HIỆU
                            </button>
                        </h2>
                        <div id="collapseBrands" class="accordion-collapse collapse">
                            <div class="accordion-body p-0">
                                <ul class="list-unstyled mb-0"
                                    style="max-height: 168px; overflow-y: auto; border: 1px solid #ddd; padding: 0;">
                                    @foreach ($brands as $brand)
                                        <li class="categorie_list" style="padding: 5px 15px;">
                                            <a href="{{ route('client.shop', ['brands' => [$brand->id]]) }}"
                                                style="color: #333; text-decoration: none; display: block; transition: all 0.3s;">
                                                {{ $brand->name }} <span
                                                    style="color: #999;">({{ $brand->products_count }})</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-lg-9 col-md-12">
                <div class="slider_area owl-carousel">
                    @foreach ($banners as $banner)
                        <div class="single_slider d-flex align-items-center"
                            data-bgimg="{{ Storage::url($banner->image) }}">
                            <div class="slider_content">
                                <h2>{{ $banner->title }}</h2>
                                <h1>{{ $banner->description }}</h1>
                                <a class="button" href="{{ route('client.shop') }}">Xem ngay</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</section>
<script>
    $(document).ready(function() {
        $('.owl-carousel').owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true
        });
    });
</script>
<style>
    .accordion-button::after {
        filter: brightness(0) invert(1);
    }

    .single_slider {
        height: 420px;
        /* hoặc tự co theo ảnh */
        background-size: contain;
        /* hoặc 'cover' nếu bạn muốn full nền */
        background-repeat: no-repeat;
        background-position: center;
        position: relative;
    }

    .slider_content {
        color: #fff;
        text-align: left;
        z-index: 2;
        padding-left: 50px;
    }

    .slider_content h1,
    .slider_content h2 {
        text-shadow: 0 2px 5px rgba(0, 0, 0, 0.6);
        /* Tăng độ rõ chữ trên nền */
    }
</style>
