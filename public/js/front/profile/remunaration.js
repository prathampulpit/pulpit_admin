$("#monthly_benifites_id").select2({
    placeholder: "Select",
    tags: true,
});
$("#annual_benifites_id").select2({
    placeholder: "Select",
    tags: true,
});
$("#other_benifites_id").select2({
    placeholder: "Select",
    tags: true,

});
$("#currency_country_id").select2({
    placeholder: "Select",
    allowClear: true,
});


function openRemunarationModal() {
    showLoader();
    axios.get(remunerationUrl)
        .then(function (response) {
            var monthly = response.data.monthly;
            var annual = response.data.annual;
            var others = response.data.others;
            var countries = response.data.countries;
            var expectations = response.data.users.expectations;


            var monthlyId = undefined;
            options("#monthly_benifites_id", monthly, monthlyId);

            var annualId = undefined;
            options("#annual_benifites_id", annual, annualId);

            var otherId = undefined;
            options("#other_benifites_id", others, otherId);


            $("#currency_country_id").empty();
            $("#currency_country_id").append('<option value="" hidden>Select</option>')
            jQuery.each(countries, function (i, val) {
                $("#currency_country_id").append('<option value="' + val.id + '" >' + val.currency_symbol + ' - ' + val.name + '</option>');
            });
            if (expectations != null) {
                $("#currency_country_id").val(expectations.country_id);
                $("#current_salary").val(expectations.current_salary);
                $("#ideal_salary").val(expectations.ideal_salary);
                $("#min_expected_salary").val(expectations.min_expected_salary);
                $("#monthly_benifites_id").val(response.data.monthlyIds);
                $("#annual_benifites_id").val(response.data.annualIds);
                $("#other_benifites_id").val(response.data.othersIds);

                $('.money').mask('0,000,000,000,000,000,000');                
            }
        });
    hideLoder();
    $("#remuneration-information").modal({
        backdrop: 'static',
        keyboard: false
    }, "show");
}
var frmremunaration = $("#frmremunaration").validate({

    messages: {
        currency_country_id: "Currency is required",
        current_salary: "Current Salary is required",
        min_expected_salary: "Minimum Expected Salry is required"
    },
    errorElement: 'span',
    errorClass: 'invalid-feedback',
    errorPlacement: function (error, element) {
        if (element.type == 'input') {
            error.insertAfter(element.sibling(a));
        } else {
            if (element.hasClass('select-two')) {
                error.insertAfter(element.next('.select2-container'));
            } else if (element.prop('type') === 'radio') {
                error.appendTo(element.parent().parent());

            } else {
                if (element.is('#fb-userId')) {
                    el = $(".name-opt .dropdown-menu").parent();
                    error.insertAfter(el);
                } else {
                    error.insertAfter(element);
                }
            }
        }
    },
    submitHandler: function (form) {
        // $(".frmrenumarationsubmit", this).attr("disabled", true).val("Please Wait...");
        axios.post(storeExpactationeUrl, {
            currency_country_id: $("#currency_country_id").val(),
            current_salary: $("#current_salary").val(),
            ideal_salary: $("#ideal_salary").val(),
            min_expected_salary: $("#min_expected_salary").val(),
            monthly_benifites_id: $("#monthly_benifites_id").val(),
            annual_benifites_id: $("#annual_benifites_id").val(),
            other_benifites_id: $("#other_benifites_id").val(),
        }).then(function (response) {
            $(".remuneration-info-ajax").empty();
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $(".remuneration-info-ajax").append(response.data.html);
            $("#remuneration-information").modal("hide");
        })
    }
});
$('#remuneration-information').on('hidden.bs.modal', function () {
    var $frmremunaration = $('#frmremunaration');
    $('#frmremunaration')[0].reset();
    $frmremunaration.validate().resetForm();
    $frmremunaration.find('.invalid-feedback').removeClass('invalid-feedback');
    $('.money').unmask();
});