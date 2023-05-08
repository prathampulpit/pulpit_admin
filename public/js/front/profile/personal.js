
$(document).ready(function () {
    //personal detail section
    var languages = "";
    $(document).on('click', '.update-personal', function () {
        showLoader();
        $(function () {
            var $birth_date = $("#birth_date");
            var today = new Date();
            $birth_date.datepicker({
                autoHide: true,
                format: 'dd/mm/yyyy',
                zIndex: 2048,
                date: null,
                endDate: today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear(),
            });
        });
        axios.get(personalModaUrl)
            .then(function (response) {
                var ethinicity = response.data.ethinicity;
                languages = response.data.languages;
                var maritalStatus = response.data.maritalStatus;
                var liscences = response.data.liscenceList;
                var user = response.data.user;
                var countries = response.data.countries;

                if (user.profiles != null) {
                    var ethnicityId = user.profiles.ethnicity_id;
                } else {
                    var ethnicityId = undefined;
                }
                options("#ethnicity_id", ethinicity, ethnicityId);


                if (user.profiles != null) {
                    var countryId = user.profiles.nationality;
                } else {
                    var countryId = undefined;
                }
                options("#nationality", countries, countryId);

                var languagesId = undefined;
                options("#language1", languages, languagesId);
                options("#language2", languages, languagesId);
                options("#language3", languages, languagesId);

                $('#have_driving_liscence').empty();
                liscences.forEach(function(index, value) {
                    $('#have_driving_liscence').append($('<option  value="' + value + '">' + index + '</option>'))
                });

                $("#no_of_children").empty();
                for (var i = 0; i <= 10; i++) {
                    if (i == 10) {
                        $("#no_of_children").append('<option value="' + i + '" >' + i + '+</option>');
                    } else {
                        $("#no_of_children").append('<option value="' + i + '" >' + i + '</option>');
                    }
                }

                if (user.profiles != null) {
                    var maritalStatusId = user.profiles.marital_status_id;
                } else {
                    var maritalStatusId = undefined;
                }
                options("#marital_status_id", maritalStatus, maritalStatusId);


                jQuery.each(user.languages, function (i, val) {
                    if (i == 0) {
                        $('#language1').val(val.id);
                    }
                    if (i == 1) {
                        $('#language2').val(val.id);
                    }
                    if (i == 2) {
                        $('#language3').val(val.id);
                    }
                });
                if (user.profiles != null) {
                    $("#linkedin_profile").val(user.profiles.linkedin_profile);
                    $("#skype").val(user.profiles.skype);
                    $("#gender").val(user.profiles.gender);
                    $("#birth_date").datepicker('setDate', response.data.birthdate);
                    $("#no_of_children").val(user.profiles.no_of_children);
                    $("#have_vehicle").val(user.profiles.have_vehicle);
                    $("#have_motorcycle").val(user.profiles.have_motorcycle);
                }
                $("#have_driving_liscence").val(response.data.liscenceIds);
                singleSelcet2("#nationality");
                singleSelcet2("#gender");
                singleSelcet2("#marital_status_id");
                singleSelcet2("#no_of_children");
                singleSelcet2("#ethnicity_id");
                singleSelcet2("#language1");
                singleSelcet2("#language2");
                singleSelcet2("#language3");
                singleSelcet2("#have_vehicle");
                singleSelcet2("#have_motorcycle");
                $("#have_driving_liscence").select2({
                    placeholder: "Select",
                });
                hideLoder();
                $("#personal-information").modal({
                    backdrop: 'static',
                    keyboard: false
                },'show');
            }).catch(function (error) {
                // console.log(error);
            });
    });

    $("#language1").on('change', function () {
        $("#language2").empty();
        $("#language2").append($('<option  value="" hidden>Select</option>'));
        languages.forEach(function(item) {
            if ($("#language1").val() != item.id) {
                $('#language2').append($('<option>', {
                    value: item.id,
                    text: item.name,
                }))
            }
        });
    })
    $("#language2").on('change', function () {
        $("#language3").empty();
        $("#language3").append($('<option  value="" hidden>Select</option>'));
        languages.forEach(function(item) {
            if ($("#language2").val() != item.id) {
                if ($("#language1").val() != item.id) {
                    $('#language3').append($('<option>', {
                        value: item.id,
                        text: item.name,
                    }))
                }
            }
        });
    })
    //end personal detail section


    $('#frmmodalpersonal').validate({
        messages: {
            nationality: "Nationality is required", 
            gender: "Gender is required",
            birth_date : "Birth date is required",            
            language1 : "Language 1 is required",
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
            $(".submit", this).attr("disabled", true).val("Please Wait...");
            axios.post(storePersonalUrl, {
                nationality: $("#nationality").val(),
                gender: $("#gender").val(),
                birth_date: $("#birth_date").val(),
                linkedin_profile: $("#linkedin_profile").val(),
                skype: $("#skype").val(),
                marital_status_id: $("#marital_status_id").val(),
                // marital_status_id: $("#marital_status_id").val(),
                no_of_children: $("#no_of_children").val(),
                ethnicity_id: $("#ethnicity_id").val(),
                language1: $("#language1").val(),
                language2: $("#language2").val(),
                language3: $("#language3").val(),
                have_driving_liscence: $("#have_driving_liscence").val(),
                have_vehicle: $("#have_vehicle").val(),
                have_motorcycle: $("#have_motorcycle").val(),
            }).then(function (response) {
                $(".personal-info-ajax").empty();
                $("#progressBarVal").attr("value", response.data.score);
                $("#progressBarText").html(response.data.score);
                if (response.data.score == 100) {
                    $(".note").addClass('d-none');
                } else {
                    $(".note").removeClass('d-none');
                }
                $(".personal-info-ajax").append(response.data.html);
                $("#personal-information").modal('hide');
            })
        }
    });
    $('#personal-information').on('hidden.bs.modal', function () {
        var $frmpersonal = $('#frmmodalpersonal');
        $('#frmmodalpersonal')[0].reset();
        $frmpersonal.validate().resetForm();
        $frmpersonal.find('.invalid-feedback').removeClass('invalid-feedback');
        $("#birth_date").datepicker('destroy');

    });
});

