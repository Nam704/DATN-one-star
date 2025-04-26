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
                            <li class="menu_item_children categorie_list"><a href="{{ route('client.shop', ['categories' => [$item->id]]) }}">
                                {{ $item->name }} ({{ $item->min_price }}) <i class="fa fa-angle-right"></i>
                            </a>
                                <ul class="categories_mega_menu">
                                    @foreach ($item->children as $child)
                                    <li class="menu_item_children"><a href="{{ route('client.shop', ['categories' => [$child->id]]) }}">
                                        {{ $child->name }}
                                    </a>

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
                    @foreach($banners as $banner)
                    <!-- <div class="single_slider d-flex align-items-center" > -->
                        <div class="slider_content" style="background-image: url('{{ Storage::url($banner->image) }}');">
                            <h2>{{ $banner->title }}</h2>
                            <h1>{{ $banner->description }}</h1>
                            <a class="button" href="{{ route('client.shop') }}">Shopping Now</a>
                        </div>
                    <!-- </div> -->
                    @endforeach
                </div>

            </div>
        </div>
    </div>

</section>
