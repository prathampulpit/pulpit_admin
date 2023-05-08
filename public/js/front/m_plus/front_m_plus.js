
var marker;
var map;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 7,
        center: {lat: -6.369028, lng: 34.888821}
    });
}

function addMarker(){
    var geocoder = new google.maps.Geocoder();
    geocodeAddress(geocoder, map);
}

function geocodeAddress(geocoder, resultsMap) {
    var address = $('#city_id').find('option:selected').data('value');
    geocoder.geocode({'address': address}, function(results, status) {
        if (status === 'OK') {
            resultsMap.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: resultsMap,
                draggable: true,
                animation: google.maps.Animation.DROP,
                position: results[0].geometry.location
            });

            latitude = marker.getPosition().lat();
            longitude = marker.getPosition().lng();
            getAddress(latitude,longitude);
                       
            google.maps.event.addListener(marker, 'dragend', function (event) {
                latitude = this.getPosition().lat();
                longitude = this.getPosition().lng();
                
                getAddress(latitude,longitude);
            });
        }
    });
}

function getAddress(latitude,longitude) {

    LocationUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng="+ latitude +","+ longitude +"&key="+ googleMapApi +"";
               
    axios.get(LocationUrl)
    .then(function (response) {
        userAddress = response.data.results[0];
        $('#address').val(JSON.stringify(userAddress));
    });
}


$(document).ready(function(){
    $('.personal_insurance_companies').hide();
    $('.fuel_stations_select2').hide();
    $('.business_insurance_companies').hide();

});

$('.js-example-basic-multiple').select2({
    placeholder: "Select",
});

$('#add_extra_phone_number').on('click',function(){

    $teleOperators = window['teleOperators'];

    $options = [];
    $.each($teleOperators,function(key,value){
        $options.push('<option value="'+ value.id +'">'+ value.name +'</option>')
    });

    $numberOfClass = $('.parent_network_operator').length;
    if($numberOfClass <  2){

        $phoneNumberHtml = '<div class="row parent_network_operator">\
                                <div class="col-md-6">\
                                    <div class="form-group btm-arrow select">\
                                        <label class="control-label" for="network_operator_id"></label>\
                                        <select class="form-control" id="network_operator_id" name="network_operator_id[]">\
                                            <option value="" hidden>Select Network Operator</option>'
                                            + $options +
                                        '</select>\
                                    </div>\
                                </div>\
                                <div class="col-md-6">\
                                    <div class="form-group add-wrapper">\
                                        <label class="control-label"></label>\
                                        <div class="row">\
                                            <div class="col-md-9">\
                                                <input type="text" class="form-control" placeholder="Enter Phone Number" id="phone_number" name="phone_number[]">\
                                            </div>\
                                            <a href="javascript:void(0)" class="btn add-row close_phone_number" title="Add New" id="close_phone_number"><i class="material-icons text-dark">close</i></a>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>';

        $('.phone_number_div').append($phoneNumberHtml);

        if($numberOfClass == '1') {
            $('.add_extra_phone_number').hide();
        }
    }
});

$(document).on('click', '.close_phone_number',function(){
    $numberOfClass = $('.parent_network_operator').length;
    if($numberOfClass < 3){
        $('.add_extra_phone_number').show();
    }

    $(this).parents('.parent_network_operator').remove();
});

$('input[name="insure_personal_item"]').on('click',function(){

    if($(this).val() === '1'){
        $('.personal_insurance_companies').show();
    } else {
        $('#insurances').val('').trigger('change');
        $('.personal_insurance_companies').hide();
    }
});

$('input[name="have_personal_vehicle"]').on('click',function(){
    if($(this).val() === '1'){
        $('.fuel_stations_select2').show();
    } else {
        $('#fuel_stations').val('').trigger('change');
        $('.fuel_stations_select2').hide();
    }
});

$('input[name="have_insure_business"]').on('click',function(){
    if($(this).val() === '1'){
    $('.business_insurance_companies').show();
    } else {
        $('#business_insurances').val('').trigger('change');
        $('.business_insurance_companies').hide();
    }
});

$('#business_description').on('keyup',function(){
    $length = $('#business_description').val().length;
    $('.businessDescriptionTextCount').text($length);
});