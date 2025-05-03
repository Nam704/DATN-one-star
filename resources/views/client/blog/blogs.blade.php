@extends('client.layouts.home.layout')
@section('title', 'Tin công nghệ')

@section('content')
    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">
            <div class="row" style="margin-top: -20px">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="index.html">Trang chủ</a></li>
                            <li>Blogs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!--blog area start-->
    <div class="blog_page_section blog_sidebar blog_reverse mt-23">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-12">
                    <div class="blog_sidebar_widget">
                        <div class="widget_list widget_categories">
                            <h3>Danh mục</h3>
                            <ul>
                                @foreach ($categoryBlogs as $categoryBlog)
                                    <li><a href="#">{{ $categoryBlog->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="widget_list widget_tag">
                            <h3>Thẻ tag</h3>
                            <div class="tag_widget">
                                <ul>
                                    @foreach ($tags as $tag)
                                        <li><a href="#">{{ $tag->name }}</a></li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                        <div class="widget_list widget_post">
                            <h3>Recent Posts</h3>
                            @foreach ($recent_blogs as $recent_blog)
                                <div class="post_wrapper">
                                    <div class="post_thumb">
                                        <a href="#"><img src="{{ asset($recent_blog->thumbnail) }}" alt="image-blog"
                                                style="height: 70px; width: 70px; object-fit:cover ;"></a>
                                    </div>
                                    <div class="post_info">
                                        <h3><a href="#">{{ $recent_blog->title }}</a></h3>
                                        <span>{{ $recent_blog->published_at }} </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
                <div class="col-lg-9 col-md-12">
                    <div class="blog_wrapper">
                        @foreach ($blogs as $blog)
                            <div class="single_blog">
                                <div class="blog_thumb" >
                                    <a href="{{ route('client.blog.show', $blog->id) }}"><img class="blog_thumb_img" src="{{ asset($blog->thumbnail) }}" alt="img" style="width: 339px; height: 239px; object-fit: cover;"></a>
                                </div>
                                <div class="blog_content">
                                    <h3><a href="{{ route('client.blog.show', $blog->id) }}">{{$blog->title}}</a></h3>
                                    <div class="blog_meta">
                                        <span class="post_date"><i class="fa-calendar fa me-1"></i>{{$blog->published_at}}</span>
                                        <span class="author"><i class="fa fa-user-circle"></i> Posts by : admin</span>
                                        <span class="category">
                                            <i class="fa fa-folder-open"></i>
                                            <a href="{{ route('client.blog.show', $blog->id) }}">{{$blog->category->name}}</a>
                                        </span>
                                    </div>
                                    <div class="blog_desc">
                                        <p>{!! Str::limit(strip_tags($blog->content), 200) !!}</p>
                                    </div>
                                    <div class="readmore_button">
                                        <a href="{{ route('client.blog.show', $blog->id) }}">Đọc thêm</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--blog area end-->


    <!--call to action start-->
    <section class="call_to_action">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="call_action_inner">
                        <div class="call_text">
                            <h3>We Have <span>Recommendations</span> for You</h3>
                            <p>Take 30% off when you spend $150 or more with code Autima11</p>
                        </div>
                        <div class="discover_now">
                            <a href="#">discover now</a>
                        </div>
                        <div class="link_follow">
                            <ul>
                                <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                                <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                                <li><a href="#"><i class="ion-social-googleplus"></i></a></li>
                                <li><a href="#"><i class="ion-social-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--call to action end-->
@endsection
