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
                            <li class="menu_item_children categorie_list"><a href="#">{{ $item->name }} <i
                                        class="fa fa-angle-right"></i></a>
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
                    <div class="single_slider d-flex align-items-center" data-bgimg="assets/img/slider/slider1.jpg">
                        <div class="slider_content">
                            <h2>Top Quality</h2>
                            <h1>Aftermarket Turbocharger Specialist</h1>
                            <a class="button" href="shop.html">shopping now</a>
                        </div>

                    </div>
                    <div class="single_slider d-flex align-items-center" data-bgimg="assets/img/slider/slider2.jpg">
                        <div class="slider_content">
                            <h2>Height - Quality</h2>
                            <h1>The Parts Of shock Absorbers & Brake Kit</h1>
                            <a class="button" href="shop.html">shopping now</a>
                        </div>
                    </div>
                    <div class="single_slider d-flex align-items-center" data-bgimg="assets/img/slider/slider3.jpg">
                        <div class="slider_content">
                            <h2>Engine Oils</h2>
                            <h1>Top Quality Oil For Every Vehicle</h1>
                            <a class="button" href="shop.html">shopping now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>