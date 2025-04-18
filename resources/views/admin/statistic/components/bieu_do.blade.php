<div class="row vudovn">
    <!-- top sale product -->
    <div class="col-xl-6"> 
        <div class="card shadow-lg border-0">
            <div class="card-header">
                <h5 class="header-title mb-0">Top 10 sản phẩm bán chạy</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col"><input type="date" id="fromDate" class="form-control"></div>
                    <div class="col">
                        <input type="date" id="toDate" class="form-control">
                    </div>
                    <div class="col">
                        <button id="filterBtn" class="btn btn-success">Lọc</button>
                    </div>
                    <div class="col">
                        <button id="resetBtn" class="btn btn-success">Xóa</button>
                    </div>
                </div>
                 <div class="col">
                    <a id="exportLinkTop" href="{{ route('admin.statistics.exportTopSaleProducts') }}" class="btn btn-primary" style="margin-top: 10px;"><i class="ri-file-excel-2-line"></i>Xuất Excel</a>
                    </div>
                <div>
                    <canvas id="myChartTopProduct"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- sản phẩm đã bán -->
    <div class="col-xl-6">
        <div class="card shadow-lg border-0">
            <div class="card-header">
                <h5 class="header-title mb-0">Sản phẩm đã bán</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col"><input type="date" id="fromDateV2" class="form-control"></div>
                    <div class="col">
                        <input type="date" id="toDateV2" class="form-control">
                    </div>
                    <div class="col">
                        <button id="filterBtnV2" class="btn btn-success">Lọc</button>
                    </div>
                    <div class="col">
                        <button id="resetBtnV2" class="btn btn-success">Xóa</button>
                    </div>
                </div>
                <div class="col">
                    <a id="exportLinkSold" href="{{ route('admin.statistics.exportproductSold') }}" class="btn btn-primary" style="margin-top: 10px;"><i class="ri-file-excel-2-line"></i>Xuất Excel</a>
                    </div>
                <div>
                    <canvas id="myChartProductSold"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // top sale product
        var ctx = document.getElementById('myChartTopProduct').getContext('2d');
        var myChart = null;

        var ctxV2 = document.getElementById('myChartProductSold').getContext('2d');
        var myChartV2 = null;

        function loadChartData(fromDate = null, toDate = null) {
            var params = {};
            if (fromDate) params.start_date = fromDate;
            if (toDate) params.end_date = toDate;

            $.ajax({
                url: "{{ route('admin.statistics.topSaleProducts') }}",
                type: "GET",
                data: params,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (myChart) {
                        myChart.destroy();
                    }

                    if (!response || response.length === 0) {
                        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                        ctx.font = '16px Arial';
                        ctx.fillStyle = "gray";
                        ctx.textAlign = 'center';
                        ctx.fillText('Không có sản phẩm nào', ctx.canvas.width / 2, ctx.canvas.height / 2);
                        return;
                    }

                    response.sort((a, b) => b.total_sold - a.total_sold);

                    var labels = response.map(p => p.name);
                    var values = response.map(p => p.total_sold);
                    var backgroundColors = labels.map(() =>
                        `rgba(${Math.floor(Math.random() * 180) + 50}, 
                              ${Math.floor(Math.random() * 180) + 50}, 
                              ${Math.floor(Math.random() * 180) + 50}, 0.8)`
                    );
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Số lượng bán',
                                data: values,
                                backgroundColor: backgroundColors,
                                borderColor: backgroundColors.map(color => color.replace('0.8',
                                    '1')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var sum = context.dataset.data.reduce((a, b) => Number(
                                                    a) + Number(b),
                                                0);
                                            return `${context.label}: ${context.raw} sản phẩm (${((context.raw / sum) * 100).toFixed(2)}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }
// sản phẩm đã bán
        function loadChartDataV2(fromDate = null, toDate = null) {
            var params = {};
            if (fromDate) params.start_date = fromDate;
            if (toDate) params.end_date = toDate;

            $.ajax({
                url: "{{ route('admin.statistics.productSold') }}",
                type: "GET",
                data: params,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (myChartV2) {
                        myChartV2.destroy();
                    }

                    if (!response || response.length === 0) {
                        ctxV2.clearRect(0, 0, ctxV2.canvas.width, ctxV2.canvas.height);
                        ctxV2.font = '16px Arial';
                        ctxV2.fillStyle = "gray";
                        ctxV2.textAlign = 'center';
                        ctxV2.fillText('Không có sản phẩm nào', ctxV2.canvas.width / 2, ctxV2.canvas.height /
                            2);
                        return;
                    }

                    response.sort((a, b) => b.total_sold - a.total_sold);

                    var labels = response.map(p => p.name);
                    var values = response.map(p => p.total_sold);
                    var backgroundColors = labels.map(() =>
                        `rgba(${Math.floor(Math.random() * 180) + 50}, 
                              ${Math.floor(Math.random() * 180) + 50}, 
                              ${Math.floor(Math.random() * 180) + 50}, 0.8)`
                    );

                    myChartV2 = new Chart(ctxV2, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Số lượng bán',
                                data: values,
                                backgroundColor: backgroundColors,
                                borderColor: backgroundColors.map(color => color.replace('0.8',
                                    '1')),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var sum = context.dataset.data.reduce((a, b) => Number(
                                                    a) + Number(b),
                                                0);
                                            return `${context.label}: ${context.raw} sản phẩm (${((context.raw / sum) * 100).toFixed(2)}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        $(document).ready(function() {
            var today = new Date();
            var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            $('#fromDate').val(firstDay.toISOString().split('T')[0]);
            $('#toDate').val(today.toISOString().split('T')[0]);
            $('#fromDateV2').val(firstDay.toISOString().split('T')[0]);
            $('#toDateV2').val(today.toISOString().split('T')[0]);
            loadChartData();
            loadChartDataV2();

            // $('#filterBtn').on('click', function() { 
            //     if ($('#fromDate').val() > $('#toDate').val()) {
            //         alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
            //         return;
            //     }

            //     if (!$('#fromDate').val() && !$('#toDate').val()) {
            //         alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc');
            //         return;
            //     }
            //     loadChartData($('#fromDate').val(), $('#toDate').val());
            // });

            // Cập nhật link Export Excel cho Top Sale Product khi nhấn Apply
            $('#filterBtn').on('click', function() {
                var fromDate = $('#fromDate').val();
                var toDate = $('#toDate').val();
                if (!fromDate || !toDate) {
                    alert('Vui lòng chọn đầy đủ ngày');
                    return;
                }
                if (fromDate > toDate) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
                    return;
                }
                var baseUrl = "{{ route('admin.statistics.exportTopSaleProducts') }}";
                var newHref = baseUrl + '?start_date=' + encodeURIComponent(fromDate) + '&end_date=' + encodeURIComponent(toDate);
                $('#exportLinkTop').attr('href', newHref);
                loadChartData(fromDate, toDate);
            });

            $('#resetBtn').on('click', function() {
                $('#fromDate').val(0);
                $('#toDate').val(0);
                loadChartData();
            });
            $('#resetBtnV2').on('click', function() {
                $('#fromDateV2').val(0);
                $('#toDateV2').val(0);
                loadChartDataV2();
            });
            // Cập nhật link Export Excel cho Product Sold khi nhấn Apply
            $('#filterBtnV2').on('click', function() {
                var fromDate = $('#fromDateV2').val();
                var toDate = $('#toDateV2').val();
                if (!fromDate || !toDate) {
                    alert('Vui lòng chọn đầy đủ ngày');
                    return;
                }
                if (fromDate > toDate) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
                    return;
                }
                var baseUrl = "{{ route('admin.statistics.exportproductSold') }}";
                var newHref = baseUrl + '?start_date=' + encodeURIComponent(fromDate) + '&end_date=' + encodeURIComponent(toDate);
                $('#exportLinkSold').attr('href', newHref);
                loadChartDataV2(fromDate, toDate);
            });
        });
    </script>
@endpush

