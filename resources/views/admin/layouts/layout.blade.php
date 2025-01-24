<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from themes.getappui.com/techzaa/velonic/layouts/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 10 Mar 2024 13:03:29 GMT -->
@include('admin.layouts.header')
{{-- header --}}

<body>
    <!-- Begin page -->
    <div class="wrapper">


        <!-- ========== Topbar Start ========== -->
        @include('admin.layouts.topbar')
        <!-- ========== Topbar End ========== -->


        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.layouts.leftSidebar')

        <!-- ========== Left Sidebar End ========== -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                @yield('content')
                <!-- container -->

            </div>
            <!-- content -->

            <!-- Footer Start -->
            @include('admin.layouts.footer')
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Theme Settings -->
    @include('admin.layouts.themeSetting')
    <!-- Theme Settings end -->

    <!-- Vendor js -->
    <script src="{{ asset('admin/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/axios.min.js') }}"></script>

    <!-- Plugins Js -->
    <script src="{{ asset('admin/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/switchery/switchery.min.js') }}"></script>

    <script src="{{ asset('admin/assets/libs/select2/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/moment/moment.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

    @stack('scripts')


    <!-- App js -->
    <script src="{{ asset('admin/assets/js/app.min.js') }}"></script>

    <!-- Init js-->
    <script src="{{ asset('admin/assets/js/pages/form-advanced.init.js') }}"></script>

    <!-- Thêm JS của Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</body>

<!-- Mirrored from themes.getappui.com/techzaa/velonic/layouts/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 10 Mar 2024 13:03:30 GMT -->

</html>