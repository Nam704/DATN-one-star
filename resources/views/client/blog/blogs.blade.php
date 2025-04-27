@extends('client.layouts.home.layout')

@section('title', 'Tin công nghệ')

@section('content')

<style>
    .card img {
        transition: transform 0.4s ease;
        /* Hiệu ứng khi hover */
    }

    .card a:hover img {
        transform: scale(1.1);
        /* Phóng to ảnh khi hover */
    }

    .card-body {
        padding: 15px;
    }
</style>
<!-- Breadcrumbs Start -->
<div class="breadcrumbs_area">
    <div class="container">
        <div class="row" style="margin-top: -20px">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <ul>
                        <li><a href="{{ route('client.home') }}">Trang chủ</a></li>
                        <li>Tin tức</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumbs End -->

<!-- Blog Area Start -->
<div class="blog_page_section blog_sidebar blog_reverse mt-5 mb-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 col-md-12 mb-4">
                <aside class="blog_sidebar_widget">

                    <!-- Danh mục -->
                    <div class="widget_list widget_categories mb-4">
                        <h3>Danh mục</h3>
                        <ul>
                            @foreach ($categoryBlogs as $categoryBlog)
                            <li><a href="#">{{ $categoryBlog->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Tag -->
                    <div class="widget_list widget_tag mb-4">
                        <h3>Thẻ tag</h3>
                        <div class="tag_widget">
                            <ul class="tag_list">
                                @foreach ($tags as $tag)
                                <li><a href="#">{{ $tag->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    <div class="widget_list widget_post">
                        <h3>Bài viết mới</h3>
                        @foreach ($recent_blogs as $recent_blog)
                        <div class="post_wrapper d-flex mb-3">
                            <div class="post_thumb me-3">
                                <a href="{{ route('client.blog.show', $recent_blog->id) }}"><img src="{{ asset($recent_blog->thumbnail) }}" alt="blog-image" style="width: 80px; height: 80px; object-fit:cover; border-radius: 8px;"></a>
                            </div>
                            <div class="post_info">
                                <h6 class="mb-1"><a href="#">{{ $recent_blog->title }}</a></h6>
                                <small class="text-muted">{{ $recent_blog->published_at }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </aside>
            </div>

            <!-- Blog Content -->
            <div class="col-lg-9 col-md-12">
                <div class="row">
                    @foreach ($blogs as $blog)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <a href="{{ route('client.blog.show', $blog->id) }}" class="d-block overflow-hidden rounded-3 position-relative" style="aspect-ratio: 4/3;">
                                <img src="{{ asset($blog->thumbnail) }}"
                                    alt="{{ $blog->title }}"
                                    class="w-100 h-100 rounded-3 position-absolute top-0 start-0 img-hover"
                                    style="object-fit: cover; object-position: center; transition: transform 0.4s ease;">
                            </a>

                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('client.blog.show', $blog->id) }}" class="text-dark">{{ $blog->title }}</a>
                                </h5>
                                <div class="d-flex flex-wrap small mb-2 text-muted">
                                    <div class="me-3"><i class="fa fa-calendar me-1"></i> {{ $blog->published_at }}</div>
                                    <div><i class="fa fa-folder-open me-1"></i> {{ $blog->category->name }}</div>
                                </div>
                                <p class="card-text">
                                    {!! Str::limit(strip_tags($blog->content), 120) !!}
                                </p>
                                <a href="{{ route('client.blog.show', $blog->id) }}" class="btn btn-outline-primary btn-sm mt-2">Đọc thêm</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $blogs->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Blog Area End -->
@endsection