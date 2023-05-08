

function openExperienceModal(id) {
    showLoader();
    $(function () {
        var today = new Date();
        var $startDate = $('#exp_start_date');
        var $endDate = $('#exp_end_date');

        $startDate.datepicker({
            format: 'mm/yyyy',
            zIndex: 2048,
            autoHide: true,
            date: null,
            endDate: (today.getMonth() + 1) + '/' + today.getFullYear(),
        });
        $endDate.datepicker({
            autoHide: true,
            format: 'mm/yyyy',
            zIndex: 2048,
            date: null,
            startDate: $startDate.datepicker('getDate'),
            endDate: (today.getMonth() + 1) + '/' + today.getFullYear(),
        });

        $startDate.on('change', function () {
            $endDate.datepicker('setStartDate', $startDate.datepicker('getDate'));
        });
    });


    $("#is_current").on("change", function () {
        if ($(this).is(':checked')) {
            $("#exp_end_date").attr("disabled", "disabled");
        } else {
            $("#exp_end_date").removeAttr("disabled");
        }

    });

    if(id != undefined) {
        id = id;
    } else {
        id = null
    }

    axios.get(experienceUrl + '/' + id)
        .then(function (response) {
            var industryList = response.data.industries;
            var jobCategories = response.data.jobCategory;
            var jobLevels = response.data.jobLevel;
            var experience = response.data.experience;
            var edit = response.data.edit;
            var check = response.data.check;

            var industry = undefined;
            options("#insdustry_id", industryList, industry);

            var category = undefined;
            options("#position_category_id", jobCategories, category);

            if (edit == true) {
                var level = experience.position_level_id;
            } else {
                var level = undefined;
            }
            options("#position_level_id", jobLevels, level);

            $("#no_reports").empty();
            for (var i = 0; i <= 251; i++) {
                if (i == 251) {
                    $("#no_reports").append('<option value="' + i + '" >' + i + '+</option>');
                } else {
                    $("#no_reports").append('<option value="' + i + '" >' + i + '</option>');
                }
            }
            if (edit == true) {
                $("#insdustry_id").val(response.data.expIndustries);

                $("#position_category_id").val(response.data.expJobCat);
                var data = {
                    id: experience.commancompanies.id,
                    text: experience.commancompanies.name
                };
                var newOption = new Option(data.text, data.id, true, true);
                $('#companyname').append(newOption).trigger('change');

                if (experience.city != null) {
                    var city_data = {
                        id: experience.city.id,
                        text: experience.city.name
                    };
                    var cityOption = new Option(city_data.text, city_data.id, true, true);
                    $('#exp_city_id').append(cityOption).trigger('change');
                }
                if (experience.country != null) {
                    var country_data = {
                        id: experience.country.id,
                        text: experience.country.name
                    };
                    var countryOption = new Option(country_data.text, country_data.id, true, true);
                    $('#exp_country_id').append(countryOption).trigger('change');
                }

                $("#no_reports").val(experience.no_reports);
                $("#positiontitle").val(experience.positiontitle);
                if (experience.start_date != null) {
                    $("#exp_start_date").datepicker('setDate', new Date(experience.start_date));
                }

                $("#experience_id").val(experience.id);
                if (experience.is_current == 1) {
                    $("#is_current").attr("checked", "checked");
                    $("#exp_end_date").attr("disabled", "disabled");
                } else {
                    $("#is_current").removeAttr("checked");
                    $("#exp_end_date").removeAttr("disabled");
                    if (experience.end_date != null) {
                        $("#exp_end_date").datepicker('setDate', new Date(experience.end_date));
                    }
                }
                $(".frmexperiencesubmit").val("Update");
            } else {
                $(".frmexperiencesubmit").val("Add");
                $("#experience_id").val('');

            }
            $("#insdustry_id").select2({
                placeholder: "Select",
                maximumSelectionLength: 3
            });
            $("#position_category_id").select2({
                placeholder: "Select",
                maximumSelectionLength: 3
            });
            singleSelcet2("#position_level_id");
            singleSelcet2("#no_reports");
            hideLoder();
            $("#experience-information").modal({
                backdrop: 'static',
                keyboard: false
            },"show");
        }).catch(function (error) {
            console.log(error);
        });
}

var frmexperience = $("#frmexperience").validate({
    rules: {
        is_current: {
            required: {
                depends: function (element) {
                    return ($("#exp_end_date").val() == "" && $("#exp_start_date").val() != "")
                }
            }
        },
        exp_end_date: {
            required: {
                depends: function (element) {
                    return ($("#is_current").val() == "" && $("#exp_start_date").val() != "")
                }
            }
        },
        exp_city_id: 'required',
        exp_country_id: 'required',
    },
    messages: {
        companyname: "Company name is required",
        positiontitle: "Position Title is required",
        position_category_id: "Job Category is required",
        position_level_id: "Position Level is required",
        exp_start_date: "Start Date is required",
        exp_end_date: "End Date is required",
        exp_city_id: "City is required",
        exp_country_id: "Country is required"
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
        // console.log($("#exp_end_date").val()); return false;
        if ($("#is_current").is(":checked")) {
            var checked = "1";
        } else {
            var checked = "0";
        }
        $(".frmexperiencesubmit", this).attr("disabled", true).val("Please Wait...");
        axios.post(storeExperienceUrl, {
            id: $("#experience_id").val(),
            companyname: $("#companyname").val(),
            industry_id: $("#insdustry_id").val(),
            positiontitle: $("#positiontitle").val(),
            position_category_id: $("#position_category_id").val(),
            position_level_id: $("#position_level_id").val(),
            exp_country_id: $("#exp_country_id").val(),
            exp_city_id: $("#exp_city_id").val(),
            no_reports: $("#no_reports").val(),
            exp_start_date: $("#exp_start_date").val(),
            end_date: $("#exp_end_date").val(),
            is_current: checked
        }).then(function (response) {
            $(".experience-info-ajax").empty();
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $(".experience-info-ajax").append(response.data.html);
            $("#experience-information").modal("hide");
        })
    }

});
$('#experience-information').on('hidden.bs.modal', function () {
    var $frmexperience = $('#frmexperience');
    $('#frmexperience')[0].reset();
    $frmexperience.validate().resetForm();
    $frmexperience.find('.invalid-feedback').removeClass('invalid-feedback');
    $("#companyname").val("");
    $("#exp_start_date").datepicker('destroy');
    $("#exp_end_date").datepicker('destroy');
    $("#exp_end_date").removeAttr("disabled");
    $("#is_current").removeAttr("checked");
    var data = {
        id: "",
        text: ""
    };
    var newOption = new Option(data.text, data.id, true, true);
    $('#companyname').append(newOption).trigger('change');

    var city_data = {
        id: "",
        text: ""
    };
    var cityOption = new Option(city_data.text, city_data.id, true, true);
    $('#exp_city_id').append(cityOption).trigger('change');

    var country_data = {
        id: "",
        text: ""
    };
    var countryOption = new Option(country_data.text, country_data.id, true, true);
    $('#exp_country_id').append(countryOption).trigger('change');

});

