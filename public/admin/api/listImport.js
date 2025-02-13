$(document).ready(function () {
    document
        .getElementById("select-all")
        .addEventListener("change", function () {
            let checkboxes = document.getElementsByClassName("import-checkbox");
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });
    $("#acceppt_select").click(function (e) {
        // e.preventDefault();
        let checkboxes = document.querySelectorAll(".import-checkbox:checked");
        let check = false;

        var ids = Array.from(checkboxes).map(function (checkbox) {
            return checkbox.value;
        });
        // var ids = checkboxes;
        console.log(ids);
        $.ajax({
            type: "post",
            url: "http://127.0.0.1:8000/api/admin/imports/accept-all",
            data: {
                ids: ids,
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                window.location.reload();
            },
            error: function (error) {
                console.log(error);
            },
        });
    });
    $("#reject_select").click(function (e) {
        e.preventDefault();
        let checkboxes = document.querySelectorAll(".import-checkbox:checked");
        let check = false;

        var ids = Array.from(checkboxes).map(function (checkbox) {
            return checkbox.value;
        });
        // var ids = checkboxes;
        console.log(ids);
        $.ajax({
            type: "post",
            url: "http://127.0.0.1:8000/api/admin/imports/reject-all",
            data: {
                ids: ids,
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                window.location.reload();
            },
            error: function (error) {
                console.log(error);
            },
        });
    });
});
