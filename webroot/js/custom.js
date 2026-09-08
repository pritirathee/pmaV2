/** @format */

$(document).ready(function () {
    $("#example").DataTable({
        scrollX: true,
        pageLength: 100,
    });

    // $("#Data-table").DataTable({
    //   scrollX:true,
    //   pageLength:100

    // });

    $(".show-description-icon").click(function () {
        // alert();

        // alert("I ran");

        var val = $(this).attr("data-value");

        $(".description-" + val).toggle();

        // $(".description").toggleClass("show");
    });
});

// JQUERY CALANDER
$(function () {
    $(".datepicker").datepicker();
});

$("#langOpt").multiselect({
    columns: 1,
    placeholder: "Select Option",
    search: true,
});
$("#langOpt1").multiselect({
    columns: 1,
    placeholder: "Select Option",
    search: true,
});
$("#langOpt2").multiselect({
    columns: 1,
    placeholder: "Select Option",
    search: true,
});

$("#edit_resource_id").multiselect({
    columns: 1,
    placeholder: "Select Option",
    search: true,
});

$(".custom-multiselect").multiselect({
    columns: 1,
    placeholder: "Select Option",
    search: true,
});

// TOOL TIP
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// AUTO COMPLETE
$(function () {
    var availableTags = ["Kevin", "Spencer", "Dues"];
    $("#tags").autocomplete({
        source: availableTags,
    });
});

$('[data-toggle="toggle"]').change(function () {
    $(this).parents().next(".hide").toggle();
});

$(".collaps-icon").click(function () {
    $("i", this).toggleClass("fa-chevron-down fa-chevron-up");
});

$("#inlineRadio3custom").change(function () {
    if ($(this).prop("checked")) {
        $("#extend_days_id_div").show();
    } else {
        $("#extend_days_id_div").hide();
    }
});

$(".validate-hrs").change(function () {
    var no = parseInt(this.value);
    if (no > 24 || no < 0) {
        alert("Number should be between 0 to 24.");
        this.value = 0;
    }
});

$(".status").change(function () {
    var val = $(this).val();
    var id = $(this).attr("id");
    var type = $(this).attr("data-type");
    var url = $(this).attr("data-url");
    url = url + "companies/status/";
    // url = url + "pma/companies/status/";

    $.ajax({
        url: url + id + "/" + val + "/" + type,
        method: "GET",
        success: function (returnData) {
            location.reload();
        },
    });
});

$(".delete").click(function () {
    //alert($(this).attr('data-delete')); return false;
    var id = $(this).attr("data-id");
    var type = $(this).attr("data-type");
    var url = $(this).attr("data-url");
    $("#p_id").val(id);
    $("#p_url").val(url);
    if (type == "user") {
        $(".modal-title").html("Confirm Delete User");
    } else if (type == "entry") {
        $(".modal-title").html("Confirm Delete Entry");
    }
});

$("#deleteConfirm").click(function () {
    var id = $("#p_id").val();
    var url = $("#p_url").val();
    // console.log(url, id);
    url = url + "companies/deleteProject/";
    $.ajax({
        url: url + id,
        method: "GET",
        success: function (res) {
            // console.log(res);
            // location.reload();
        },
    });
});

$(document).ready(function () {
    $(".changeTime").blur(function () {
        var id = $(this).attr("data-id");
        var resource_id = $(this).attr("data-user");
        var val = $(this).val();
        //  var url = '<?= $this->Url->build("/companies/allotment") ?>';
        var url = $("#url").val() + "companies/allotment";
        // var url = $("#url").val() + "pma/companies/allotment";
        let resDate = $("#resDate").val();
        // console.log(val);
        // console.log(id);
        console.log(resDate);
        $.ajax({
            type: "GET",
            url: `${url}/${id}/${val}/${resource_id}`,
            data: {
                resDate,
            },
            success: function (returnData) {
                var sum = 0;
                $(".hrs_" + id).each(function () {
                    sum += Number($(this).val());
                });
                $(".totalmgr_" + id).val(sum);
            },
        });
    });

    $(".create-notes").on("click", function () {
        var id = $(this).attr("data-id");
        var day = $(this).attr("data-day");
        document.getElementById("milestoneid").value = id;
        document.getElementById("timesheetday").value = day;
        $("#create_notes").modal("show");
    });
    $(".fillhrs").change(function () {
        var id = $(this).attr("data-id");
        var day = $(this).attr("data-day");
        var hrs = $(this).attr("data-hrs");
        var count = $(this).attr("data-count");
        var val = $(this).val() == "" ? 0 : $(this).val();
        var url = $("#url").val();
        url = url + "pmaV2/users/allotment/";
        $.ajax({
            url: url + id + "/" + val + "/" + day,
            type: "GET",
            success: function (returnData) {

            $(".hour" + count + "_" + id).attr("data-hrs", val);

            let totalHours = 0;

            $(".hrs_" + id).each(function () {
                totalHours += Number($(this).val()) || 0;
            });

            $(".totalmgr_" + id).val(totalHours);
        },
        });
    });
});
$(function () {
    $(".cost-inp").on("change", function () {
        if (!KEY) {
            alert("Please enter key first");
            return false;
        }
        var month = $(this).attr("month");
        var year = $(this).attr("year");
        var amount = $(this).val();
        var uid = $(this).attr("u-id");
        $.ajax({
            url: baseUrl + "Salaries/addSalary",
            data: {
                month: month,
                amount: amount,
                year: year,
                user_id: uid,
                key: KEY,
            },
            headers: {
                "X-CSRF-Token": TOKEN,
            },
            method: "post",
            success: function (resp) {
                // console.log(resp);
            },
        });
    });
});

$(".langOpt").multiselect({
    columns: 1,
    placeholder: "Select Training",
    search: true,
});

$(".langOpt1").multiselect({
    columns: 1,
    placeholder: "Select Training",
    search: true,
});
