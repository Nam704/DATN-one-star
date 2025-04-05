<section class="slider_section mb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="categories_menu">
                    <div class="categories_title">
                        <h2 class="categori_toggle">Browse categories</h2>
                    </div>
                    <div class="categories_menu_toggle">
                        <ul>
                            @foreach ($categories as $item)
                                @if ($item->id_parent == 0)
                                    <li class="menu_item_children categorie_list"><a href="#">{{ $item->name }}
                                            ({{ $item->min_price }}) <i class="fa fa-angle-right"></i></a>
                                        <ul class="categories_mega_menu">
                                            @foreach ($item->children as $child)
                                                <li class="menu_item_children"><a href="#">{{ $child->name }}</a>

                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach



                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="slider_area owl-carousel">
                    @foreach ($homeSlides as $slide)
                        {{-- Ảnh chính --}}
                        <div class="single_slider d-flex align-items-center"
                            data-bgimg="{{ asset($slide->primaryImage->image) }}">
                            <div class="slider_content">
                                <h2>{{ $slide->title }}</h2>
                                <h1>{!! $slide->description !!}</h1>
                                <a class="button" href="#">shopping now</a>
                            </div>
                        </div>

                        {{-- Ảnh phụ nếu có --}}
                        @foreach ($slide->secondaryImages as $image)
                            <div class="single_slider d-flex align-items-center"
                                data-bgimg="{{ asset($image->image) }}">
                                <div class="slider_content">
                                    <h2>{{ $slide->title }}</h2>
                                    <h1>{!! $slide->description !!}</h1>
                                    <a class="button" href="#">shopping now</a>
                                </div>
                            </div>
                        @endforeach

                    @endforeach
                </div>

            </div>
        </div>
    </div>

</section>
