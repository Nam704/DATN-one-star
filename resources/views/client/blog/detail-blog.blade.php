@extends('client.layouts.home.layout')
@section('title', 'Tin công nghệ')

@section('content')
<style>
    /* Bài viết chi tiết */
    .blog_details_wrapper {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background-color: #fff;
    }

    .blog_thumb img {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .post_title {
        font-size: 32px;
        font-weight: 700;
        color: #333;
    }

    .post_meta span {
        font-size: 14px;
        color: #888;
    }

    .post_content {
        font-size: 16px;
        line-height: 1.7;
        color: #555;
    }

    .social_sharing ul {
        padding-left: 0;
    }

    .social_sharing ul li {
        display: inline-block;
        margin-right: 10px;
        font-size: 18px;
    }

    /* Related posts */
    .related_posts h3 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .single_related {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background-color: #f9f9f9;
    }

    .related_thumb img {
        border-radius: 8px;
        object-fit: cover;
        height: 200px;
    }

    .related_content h5 {
        font-size: 18px;
        font-weight: 500;
        color: #333;
    }

    .widget_list {
        margin-bottom: 30px;
    }

    .widget_list h5 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .widget_categories ul {
        list-style-type: none;
        padding: 0;
    }

    .widget_categories ul li {
        margin-bottom: 10px;
    }

    .widget_categories ul li a {
        font-size: 16px;
        color: #555;
        text-decoration: none;
    }

    .widget_tag ul li {
        display: inline-block;
        margin-right: 10px;
    }

    .widget_tag ul li a {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        text-decoration: none;
    }
</style>
<!--breadcrumbs area start-->
<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <ul>
                        <li><a href="{{route('client.home')}}">Trang chủ</a></li>
                        <li>Tin tức</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--breadcrumbs area end-->

<!-- Blog details area start-->
<div class="blog_details blog_padding mt-5">
    <div class="container">
        <div class="row">
            <!-- Main content area -->
            <div class="col-lg-9 col-md-12">
                <div class="blog_details_wrapper">
                    <div class="blog_thumb mb-5 ">
                        <a href="#"><img src="{{ asset($blog->thumbnail) }}" alt="" class="img-fluid rounded-3 shadow-sm" style="height: 450px; object-fit: cover;"></a>
                    </div>
                    <div class="blog_content">
                        <h3 class="post_title mb-3">{{ $blog->title }}</h3>
                        <div class="post_meta mb-4">
                            <span><i class="ion-person"></i> <a href="#">admin</a></span>
                            <span class="mx-2">|</span>
                            <span><i class="fa fa-calendar"></i> Posted on {{ $blog->published_at }}</span>
                        </div>
                        <div class="post_content">
                            <p>{!! $blog->content !!}</p>
                        </div>
                        <div class="entry_content mt-5">
                            <div class="post_meta">
                                <span>Tags: </span>
                                @foreach ($blog->tags as $tag)
                                <span><a href="#" class="badge bg-primary text-white">{{ $tag->name }}</a></span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Social sharing -->
                        <div class="social_sharing mt-4">

                            <ul class="social-share list-inline">
                                <li class="list-inline-item"><a href="#" title="Facebook" class="social-icon facebook"><i class="fab fa-facebook"></i></a></li>
                                <li class="list-inline-item"><a href="#" title="Twitter" class="social-icon twitter"><i class="fab fa-twitter"></i></a></li>
                                <li class="list-inline-item"><a href="#" title="Pinterest" class="social-icon pinterest"><i class="fab fa-pinterest"></i></a></li>
                                <li class="list-inline-item"><a href="#" title="Google+" class="social-icon google-plus"><i class="fab fa-google-plus"></i></a></li>
                                <li class="list-inline-item"><a href="#" title="LinkedIn" class="social-icon linkedin"><i class="fab fa-linkedin"></i></a></li>
                            </ul>


                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <div class="related_posts mt-5">
                    <h3>Bài viết liên quan</h3>
                    <div class="row">
                        @foreach ($relatedblogs as $related)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="single_related">
                                <div class="related_thumb text-center">
                                    <a href="{{ route('client.blog.show', $related->id) }}">
                                        <img src="{{ asset($related->thumbnail) }}" alt="related" class="img-fluid rounded-3" style="height: 200px; object-fit: cover;">
                                    </a>
                                </div>
                                <div class="related_content mt-2">
                                    <h5><a href="{{ route('client.blog.show', $related->id) }}">{{ $related->title }}</a></h5>
                                    <small><i class="fa fa-calendar"></i> {{ $related->published_at }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar area -->
            <div class="col-lg-3 col-md-12">
                <div class="blog_sidebar_widget">
                    <div class="widget_list widget_search mb-4">
                        <h5>Search</h5>
                        <form action="#" method="GET">
                            <input type="text" name="search" class="form-control mb-3" placeholder="Search...">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </form>
                    </div>

                    <div class="widget_list widget_categories mb-4">
                        <h5>Danh mục</h5>
                        <ul>
                            @foreach ($categoryBlogs as $categoryBlog)
                            <li><a href="">{{ $categoryBlog->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="widget_list widget_tag mb-4">
                        <h5>Thẻ tag</h5>
                        <div class="tag_widget">
                            <ul class="list-inline">
                                @foreach ($tags as $tag)
                                <li class="list-inline-item"><a href="#" class="badge bg-info text-white">{{ $tag->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="widget_list widget_post mb-4">
                        <h5>Bài viết mới</h5>
                        @foreach ($recent_blogs as $recent_blog)
                        <div class="post_wrapper d-flex mb-3">
                            <div class="post_thumb me-3">
                                <a href="{{ route('client.blog.show', $recent_blog->id) }}">
                                    <img src="{{ asset($recent_blog->thumbnail) }}" alt="recent-blog" class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;">
                                </a>
                            </div>
                            <div class="post_info">
                                <h6><a href="{{ route('client.blog.show', $recent_blog->id) }}">{{ $recent_blog->title }}</a></h6>
                                <small>{{ $recent_blog->published_at }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog details area end -->
@endsection