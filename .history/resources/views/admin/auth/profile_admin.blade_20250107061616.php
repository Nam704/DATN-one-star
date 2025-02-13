@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="profile-bg-picture"
                style="background-image:url('{{ asset('admin/assets/images/bg-profile.jpg') }}')">
                <span class="picture-bg-overlay"></span>
                <!-- overlay -->
            </div>
            <!-- meta -->
            <div class="profile-user-box">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="profile-user-img"><img src="{{ asset('storage/'.$user->profile_image) }}" alt=""
                                class="avatar-lg rounded-circle"></div>
                        <div class="">
                            <h4 class="mt-4 fs-17 ellipsis">
                                @if ($user)
                                {{ $user->name }}
                                @endif
                            </h4>
                            <p class="font-13">{{ $user->role->name }}</p>
                            <p class="text-muted mb-0"><small>
                                    @if ($user)
                                    Address
                                    @endif</small></p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <button type="button" class="btn btn-soft-danger">
                                <i class="ri-settings-2-line align-text-bottom me-1 fs-16 lh-1"></i>
                                Edit Profile
                            </button>
                            <a class="btn btn-soft-info" href="#"> <i class="ri-check-double-fill fs-18 me-1 lh-1"></i>
                                Following</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ meta -->
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card p-0">
                <div class="card-body p-0">
                    <div class="profile-content">
                        <ul class="nav nav-underline nav-justified gap-0">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#aboutme" type="button" role="tab" aria-controls="home"
                                    aria-selected="true" href="#aboutme">About</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#user-activities" type="button" role="tab" aria-controls="home"
                                    aria-selected="true" href="#user-activities">Activities</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" data-bs-target="#edit-profile"
                                    type="button" role="tab" aria-controls="home" aria-selected="true"
                                    href="#edit-profile">Settings</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" data-bs-target="#projects"
                                    type="button" role="tab" aria-controls="home" aria-selected="true"
                                    href="#projects">Projects</a></li>
                        </ul>

                        <div class="tab-content m-0 p-4">
                            <div class="tab-pane active" id="aboutme" role="tabpanel" aria-labelledby="home-tab"
                                tabindex="0">
                                <div class="profile-desk">
                                    <h5 class="text-uppercase fs-17 text-dark">{{ $user->name }}</h5>
                                    <div class="designation mb-4">PRODUCT DESIGNER (UX / UI / Visual
                                        Interaction)</div>
                                    <p class="text-muted fs-16">

                                    </p>

                                    <h5 class="mt-4 fs-17 text-dark">Contact Information</h5>
                                    <table class="table table-condensed mb-0 border-top">
                                        <tbody>

                                            <tr>
                                                <th scope="row">Email</th>
                                                <td>
                                                    <a href="#" class="ng-binding">
                                                        {{ $user->email }}
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th scope="row">Phone</th>
                                                <td class="ng-binding">{{ $user->phone ? $user->phone : "Phone number
                                                    not updated" }}</td>
                                            </tr>


                                        </tbody>
                                    </table>
                                </div> <!-- end profile-desk -->
                            </div> <!-- about-me -->

                            <!-- Activities -->
                            <div id="user-activities" class="tab-pane">
                                <div class="timeline-2">
                                    <div class="time-item">
                                        <div class="item-info ms-3 mb-3">
                                            <div class="text-muted">5 minutes ago</div>
                                            <p><strong><a href="#" class="text-info">John
                                                        Doe</a></strong>Uploaded a photo</p>
                                            <img src="assets/images/small/small-3.jpg" alt="" height="40" width="60"
                                                class="rounded-1">
                                            <img src="assets/images/small/small-4.jpg" alt="" height="40" width="60"
                                                class="rounded-1">
                                        </div>
                                    </div>






                                </div>
                            </div>

                            <!-- settings -->
                            <div id="edit-profile" class="tab-pane">
                                <div class="user-profile-content">
                                    <form>
                                        <div class="row row-cols-sm-2 row-cols-1">
                                            <div class="mb-2">
                                                <label class="form-label" for="FullName">Full
                                                    Name</label>
                                                <input type="text" name="name" value="{{ $user->name }}" id="FullName"
                                                    class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="Email">Email</label>
                                                <input type="email" value="{{ $user->email }}" name="email" id="Email"
                                                    class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="web-url">Old password</label>
                                                <input type="text" value="Enter your old password" name="old_password"
                                                    id="web-url" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="phone">Phone</label>
                                                <input type="text" value="{{ $user->phone }}" name="phone" id="phone"
                                                    class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="new_password"> New Password</label>
                                                <input type="password" placeholder="8 - 15 Characters" id="new_password"
                                                    class="form-control" name="new_password">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="re_password">Re-Password</label>
                                                <input type="password" placeholder="6 - 15 Characters"
                                                    name="re_password" id="re_password" class="form-control">
                                            </div>
                                            <div class="col-sm-12 mb-3">
                                                <label class="form-label" for="AboutMe">Address</label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <select name="province" class="form-select" id="province">
                                                            <option value="" {{ old('province') ? 'selected' : '' }}>
                                                                Chọn tỉnh
                                                            </option>
                                                        </select>
                                                        @error('province')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="district" class="form-select" id="district">
                                                            <option value="" {{ old('district') ? 'selected' : '' }}>
                                                                Chọn quận
                                                            </option>
                                                        </select>
                                                        @error('district')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="ward" class="form-select" id="ward">
                                                            <option value="" {{ old('ward')? 'selected' : '' }}>Chọn
                                                                phường
                                                            </option>
                                                        </select>
                                                        @error('ward')
                                                        <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" type="submit"><i
                                                class="ri-save-line me-1 fs-16 lh-1"></i> Save</button>
                                    </form>
                                </div>
                            </div>

                            <!-- profile -->
                            <div id="projects" class="tab-pane">
                                <div class="row m-t-10">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Project Name</th>
                                                        <th>Start Date</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Assign</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Velonic Admin</td>
                                                        <td>01/01/2015</td>
                                                        <td>07/05/2015</td>
                                                        <td><span class="badge bg-info">Work
                                                                in Progress</span></td>
                                                        <td>Techzaa</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Velonic Frontend</td>
                                                        <td>01/01/2015</td>
                                                        <td>07/05/2015</td>
                                                        <td><span class="badge bg-success">Pending</span>
                                                        </td>
                                                        <td>Techzaa</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Velonic Admin</td>
                                                        <td>01/01/2015</td>
                                                        <td>07/05/2015</td>
                                                        <td><span class="badge bg-pink">Done</span>
                                                        </td>
                                                        <td>Techzaa</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td>Velonic Frontend</td>
                                                        <td>01/01/2015</td>
                                                        <td>07/05/2015</td>
                                                        <td><span class="badge bg-purple">Work
                                                                in Progress</span></td>
                                                        <td>Techzaa</td>
                                                    </tr>
                                                    <tr>
                                                        <td>5</td>
                                                        <td>Velonic Admin</td>
                                                        <td>01/01/2015</td>
                                                        <td>07/05/2015</td>
                                                        <td><span class="badge bg-warning">Coming
                                                                soon</span></td>
                                                        <td>Techzaa</td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

</div>
@endsection
@push('scripts')
<!-- Chart.js -->

<script src="{{ asset('admin/assets/vendor/chart.js/chart.min.js') }}"></script>

<!-- Profile Demo App js -->
<script src="{{ asset('admin/assets/js/pages/profile.init.js') }}"></script>
<script src="{{ asset('admin/api/selectAddress.js') }}"></script>
@endpush