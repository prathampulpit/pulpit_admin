$(document).ready(function () {

    jQuery.validator.setDefaults({
        errorElement: 'span',
        errorClass: 'invalid-feedback',
        ignore: false,
        errorPlacement: function (error, element) {
            if (element.hasClass('dateselect')) {
                error.insertAfter(element.parent('.date'))
            } else if (element.hasClass('time_picker')) {
                error.insertAfter(element.parent('.time'))
            } else if (element.parent('.input-group').length ||
                element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parents('.form-group'));
            } else if (element.prop('type') == 'file') {
                error.insertAfter(element.parents('.browse-btn'));
            } else if (element.hasClass('editor')) {
                error.insertAfter(element.next());
            } else if (element.is("textarea")) {
                error.insertAfter(element);
            } else if (element.hasClass('select-two')) {
                error.insertAfter(element.next());
            } else if (element.hasClass('hasClose')) {
                error.insertAfter(element.next('a'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid'); // add the Bootstrap error class to the control group
        },
        unhighlight: function (element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        success: function (element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid'); // remove the Boostrap error class from the control group
        },
        focusInvalid: false, // do not focus the last invalid input
    });


    /******************************************/
    // Show progrss while axios is running
    /******************************************/
    axios.interceptors.request.use(config => {
        NProgress.start()
        return config
    })

    axios.interceptors.response.use(response => {
        NProgress.done()
        return response
    })

    /******************************************/
    // Confirm on logout
    /******************************************/
    $('#btnLogoutYes').click(function () {
        $('#frmLogout').submit();
    });

    /******************************************/
    // Init Tooltip
    /******************************************/
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        placement: 'top'
    })

    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.preview').attr('src', e.target.result);
                $('.preview').parents('.previewDiv').show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function documentUpload(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $(input).parents('.custom-file').nextAll('.filename').first().find('.control-label').text(input.files[0].name);
                //$(input).parents('.custom-file').next('.filename').find('lable').text(input.files[0].name);
                //$('.filename').val(input.files[0].name);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('.fileupload').on('change', function () { readFile(this); });

    $(document).on('change', '.documentUpload,.documentUploadTitle', function () { documentUpload(this); });

    $(document).on('click', '.documentUploadTitle', function (e) {
        var title = $(this).parents('.custom-file').next('div').find('.title').val();
        if (title == "") {
            $(this).parents('.custom-file').next('div').find('.invalid-feedback').remove();
            var erroElement = '<span class="invalid-feedback">Title is required</span>';
            $(erroElement).insertAfter($(this).parents('.custom-file').next('div').find('.title'));
            e.preventDefault();
            return false;
        }
        $(this).parents('.custom-file').next('div').find('.invalid-feedback').remove();
        return true;
    });

    function companyTaxes(countryId) {
        axios.get(taxesListUrl + '/' + countryId, {

        })
            .then(function (response) {
                this.data = response.data;
                this.data.forEach((item) => {

                    var formDiv = '<div class="col-md-6 newDivs">';
                    formDiv += '<div class="form-group"><label class="control-label">' + item.tax_title + '</label>';
                    formDiv += '<input type="text" class="form-control" name="taxDetails[' + item.tax_title + ']" value="" placeholder="Enter ' + item.tax_title + ' " />';
                    formDiv += '</div></div>';

                    $(".col-md-6:last").after(formDiv);
                });
            })
    }
});

var stateUrl = base_url + "/states/showByCountryId";
var cityUrl = base_url + "/city/showByStateId";
var regisatrationListUrl = base_url + "/companies/CountryRegistrationList";
var taxesListUrl = base_url + "/companies/CountryTaxesList";
var contactsByParamsUrl = base_url + '/contacts/contactsByParams';

function companyRegistrations(countryId) {
    axios.get(regisatrationListUrl + '/' + countryId, {

    })
        .then(function (response) {
            this.data = response.data;
            this.data.forEach((item) => {
                var formDiv = '<div class="col-md-6 newDivs">';
                formDiv += '<div class="form-group"><label class="control-label">' + item.registration_title + '</label>';
                formDiv += '<input type="text" class="form-control" name="registrationDetails[' + item.registration_title + ']" value="" placeholder="Enter ' + item.registration_title + ' Number" />';
                formDiv += '</div></div>';
                $(".col-md-6:last").after(formDiv);
            });
        })
}

function stateList(countryId, selected) {

    axios.get(stateUrl, {
        params: {
            'id': countryId
        },
    })
        .then(function (response) {
            results = response.data;
            if (results != "") {
                $("#state_id").empty();
                $("#state_id").append('<option value="" hidden>Select State</option>');
                $.each(results, function (id, data) {
                    $("#state_id").append('<option value="' + data.id + '">' + data.name + '</option>');
                });

            } else {
                $("#state_id").empty();
                $("#state_id").append('<option value="" hidden>Select State</option>');
            }

            if (selected != undefined)
                $('#state_id').val(selected);

        })
        .catch(function (error) {
        });
}

function cityList(stateId, selected) {
    axios.get(cityUrl, {
        params: {
            'id': stateId
        },
    })
        .then(function (response) {
            results = response.data;
            if (results != "") {
                $("#city_id").empty();
                $("#city_id").append('<option value="" hidden>Select City</option>');
                $.each(results, function (id, data) {
                    $("#city_id").append('<option value="' + data.id + '">' + data.name + '</option>');
                });

            } else {
                $("#city_id").empty();
                $("#city_id").append('<option value="" hidden>Select City</option>');
            }

            if (selected != undefined)
                $('#city_id').val(selected);
        })
        .catch(function (error) {
        });

}

function contactList(data, selected) {
    axios.get(contactsByParamsUrl, {
        params: data
    })
        .then(function (response) {
            results = response.data;
            if (results != "") {
                $("#contact_id").empty();
                $("#contact_id").append('<option value="">Select Contact</option>');
                $.each(results, function (id, data) {
                    $("#contact_id").append('<option value="' + data.id + '">' + data.first_name + ' ' + data.last_name + '</option>');
                });

            } else {
                $("#contact_id").empty();
                $("#contact_id").append('<option value="">Select Contact</option>');
            }

            if (selected != undefined)
                $('#contact_id').val(selected);
        })
        .catch(function (error) {
        });

}