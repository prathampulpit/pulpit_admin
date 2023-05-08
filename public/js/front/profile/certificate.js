

function openCertificateModal(id) {
    showLoader();
    $(function () {
        var today = new Date();
        var $completion_date = $('#completion_date');
        $completion_date.datepicker({
            format: 'mm/yyyy',
            zIndex: 2048,
            autoHide: true,
            endDate: (today.getMonth() + 1) + '/' + today.getFullYear(),
        });
    });

    $("#is_ongoing").on("change", function () {
        if ($(this).is(':checked')) {
            $("#completion_date").attr("disabled", "disabled");
        } else {
            $("#completion_date").removeAttr("disabled");
        }
    });
    if(id != undefined) {
        id = id;
    } else {
        id = null
    }
    axios.get(certificateUrl + '/' + id)
        .then(function (response) {
            var categories = response.data.certCategories;
            var categoriesInstiturion = response.data.institution;
            var userCertificate = response.data.userCertificate;
            var certificateCategory = response.data.certificateCategory;
            var edit = response.data.edit;

            var certificateId = undefined;
            options("#cert_cat_id", categories, certificateId);

            var institutionId = undefined;
            if (userCertificate.institution_id != null) {
                var institutionId = userCertificate.institution_id;
            }
            options("#certinstitution", categoriesInstiturion, institutionId);

            if (edit == true) {
                $("#title").val(userCertificate.name);
                $("#cert_cat_id").val(certificateCategory);
                if (userCertificate.is_ongoing == 1) {
                    $("#is_ongoing").attr("checked", "checked");
                    $("#completion_date").attr("disabled", "disabled");
                } else {
                    $("#is_ongoing").removeAttr("checked");
                    $("#completion_date").removeAttr("disabled");
                    if (userCertificate.end_date != null) {
                        $("#completion_date").datepicker('setDate', userCertificate.end_date);
                    }
                }
                $("#certificate_id").val(userCertificate.id);
                $("#certificatefrm-button").val("Update");
            } else {
                $("#certificate_id").val('');
                $("#certificatefrm-button").val("Add New");
            }
            singleTagsSelcet2("#certinstitution");
            singleSelcet2("#cert_cat_id");



            hideLoder();
            $("#certificate-information").modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');
        }).catch(function (error) {
            console.log(error);
        });
}

var frmecertificates = $("#frmecertificates").validate({
    rules: {
        is_ongoing: {
            required: {
                depends: function (element) {
                    return ($("#completion_date").val() == "")
                }
            }
        },
        completion_date: {
            required: {
                depends: function (element) {
                    return ($("#is_ongoing").val() == "")
                }
            }
        },
    },
    messages: {
        cert_cat_id: "Certificate category is required",
        completion_date: "Completion date is required",
    },
    errorElement: 'span',
    errorClass: 'invalid-feedback',
    submitHandler: function (form) {
        if ($("#is_ongoing").is(":checked")) {
            var checked = "1";
        } else {
            var checked = "0";
        }
        // $(".frmcertificatesubmit").attr("disabled", true).val("Please Wait...");

        axios.post(storeCertificateUrl, {
            id: $("#certificate_id").val(),
            title: $("#title").val(),
            institution: $("#certinstitution").val(),
            cert_cat_id: $("#cert_cat_id").val(),
            completion_date: $("#completion_date").val(),
            is_ongoing: checked
        }).then(function (response) {
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $(".certificate-info-ajax").empty();
            $(".certificate-info-ajax").append(response.data.html);
            $("#certificate-information").modal("hide");
        })
    }
});
$('#certificate-information').on('hidden.bs.modal', function () {
    var $frmecertificates = $('#frmecertificates');
    $('#frmecertificates')[0].reset();
    $frmecertificates.validate().resetForm();
    $frmecertificates.find('.invalid-feedback').removeClass('invalid-feedback');
    $("#completion_date").datepicker('destroy');
    $("#completion_date").datepicker('disabled');
    $("#is_ongoing").removeAttr("checked");
});