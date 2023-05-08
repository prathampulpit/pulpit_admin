

function openEducationModal(id) {
    showLoader();
    $(function () {
        var today = new Date();
        var $startDate = $('#start_date');
        var $endDate = $('#end_date');

        $startDate.datepicker({
            format: 'mm/yyyy',
            zIndex: 2048,
            autoHide: true,
            endDate: (today.getMonth() + 1) + '/' + today.getFullYear(),
        });
        $endDate.datepicker({
            autoHide: true,
            format: 'mm/yyyy',
            zIndex: 2048,
            startDate: $startDate.datepicker('getDate'),
            endDate: (today.getMonth() + 1) + '/' + today.getFullYear(),
        });

        $startDate.on('change', function () {
            $endDate.datepicker('setStartDate', $startDate.datepicker('getDate'));
        });
    });
    $("#currently_studing").on("change", function () {
        if ($(this).is(':checked')) {
            $("#end_date").attr("disabled", "disabled");
        } else {
            $("#end_date").removeAttr("disabled");
        }
    })

    if(id != undefined) {
        id = id;
    } else {
        id = null
    }
    axios.get(educationUrl + '/' + id)
        .then(function (response) {
            var education = response.data.education;
            var edit = response.data.edit;

            var qualcatId = undefined;
            options("#qualcat_id", response.data.qualificationCategory, qualcatId);

            if (education.qualification_id != null) {
                var qualificationId = education.qualification_id;
            } else {
                var qualificationId = undefined;
            }
            options("#qualification_id", response.data.quelifications, qualificationId);


            if (education.institute_country_id != null) {
                var instCountryId = education.institute_country_id;
            } else {
                var instCountryId = undefined;
            }
            options("#inst_country_id", response.data.countries, instCountryId);


            if (education.institution_id != null) {
                var institutionId = education.institution_id;
            } else {
                var institutionId = undefined;
            }
            options("#institution_id", response.data.institution, institutionId);


            if (edit == true) {
                $("#qualcat_id").val(response.data.qualCat);
                $("#edu_title").val(education.title);
                $("#start_date").datepicker('setDate', education.start_date);
                $('#edu_id').val(education.id);
                if (education.currently_studing == 1) {
                    $("#currently_studing").attr("checked", "checked");
                    $("#end_date").attr("disabled", "disabled");
                } else {
                    $("#currently_studing").removeAttr("checked");
                    $("#end_date").removeAttr("disabled");
                    if (education.end_date != null) {
                        $("#end_date").datepicker('setDate', education.end_date);
                    }
                }
                $("#edu_btn").html('Update');
                // $("#inst_country_id").attr("disabled", "disabled");
            } else {
                $('#edu_id').val('');
                $("#edu_btn").html('Add');
            }

            singleSelcet2("#qualcat_id");
            singleSelcet2("#inst_country_id");
            singleSelcet2("#qualification_id");
            singleTagsSelcet2("#institution_id");
            hideLoder();
            $("#education-information").modal({
                backdrop: 'static',
                keyboard: false
            }, 'show');

        }).catch(function (error) {
            console.log(error);
        });
}
// $(document).on('change', "#institution_id", function () {
//     axios.get(instituteCountryUrl + '/' + $(this).val()).then(function (response) {
//         $("#inst_country_id").val(response.data.countryId);
//         $('#inst_country_id').select2().trigger('change');
//         $("#inst_country_id").attr("disabled", "disabled");
//     }).catch(function (error) {
//         $("#inst_country_id").removeAttr("disabled");
//         $("#inst_country_id").val();
//         $('#inst_country_id').select2().trigger('change');
//     });
// })


var frmeducation = $("#frmeducation").validate({
    rules: {
        currently_studing: {
            required: {
                depends: function (element) {
                    return ($("#end_date").val() == "" && $("#start_date").val() != "")
                }
            }
        },
        end_date: {
            required: {
                depends: function (element) {
                    return ($("#currently_studing").val() == "" && $("#start_date").val() != "")
                }
            }
        },
    },
    messages: {
        edu_title: "Course Title is required",
        institution_id: "Institution is required",
        qualcat_id: "QualificationCategory is required",
        qualification_id: "Level is required",
        inst_country_id: "Country of Institution is required",
        start_date: "Start date is required",
        end_date: "End date is required",        
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
        if ($("#currently_studing").is(":checked")) {
            var checked = "1";
        } else {
            var checked = "0";
        }
        $(".frmeducationsubmit", this).attr("disabled", true).val("Please Wait...");
        axios.post(storeEducationUrl, {
            id: $("#edu_id").val(),
            title: $("#edu_title").val(),
            institution: $("#institution_id").val(),
            qualificationCategory: $("#qualcat_id").val(),
            qualification: $("#qualification_id").val(),
            countryOfInstitute: $("#inst_country_id").val(),
            start_date: $("#start_date").val(),
            end_date: $("#end_date").val(),
            currently_studing: checked
        }).then(function (response) {
            $(".education-info-ajax").empty();
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $(".education-info-ajax").append(response.data.html);
            $("#education-information").modal("hide");
        })
    }
});
$('#education-information').on('hidden.bs.modal', function () {
    var $alertas = $('#frmeducation');
    $('#frmeducation')[0].reset();
    $alertas.validate().resetForm();
    $alertas.find('.invalid-feedback').removeClass('invalid-feedback');
    $("#start_date").datepicker('destroy');
    $("#end_date").datepicker('destroy');
    $("#end_date").removeAttr("disabled");
    $("#currently_studing").removeAttr("checked");
    $("#inst_country_id").removeAttr("disabled");
});

