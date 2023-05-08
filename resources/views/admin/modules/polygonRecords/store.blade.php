@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Polygon Records
@else
Add Polygon Records
@endif

@endsection

@section('content')

<!-- Main Content-->
<style>
#map-canvas {
    height: 90%;
    margin: 0px;
    padding: 0px;
    position: inherit !important;
}

#map-canvas>div {
    position: initial !important;
}
</style>

<div class="main-content side-content pt-0">
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('polygonRecords.polygonRecords') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Forms</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Validation</li>
                    </ol> -->
                </div>
               
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm justify-content-md-center">
                <div class="col-xl-10 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('polygonRecords.edit_polygonRecords') }}
                                </h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('polygonRecords.add_polygonRecords') }}
                                </h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post"
                                action="{{ route('admin.polygonRecords.store',['panel' => Session::get('panel')]) }}"
                                class="parsley-style-1" id="selectForm2" name="selectForm2">
                                @csrf

                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Coordinates: <span class="tx-danger">*</span></p>
                                        <div id="map-canvas" style='width:100%;height:500px;'></div>

                                        <input type="hidden" name="coordinates" id="coordinates"
                                            value="@if(old('coordinates')){{old('coordinates')}}@elseif($id){{$data->coordinates}}@endif">
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Area Name: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control"
                                                value="@if(old('area_name')){{old('area_name')}}@elseif($id){{$data->area_name}}@endif"
                                                name="area_name" placeholder="Enter Area Name" required="" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Circle Radius: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control"
                                                value="@if(old('circle_radius')){{old('circle_radius')}}@elseif($id){{$data->circle_radius}}@endif"
                                                name="circle_radius" placeholder="Enter Area Name" required=""
                                                type="number">
                                        </div>
                                    </div>
                                </div>
                                @php
                                $city_data = App\Models\Cities::all();
                                @endphp
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">City Name: <span class="tx-danger">*</span></p>
                                        <!-- <div class="mg-b-10"> -->
                                        <select class="form-control" name="city_id" id="city">
                                            <option value="" label="Select City" disabled selected>Select City</option>
                                            @foreach($city_data as $city)
                                            <option value="{{ $city->id }}" @if(isset($data)) @if($data->city_id ==
                                                $city->id) selected @endif @endif>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                        <!-- </div> -->
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Service: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input name="service" id="radio1" type="radio" value="1" @if($id)
                                                @if($data->service == '1')
                                            checked @endif @endif>
                                            <label for="radio1">Yes</label>
                                            <input name="service" id="radio2" type="radio" value="0" @if($id)
                                                @if($data->service == '0')
                                            checked @endif @endif>
                                            <label for="radio2">No</label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $coordinates1 = array();
                                $lat0 = '20.5937';
                                $lng0 = '78.9629';
                                $lat1 = '';
                                $lng1 = '';
                                if (!empty($id)) {
                                    $coordinates = $data->coordinates;
                                    $coordinates = str_replace("(", "", $coordinates);
                                    $coordinates = str_replace(")", "", $coordinates);
                                    if (!empty($coordinates)) {
                                        $coordinates_arr = explode(",", $coordinates);
                                        //echo "<pre>"; print_r($coordinates_arr);

                                        $lat0 = trim($coordinates_arr[0]);
                                        $lng0 = trim($coordinates_arr[1]);

                                        $lat1 = trim($coordinates_arr[2]);
                                        $lng1 = trim($coordinates_arr[3]);

                                        $lat2 = trim($coordinates_arr[4]);
                                        $lng2 = trim($coordinates_arr[5]);

                                        if (isset($coordinates_arr[6])) {
                                            $lat3 = trim($coordinates_arr[6]);
                                            $lng3 = trim($coordinates_arr[7]);
                                        }

                                        if (isset($coordinates_arr[8])) {
                                            $lat4 = trim($coordinates_arr[8]);
                                            $lng4 = trim($coordinates_arr[9]);
                                        }

                                        if (isset($coordinates_arr[10])) {
                                            $lat5 = trim($coordinates_arr[10]);
                                            $lng5 = trim($coordinates_arr[11]);
                                        }

                                        if (isset($coordinates_arr[12])) {
                                            $lat6 = trim($coordinates_arr[12]);
                                            $lng6 = trim($coordinates_arr[13]);
                                        }

                                        if (isset($coordinates_arr[14])) {
                                            $lat7 = trim($coordinates_arr[14]);
                                            $lng7 = trim($coordinates_arr[15]);
                                        }

                                        if (isset($coordinates_arr[16])) {
                                            $lat8 = trim($coordinates_arr[16]);
                                            $lng8 = trim($coordinates_arr[17]);
                                        }

                                        //$coordinates_final_arr[] = $coordinates1;
                                    }
                                    //echo "<pre>"; print_r($coordinates1);

                                    $lat0 = trim($coordinates_arr[0]);
                                    $lng0 = trim($coordinates_arr[1]);
                                }

                                $coordinates_json = json_encode($coordinates1);
                                ?>

                                <div class="mg-t-30">
                                    <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
                                    <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="load2"
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row-->
        </div>
    </div>
</div>
<!-- End Main Content-->

@endsection

@push('pageModals')
@endpush

@push('pageJs')
<script>
@if(Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif

$(document).ready(function() {
    $('#city_id').select2({
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("api.cities.search") }}',
            dataType: 'json',
        },
    });
});

function editProfile() {
    $('#editProfile').modal();
}

function submitEditProfile() {
    $("#selectForm2").submit();
}
</script>

<!-- Internal Form-validation js -->
<script src="{{asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
<script src="{{asset('assets/js/form-validation.js') }}"></script>


<!-- Internal Fileuploads js-->
<script src="{{asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

<!-- InternalFancy uploader js-->
<script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=drawing">
</script>
<script>
var map; // Global declaration of the map
var iw = new google.maps.InfoWindow(); // Global declaration of the infowindow
var lat_longs = new Array();
var markers = new Array();
var drawingManager;

function initialize() {
    @if($id)
    var triangleCoords = [{
            lat: <?= $lat0; ?>,
            lng: <?= $lng0; ?>
        },
        {
            lat: <?= $lat1; ?>,
            lng: <?= $lng1; ?>
        },
        {
            lat: <?= $lat2; ?>,
            lng: <?= $lng2; ?>
        },
        @if(isset($lat3)) {
            lat: <?= $lat3; ?>,
            lng: <?= $lng3; ?>
        },
        @endif
        @if(isset($lat4)) {
            lat: <?= $lat4; ?>,
            lng: <?= $lng4; ?>
        },
        @endif
        @if(isset($lat5)) {
            lat: <?= $lat5; ?>,
            lng: <?= $lng5; ?>
        },
        @endif
        @if(isset($lat6)) {
            lat: <?= $lat6; ?>,
            lng: <?= $lng6; ?>
        },
        @endif
        @if(isset($lat7)) {
            lat: <?= $lat7; ?>,
            lng: <?= $lng7; ?>
        },
        @endif
        @if(isset($lat8)) {
            lat: <?= $lat8; ?>,
            lng: <?= $lng8; ?>
        },
        @endif

    ];
    @endif

    var myLatlng = new google.maps.LatLng('<?= $lat0; ?>', '<?= $lng0; ?>');
    var myOptions = {
        zoom: 13,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        },
        polygonOptions: {
            editable: true
        }
    });

    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
        var newShape = event.overlay;
        newShape.type = event.type;
    });

    google.maps.event.addListener(drawingManager, "overlaycomplete", function(event) {
        overlayClickListener(event.overlay);
        $('#coordinates').val(event.overlay.getPath().getArray());
    });

    @if($id)
    createPolygon(triangleCoords);
    @endif
}

function createPolygon(paths) {

    var polygon = new google.maps.Polygon({
        fillColor: '#9D9EA1',
        fillOpacity: 0.6,
        strokeWeight: 1,
        strokeColor: '#000000',
        editable: true,
        draggable: true,
        paths: paths,
        map: map
    });
}

function overlayClickListener(overlay) {
    google.maps.event.addListener(overlay, "mouseup", function(event) {
        $('#coordinates').val(overlay.getPath().getArray());
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

$(function() {
    $('#save').click(function() {
        //iterate polygon vertices?
    });
});
</script>
@endpush