<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <div class="card-widgets">
                <a href="javascript:;" onclick="loadCancelledChartData()" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
            </div>
            <h5 class="header-title mb-0">Sản phẩm bị hủy trong ngày</h5>
            <div id="cancelled-products-collapse" class="collapse pt-3 show">
                <canvas id="cancelledProductChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var cancelledChart = null;
    var cancelledCtx = document.getElementById('cancelledProductChart').getContext('2d');

    function loadCancelledChartData(fromDate = null, toDate = null) {
        var params = {};
        if (fromDate) params.start_date = fromDate;
        if (toDate) params.end_date = toDate;

        $.ajax({
            url: "{{ route('admin.productCancelled') }}",
            type: "GET",
            data: params,
            dataType: "json",
            success: function (response) {
                if (cancelledChart) {
                    cancelledChart.destroy();
                }

                if (!response || response.length === 0) {
                    cancelledCtx.clearRect(0, 0, cancelledCtx.canvas.width, cancelledCtx.canvas.height);
                    cancelledCtx.font = '16px Arial';
                    cancelledCtx.fillStyle = "gray";
                    cancelledCtx.textAlign = 'center';
                    cancelledCtx.fillText('Không có sản phẩm nào', cancelledCtx.canvas.width / 2, cancelledCtx.canvas.height / 2);
                    return;
                }

                response.sort((a, b) => b.total_cancelled - a.total_cancelled);

                let labels = response.map(item => item.name);
                let values = response.map(item => item.total_cancelled);
                let colors = labels.map(() =>
                    `rgba(${Math.floor(Math.random() * 180) + 50}, ${Math.floor(Math.random() * 180) + 50}, ${Math.floor(Math.random() * 180) + 50}, 0.8)`
                );

                cancelledChart = new Chart(cancelledCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Số lượng bị hủy',
                            data: values,
                            backgroundColor: colors,
                            borderColor: colors.map(c => c.replace('0.8', '1')),
                            borderWidth: 1,
                            barThickness: 12,
                            categoryPercentage: 0.8,
                            barPercentage: 0.8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: {
                                beginAtZero: true
                            },
                            y: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0,
                                    minRotation: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var sum = context.dataset.data.reduce((a, b) => Number(a) + Number(b), 0);
                                        return `${context.label}: ${context.raw} sản phẩm `;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            error: function (err) {
                console.error("Lỗi khi lấy dữ liệu:", err);
            }
        });
    }

    $(document).ready(function () {
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        const start = today.toISOString().split('T')[0];
        const end = tomorrow.toISOString().split('T')[0];

        loadCancelledChartData(start, end);
    });
</script>
@endpush
