$(document).ready(function () {
    var a = $("#fixed-header-datatable").DataTable({
        responsive: true,
        order: [], // Tắt sắp xếp mặc định
        columnDefs: [
            {
                targets: 0, // Chỉ định cột đầu tiên (index 0)
                orderable: false, // Tắt sắp xếp cho cột này
            },
        ],
        language: {
            paginate: {
                previous: "<i class='ri-arrow-left-s-line'>",
                next: "<i class='ri-arrow-right-s-line'>",
            },
        },
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass(
                "pagination-rounded"
            );
        },
    });

    // Kích hoạt FixedHeader
    new $.fn.dataTable.FixedHeader(a);
});
