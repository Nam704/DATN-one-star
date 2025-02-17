$(document).ready(function () {
    var a = $("#fixed-header-datatable").DataTable({
        responsive: true,
        order: [], // Tắt sắp xếp mặc định
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
