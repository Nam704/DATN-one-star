<div class="col-lg-8">
    <div class="card">
        <div class="card-body">
            <!-- Các icon điều khiển trên góc -->
            <div class="card-widgets">
                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                <!-- Sử dụng collapse riêng với id riêng cho widget đơn hàng -->
                <a data-bs-toggle="collapse" href="#dailystatus-collapse" role="button" aria-expanded="false" aria-controls="dailystatus-collapse">
                    <i class="ri-subtract-line"></i>
                </a>
                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
            </div>
            <!-- Tiêu đề widget -->
            <h5 class="header-title mb-0">Đơn hàng theo trạng thái trong ngày (ngày {{ date('d/m/Y') }})</h5>
            <!-- Nội dung widget có thể collapse được -->
            <div id="dailystatus-collapse" class="collapse pt-3 show">
                <!-- Vùng hiển thị biểu đồ bằng canvas của Chart.js -->
                <canvas id="dailyStatusChart" width="200" height="100"></canvas>

                <!-- Dòng summary bên dưới (tùy chọn): ví dụ hiển thị từng trạng thái và số đơn -->
                <div class="row text-center mt-3" id="daily-stats-summary">
                    <!-- Dữ liệu sẽ được render từ JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js từ CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Hàm render bảng tổng hợp dưới biểu đồ dựa trên dữ liệu trả về
    function renderDailyStatsSummary(data) {
        let summary = '';
        data.forEach(item => {
            summary += `
                <div class="col">
                    <p class="text-muted mt-2 mb-1">${item.status}</p>
                    <h5 class="mb-0">${item.total} đơn</h5>
                </div>
            `;
        });
        document.getElementById('daily-stats-summary').innerHTML = summary;
    }

    // Gọi API để lấy dữ liệu đơn hàng trong ngày và vẽ biểu đồ
    fetch("{{ route('admin.dailyStatistics_Dashboard') }}", {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Tách mảng label và total từ dữ liệu trả về
        var statusLabels = data.map(item => item.status);
        var statusValues = data.map(item => item.total);

        // Vẽ biểu đồ với Chart.js
        var ctx = document.getElementById('dailyStatusChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Số đơn hàng',
                    data: statusValues,
                    backgroundColor: '#3bc0c3'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Render summary bên dưới biểu đồ
        renderDailyStatsSummary(data);
    })
    .catch(error => console.error('Error fetching daily statistics:', error));
</script>
