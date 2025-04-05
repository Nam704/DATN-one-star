<div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-widgets">
                            <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        </div>
                        <h5 class="header-title mb-0">Top Least Sold Products Today</h5>
                        <div id="yearly-sales-collapse" class="collapse pt-3 show">
                            <canvas id="myChartProductSold"></canvas>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
</div> <!-- end col-->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctxV2 = document.getElementById('myChartProductSold').getContext('2d');
        var myChartV2 = null;

        function loadChartDataV2(fromDate = null, toDate = null) {
            var params = {};
            if (fromDate) params.start_date = fromDate;
            if (toDate) params.end_date = toDate;

            $.ajax({
                url: "{{ route('admin.topLeastProducts') }}",
                type: "GET",
                data: params,
                dataType: "json",
                success: function (response) {
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
            loadChartDataV2(startDay, endDay);
        });
    </script>
@endpush
