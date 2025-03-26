<div class="row vudovn">

 <!-- số lượng theo danh mục -->
<div class="col-xl-6">
    <div class="card shadow-lg border-0">
        <div class="card-header">
            <h5 class="header-title mb-0">Số lượng sản phẩm theo danh mục</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col"><input type="date" id="fromDateCategory" class="form-control"></div>
                <div class="col"><input type="date" id="toDateCategory" class="form-control"></div>
                <div class="col"><button id="filterCategoryBtn" class="btn btn-success">Apply</button></div>
                <div class="col"><button id="resetCategoryBtn" class="btn btn-success">Clear</button></div>
            </div>
            <div>
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>


    <!-- top sale product -->
    <div class="col-xl-6"> 
        <div class="card shadow-lg border-0">
            <div class="card-header">
                <h5 class="header-title mb-0">Top sale product</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col"><input type="date" id="fromDate" class="form-control"></div>
                    <div class="col">
                        <input type="date" id="toDate" class="form-control">
                    </div>
                    <div class="col">
                        <button id="filterBtn" class="btn btn-success">Apply</button>
                    </div>
                    <div class="col">
                        <button id="resetBtn" class="btn btn-success">Clear</button>
                    </div>
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
                <h5 class="header-title mb-0">Product Sold</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col"><input type="date" id="fromDateV2" class="form-control"></div>
                    <div class="col">
                        <input type="date" id="toDateV2" class="form-control">
                    </div>
                    <div class="col">
                        <button id="filterBtnV2" class="btn btn-success">Apply</button>
                    </div>
                    <div class="col">
                        <button id="resetBtnV2" class="btn btn-success">Clear</button>
                    </div>
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

        // số lượng sản phẩm theo danh mục
       var ctxCategory = document.getElementById('categoryChart').getContext('2d');
var categoryChart = null;

function loadCategoryChart(fromDate = null, toDate = null) {
    var params = {};
    if (fromDate) params.start_date = fromDate;
    if (toDate) params.end_date = toDate;

    $.ajax({
        url: "{{ route('admin.statistics.categoryStatistics') }}",
        type: "GET",
        data: params,
        dataType: "json",
        success: function(response) {
            console.log(response);
            if (categoryChart) {
                categoryChart.destroy();
            }

            if (!response || response.length === 0) {
                ctxCategory.clearRect(0, 0, ctxCategory.canvas.width, ctxCategory.canvas.height);
                ctxCategory.font = '16px Arial';
                ctxCategory.fillStyle = "gray";
                ctxCategory.textAlign = 'center';
                ctxCategory.fillText('Không có dữ liệu', ctxCategory.canvas.width / 2, ctxCategory.canvas.height / 2);
                return;
            }

            var labels = response.map(c => c.name);
            var values = response.map(c => c.total_products);
            var backgroundColors = labels.map(() =>
                `rgba(${Math.floor(Math.random() * 180) + 50}, 
                      ${Math.floor(Math.random() * 180) + 50}, 
                      ${Math.floor(Math.random() * 180) + 50}, 0.8)`
            );

            categoryChart = new Chart(ctxCategory, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số lượng sản phẩm',
                        data: values,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
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
                                    return `${context.label}: ${context.raw} sản phẩm`;
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
    $('#fromDateCategory').val(firstDay.toISOString().split('T')[0]);
    $('#toDateCategory').val(today.toISOString().split('T')[0]);

    loadCategoryChart();

    $('#filterCategoryBtn').on('click', function() {
        if ($('#fromDateCategory').val() > $('#toDateCategory').val()) {
            alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
            return;
        }

        if (!$('#fromDateCategory').val() && !$('#toDateCategory').val()) {
            alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc');
            return;
        }

        loadCategoryChart($('#fromDateCategory').val(), $('#toDateCategory').val());
    });

    $('#resetCategoryBtn').on('click', function() {
        $('#fromDateCategory').val(0);
        $('#toDateCategory').val(0);
        loadCategoryChart();
    });
});


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
                        type: 'pie',
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
                            plugins: {
                                legend: {
                                    position: 'top',
                                    display: true
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            var sum = context.dataset.data.reduce((a, b) => Number(
                                                    a) + Number(b),
                                                0);
                                            return `${context.label}: ${context.raw} units (${((context.raw / sum) * 100).toFixed(2)}%)`;
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

        function loadChartDataV2(fromDate = null, toDate = null) {
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
                                            return `${context.label}: ${context.raw} units (${((context.raw / sum) * 100).toFixed(2)}%)`;
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

            $('#filterBtn').on('click', function() {
                if ($('#fromDate').val() > $('#toDate').val()) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
                    return;
                }

                if (!$('#fromDate').val() && !$('#toDate').val()) {
                    alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc');
                    return;
                }
                loadChartData($('#fromDate').val(), $('#toDate').val());
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
            $('#filterBtnV2').on('click', function() {
                if ($('#fromDateV2').val() > $('#toDateV2').val()) {
                    alert('Ngày bắt đầu không được lớn hơn ngày kết thúc');
                    return;
                }
                if (!$('#fromDateV2').val() && !$('#toDateV2').val()) {
                    alert('Vui lòng chọn ngày bắt đầu và ngày kết thúc');
                    return;
                }
                loadChartDataV2($('#fromDateV2').val(), $('#toDateV2').val());
            });
        });
    </script>
@endpush

