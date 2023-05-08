$(document).ready(function () {
    $(document).on('click', '.contact-info', function () {
        showLoader();
        axios.get(showAbout)
            .then(function (response) {
                var users = response.data.users;
                var countries = response.data.countries;
                var states = response.data.states;
                var cities = response.data.cities;

                if (users.locations != null) {
                    var countryId = users.locations.country_id;
                } else {
                    var countryId = undefined;
                }
                options('#country_id', countries, countryId);

                if (users.locations != null) {
                    var stateId = users.locations.state_id;
                } else {
                    var stateId = undefined;
                }
                options('#state_id', states, stateId);

                if (users.locations != null) {
                    var cityId = users.locations.city_id;
                } else {
                    var cityId = undefined;
                }
                options('#city_id', cities, cityId);
                if (users.profile_image) {
                   // $("#output").attr('src', response.data.link)
                }
                $("#first_name").val(users.first_name);
                $("#middle_name").val(users.middle_name);
                $("#last_name").val(users.last_name);
                $("#secondary_email").val(users.secondary_email);
                $("#work_email").val(users.work_email);
                $("#mobile").val(users.mobile);
                $("#secondary_mobile").val(users.secondary_mobile);
                $("#whatsapp_mobile").val(users.whatsapp_mobile);
                if (users.locations != null) {
                    $("#area_of_residence").val(users.locations.area_of_residence);
                    $("#zip_code").val(users.locations.zip_code);
                }
                // $(".phone").mask('(000) 000-0000');
                singleSelcet2("#country_id");
                singleSelcet2("#state_id");
                singleSelcet2("#city_id");
                hideLoder();

                $('#contact-info-modal').modal({
                    backdrop: 'static',
                    keyboard: false
                }, 'show');
            }).catch(function (error) {
                console.log(error);
            });
    });
});

var frmprofie = $('#frmprofie').validate({ // initialize the plugin
    rules: {
        secondary_email: {
            email: true,
            notEqual: ("#work_email"),
            remote: {
                url: checkEmail,
                type: "get"
            }
        },
        work_email: {
            email: true,
            notEqual: "#secondary_email",
            remote: {
                url: checkEmail,
                type: "get"
            }
        },
        mobile: {
            required: true,
            // number: true,
            // minlength: 10,
            // maxlength: 10
        },
        secondary_mobile: {
            // number: true,
            // minlength: 10,
            // maxlength: 10
        },
        whatsapp_mobile: {
            // number: true,
            // minlength: 10,
            // maxlength: 10
        },
    },
    messages: {
        secondary_email: {
            remote: "This email is already used."
        },
        work_email: {
            remote: "This email is already used."
        },
        state_id: {
            required: "State of Residence is required."
        },
        city_id: {
            required: "City of Residence is required."
        },
        area_of_residence: {
            required: "Area of Residence is required."
        }
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
        // $(".submit", this).attr("disabled", true).val("Please Wait...");
        var formData = new FormData();
        var imagefile = document.querySelector('#profile_image');

        formData.append("id", userId);
        // formData.append("profile_image", imagefile.files[0]);
        formData.append("profile", $("#profilepost").val());
        formData.append("first_name", $("#first_name").val());
        formData.append("middle_name", $("#middle_name").val());
        formData.append("last_name", $("#last_name").val());
        formData.append("secondary_email", $("#secondary_email").val());
        formData.append("work_email", $("#work_email").val());
        formData.append("mobile", $("#mobile").val());
        formData.append("secondary_mobile", $("#secondary_mobile").val());
        formData.append("whatsapp_mobile", $("#whatsapp_mobile").val());
        formData.append("country_id", $("#country_id").val());
        formData.append("state_id", $("#state_id").val());
        formData.append("city_id", $("#city_id").val());
        formData.append("area_of_residence", $("#area_of_residence").val());
        formData.append("zip_code", $("#zip_code").val());
        axios.post(storeAboutUrl, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
        }).then(function (response) {
            $(".contact-info-ajax").empty();
            $(".contact-info-ajax").append(response.data.html);
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $('#contact-info-modal').modal('hide');
        })
    }
});
jQuery.validator.addMethod("notEqual", function (value, element, param) { // Adding rules for Amount(Not equal to zero)
    return this.optional(element) || value != $(param).val();;
}, "Please use diffrent email");

$('#contact-info-modal').on('hidden.bs.modal', function () {
    var $frmprofie = $('#frmprofie');
    $('#frmprofie')[0].reset();
    $frmprofie.validate().resetForm();
    $frmprofie.find('.invalid-feedback').removeClass('invalid-feedback');
    // $(".phone").unmask();
});
