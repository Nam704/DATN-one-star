<div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-widgets">
                            <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                        </div>
                        <h5 class="header-title mb-0">Top Product View</h5>

                        <div id="yearly-sales-collapse" class="collapse pt-3 show">
                            <canvas id="myChartProductView"></canvas>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
        var ctxV3 = document.getElementById('myChartProductView').getContext('2d');
        var myChartV3 = null;

        function loadChartDataV3(fromDate = null, toDate = null) {
            var params = {};
            if (fromDate) params.start_date = fromDate;
            if (toDate) params.end_date = toDate;

            $.ajax({
                url: "{{ route('admin.topViewProducts') }}",
                type: "GET",
                data: params,
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if (myChartV3) {
                        myChartV3.destroy();
                    }

                    if (!response || response.length === 0) {
                        ctxV3.clearRect(0, 0, ctxV3.canvas.width, ctxV3.canvas.height);
                        ctxV3.font = '16px Arial';
                        ctxV3.fillStyle = "gray";
                        ctxV3.textAlign = 'center';
                        ctxV3.fillText('Không có sản phẩm nào', ctxV3.canvas.width / 2, ctxV3.canvas.height /
                            2);
                        return;
                    }

                    response.sort((a, b) => b.view - a.view);

                    var labels = response.map(p => p.name);
                    var values = response.map(p => p.view);
                    var backgroundColors = labels.map(() =>
                        `rgba(${Math.floor(Math.random() * 180) + 50}, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                      ${Math.floor(Math.random() * 180) + 50}, 
                                                                                                                                                                                                                                                                                                                                                                                                                                                      ${Math.floor(Math.random() * 180) + 50}, 0.8)`
                    );

                    myChartV3 = new Chart(ctxV3, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Số lượng view',
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
                                            return `${context.label}: ${context.raw} view (${((context.raw / sum) * 100).toFixed(2)}%)`;
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
            loadChartDataV3(startDay, endDay);
        });
    </script>
@endpush
 <!-- end col-->
