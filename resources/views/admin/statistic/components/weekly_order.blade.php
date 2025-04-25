<div class="col-lg-4">
    <div class="card">
        <div class="card-body">
            <!-- Các icon widget tương tự -->
            <div class="card-widgets">
                <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                <a data-bs-toggle="collapse" href="#weekly-sales-collapse" role="button" aria-expanded="false" aria-controls="weekly-sales-collapse">
                    <i class="ri-subtract-line"></i>
                </a>
                <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
            </div>
            <!-- Tiêu đề hiển thị kèm tháng và năm hiện tại -->
            <h5 class="header-title mb-0">Tổng đơn theo tuần (tháng {{ date('m/Y') }})</h5>
            <div id="weekly-sales-collapse" class="collapse pt-3 show">
                <div dir="ltr">
                    <!-- Container cho biểu đồ, tương tự như yearly-sales-chart -->
                    <div id="weekly-sales-chart" class="apex-charts" data-colors="#3bc0c3,#1a2942,#d1d7d973"></div>
                </div>
                <!-- Dòng summary bên dưới, sẽ hiển thị thông tin của từng tuần -->
                <div class="row text-center" id="weekly-stats-summary">
                    <!-- Các cột tóm tắt sẽ được inject qua JS -->
                </div>
            </div>
        </div> <!-- end card-body-->
    </div> <!-- end card-->
</div>

<!-- Include ApexCharts nếu chưa có -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    function loadWeeklyChart() {
        fetch("{{ route('admin.weeklyOrderStats') }}", {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Tách mảng labels (ví dụ "Tuần 1", "Tuần 2",…) và total đơn theo tuần
                let labels = data.map(item => item.label);
                let totals = data.map(item => item.total);

                // Cấu hình và vẽ biểu đồ bar bằng ApexCharts
                var options = {
                    chart: {
                        type: 'bar',
                        height: 250
                    },
                    series: [{
                        name: 'Số đơn hàng',
                        data: totals
                    }],
                    xaxis: {
                        categories: labels
                    },
                    colors: ['#3bc0c3']
                };

                var chart = new ApexCharts(document.querySelector("#weekly-sales-chart"), options);
                chart.render();

                // Render các cột summary tương tự phần Yearly Sales Report
                let summaryHtml = '';
                data.forEach(item => {
                    summaryHtml += `
                        <div class="col">
                            <p class="text-muted mt-3 mb-2">${item.label}</p>
                            <h4 class="mb-0">${item.total} đơn</h4>
                        </div>
                    `;
                });
                document.getElementById('weekly-stats-summary').innerHTML = summaryHtml;
            })
            .catch(error => console.error('Error:', error));
    }

    // Tải biểu đồ ngay khi trang load
    document.addEventListener("DOMContentLoaded", loadWeeklyChart);
</script>
