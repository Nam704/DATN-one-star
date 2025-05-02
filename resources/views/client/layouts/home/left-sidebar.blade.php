<section class="slider_section mb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="categories_menu">
                    <div class="categories_title">
                        <h2 class="categori_toggle"> Mục Lục Sản Phẩm</h2>
                    </div>
                    <div class="categories_menu_toggle">
                        <ul style="padding: 0; margin: 0; list-style: none;">
                            {{-- Nhóm: Danh mục --}}
                            <li class="group-title" style="font-weight: bold; margin-top: 10px; padding: 8px 15px; background: #f1f1f1; border-radius: 4px;">
                                 Danh mục
                            </li>
                            @foreach ($category as $item)
                                @if ($item->id_parent == 0)
                                    <li class="categorie_list" style="padding: 5px 15px;">
                                        <a href="{{ route('client.shop', ['categories' => [$item->id]]) }}"style="color: #333; text-decoration: none; display: block; transition: all 0.3s;">
                                            {{ $item->name }} <span style="color: #999;">({{ $item->products_count }})</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Nhóm: Thương hiệu --}}
                            @if ($brands->count())
                                <li class="group-title" style="font-weight: bold; margin-top: 20px; padding: 8px 15px; background: #f1f1f1; border-radius: 4px;">
                                     Thương hiệu
                                </li>
                                @foreach ($brands as $brand)
                                    <li class="categorie_list" style="padding: 5px 15px;">
                                        <a href="{{ route('client.shop', ['brands' => [$brand->id]]) }}"style="color: #333; text-decoration: none; display: block; transition: all 0.3s;">
                                            {{ $brand->name }} <span style="color: #999;">({{ $brand->products_count }})</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-9 col-md-12">

                <div class="slider_area owl-carousel">
                    @foreach ($banners as $banner)
                        <!-- <div class="single_slider d-flex align-items-center" > -->
                        <div class="slider_content"
                            style="background-image: url('{{ Storage::url($banner->image) }}');">
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
