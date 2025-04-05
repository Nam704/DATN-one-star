<div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-widgets">
                            <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        </div>
                        <h5 class="header-title mb-0">Top sale products today</h5>

                        <div id="weeklysales-collapse" class="collapse pt-3 show">
                            <canvas id="myChartTopProduct"></canvas>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // top sale product
        var ctx = document.getElementById('myChartTopProduct').getContext('2d');
        var myChart = null;

        function loadChartData(fromDate = null, toDate = null) {
            var params = {};
            if (fromDate) params.start_date = fromDate;
            if (toDate) params.end_date = toDate;
            $.ajax({
                url: "{{ route('admin.topSaleProducts') }}",
                type: "GET",
                data: params,
                dataType: "json",
                success: function (response) {
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
                                        label: function (context) {
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
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }


        $(document).ready(function () {
            var today = new Date();
            var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const tomorrow = new Date();
            tomorrow.setDate(today.getDate() + 1);
            const startDay = today.toISOString().split('T')[0];
            const endDay = tomorrow.toISOString().split('T')[0];
            loadChartData(startDay, endDay);
        });
    </script>
@endpush
 <!-- end col-->
