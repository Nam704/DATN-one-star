@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Default Tabs</h4>
                            <p class="text-muted mb-0">Simple widget of tabbable panes of local content.</p>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a href="#home" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        All
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                        New
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#settings" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        Settings
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane" id="home">
                                    <p>Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula
                                        eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
                                        montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu,
                                        pretium quis, sem. Nulla consequat massa quis enim.</p>
                                    <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In
                                        enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu
                                        pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi.
                                        Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                        eleifend ac, enim.</p>
                                </div>
                                <div class="tab-pane show active" id="profile">
                                    <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo,
                                        rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis
                                        pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean
                                        vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                        eleifend ac, enim.</p>
                                    <p class="mb-0">Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean
                                        commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis
                                        parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec,
                                        pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                </div>
                                <div class="tab-pane" id="settings">
                                    <p>Food truck quinoa dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula
                                        eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
                                        montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu,
                                        pretium quis, sem. Nulla consequat massa quis enim.</p>
                                    <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In
                                        enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu
                                        pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi.
                                        Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae,
                                        eleifend ac, enim.</p>
                                </div>
                            </div>
                        </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col -->

            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">List Order</h4>
                    </div>

                    <div class="card-body">

                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <div class="row">
                                <select name="status" id="statuses" class="col-3">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                <div class="col-2">
                                    <button class="accept-all">Accept</button>
                                    <button class="reject-all">Reject</button>
                                </div>
                            </div>

                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="select-all"></th>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody class="order_list">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><input type="checkbox" class="select-all"></th>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

@push('scripts')
    <x-admin.data-table-scripts />
    @vite('resources/js/order.js')
@endpush
