<div class="col-lg-12 d-flex">
    <div class="card flex-fill">
        <div class="card-body d-flex flex-column">
            <table id="dailyStatusTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Trạng thái</th>
                    <th>Tổng đơn</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- DataTables sẽ tự inject --}}
                </tbody>
              </table>

        </div>
    </div> <!-- end card-body-->
</div> <!-- end card-->

<script>
    $(document).ready(function() {
      $('#dailyStatusTable').DataTable({
        processing: true,
        serverSide: false,  // bên này data nhỏ nên không cần serverSide
        ajax: {
          url: "{{ route('admin.dailyStatistics_Dashboard') }}",
          dataSrc: ''        // JSON trả về là 1 mảng, nên dùng ''
        },
        columns: [
          { data: 'status', title: 'Trạng thái' },
          { data: 'total',  title: 'Tổng đơn' }
        ],
        language: {
          emptyTable: "Chưa có đơn hàng nào trong ngày",
          paginate: {
            previous: "<",
            next: ">"
          }
        }
      });
    });
    </script>
