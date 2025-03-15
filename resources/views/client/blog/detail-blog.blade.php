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

    <!--blog body area start-->
    <div class="blog_details blog_padding mt-23">
        <div class="container">
            <div class="row">

                <div class="col-lg-9 col-md-12">
                    <!--blog grid area start-->
                    <div class="blog_details_wrapper">
                        <div class="blog_thumb" style="margin-bottom: 40px; ">
                            <a href="#"><img src="{{ asset($blog->thumbnail) }}" alt=""
                                    style="width: 830px; height: 300px; object-fit:cover"></a>
                        </div>
                        <div class="blog_content">
                            <h3 class="post_title">{{ $blog->title }}</h3>
                            <div class="post_meta">
                                <span><i class="ion-person"></i> Posted by </span>
                                <span><a href="#">admin</a></span>
                                <span>|</span>
                                <span><i class="fa fa-calendar" aria-hidden="true"></i> Posted on {{ $blog->published_at }}
                                </span>

                            </div>
                            <div class="post_content">
                                <p>{!! $blog->content !!}</p>
                            </div>
                            <div class="entry_content">
                                <div class="post_meta">
                                    <span>Tags: </span>
                                    @foreach ($blog->tags as $tag)
                                        <span><a href="#">, {{ $tag->name }}</a></span>
                                    @endforeach
                                </div>

                                <div class="social_sharing">
                                    <h3>share this post:</h3>
                                    <ul>
                                        <li><a href="#" title="facebook"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="#" title="twitter"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="#" title="pinterest"><i class="fa fa-pinterest"></i></a></li>
                                        <li><a href="#" title="google+"><i class="fa fa-google-plus"></i></a></li>
                                        <li><a href="#" title="linkedin"><i class="fa fa-linkedin"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="related_posts">
                            <h3>Bài viết liên quan</h3>
                            <div class="row">
                                @foreach ($relatedblogs as $related)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="single_related">
                                            <div class="related_thumb">
                                                <img src="{{ asset($blog->thumbnail) }}" alt="image" style="width: 270px; height: 180px; object-fit: cover;">
                                            </div>
                                            <div class="related_content">
                                                <h3><a href="{{route('admin.blogs.show', $related->id)}}">{{$related->title}}</a></h3>
                                                <span><i class="fa fa-calendar" aria-hidden="true"></i> {{$related->published_at}}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--blog grid area start-->
                </div>
                <div class="col-lg-3 col-md-12">
                    <div class="blog_sidebar_widget">
                        <div class="widget_list widget_search">
                            <h3>Search</h3>
                            <form action="#">
                                <input placeholder="Search..." type="text">
                                <button type="submit">search</button>
                            </form>
                        </div>
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
                                        <a href="#"><img src="{{ asset($recent_blog->thumbnail) }}"
                                                alt="image-blog"
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
            </div>
        </div>
    </div>
    <!--blog section area end-->


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
