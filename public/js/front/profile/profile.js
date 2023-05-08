$(document).ready(function () {

    // $(".js-example-basic-multiple").select2({
    //     placeholder: "Select",
    // });
    // $(".js-example-basic-single").select2({
    //     tags: true,
    //     placeholder: "Select",
    //     allowClear: true,
    // });

    $(document).on('change', '#country_id', function () {
        $('#city_id').empty();
        stateList($(this).val(), stateUrl);

    });
    $(document).on('change', '#state_id', function () {
        cityList($(this).val(), cityUrl);

    });

    // common functions
    function stateList(countryId, stateUrl) {
        axios.get(stateUrl, {
            params: {
                id: countryId,
            }
        }).then(function (response) {
            this.data = response.data;
            $('#state_id').empty();
            this.data.forEach(function(item) {
                $('#state_id').append($('<option>', {
                    value: item.id,
                    text: item.name
                }))
            });
        });
    }

    function cityList(stateId, cityUrl) {
        axios.get(cityUrl, {
            params: {
                id: stateId,
            }
        }).then(function (response) {
            this.data = response.data;
            $('#city_id').empty();
            this.data.forEach(function(item) {
                $('#city_id').append($('<option>', {
                    value: item.id,
                    text: item.name
                }))
            });
        });
    }
});


