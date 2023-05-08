@extends('admin.layouts.main')

@section('title')
Bubble Text User Details
@endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec detail-page">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('bubble_text_messages.users') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.users.index',['panel' => Session::get('panel')]) }}">{{ @trans('bubble_text_messages.list') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('bubble_text_messages.detail') }}</li>
                </ol>
            </nav>
            
            

            <div class="detail-content mt-4">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#trans">{{ @trans('bubble_text_messages.users') }}</a>
                    </li>
                </ul>

              <!-- Tab panes -->
                <div class="tab-content box">
                    <div id="trans" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-lg-12">
                                
                                <h3 class="detail-heading">{{ @trans('bubble_text_messages.details') }}</h3>

                                <div class="table-data" v-if="items.length">
                                    <div class="table-responsive table-checkable">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    
                                                    <th>{{ @trans('bubble_text_messages.image') }}</th>
                                                    <th>{{ @trans('bubble_text_messages.username') }}<a href="javascript:void(0)" :class="classSort('username')" @click="sort('username')"></a>
                                                    </th>
                                                    <th>{{ @trans('bubble_text_messages.email') }}<a href="javascript:void(0)" :class="classSort('email')" @click="sort('email')"></a></th>
                                                    <th>{{ @trans('bubble_text_messages.mobile_number') }}<a href="javascript:void(0)" :class="classSort('account_number')" @click="sort('account_number')"></a>
                                                    </th>
                                                    <th>{{ @trans('bubble_text_messages.account_number') }}<a href="javascript:void(0)" :class="classSort('trans_type')" @click="sort('trans_type')"></a>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach($users as $val)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td><span class="can-img rounded-pic">
                                                        <img src="{{$file_path}}/{{$val['profile_picture']}}">
                                                    </span></td>
                                                    <td>{{ $val['name'] }}</td>

                                                    <td>{{ $val['email'] }}</td>

                                                    <td>•••• {{ substr($val['mobile_number'], -4) }}</td>    
                                                    <td>•••• {{ substr($val['account_number'], -4) }}</td>
                                                    
                                                </tr>
                                                @endforeach
                                                <?php $i++; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

<!-- Image View Modal -->
<div class="modal fade" id="attachModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Image View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <img id="image-gallery-image" src="">
            </div>
        </div>
    </div>
</div>
@push('pageJs')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
function getImage(imageName){
    $('#image-gallery-image').attr('src', imageName);
}
@if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
@endif

$('#reset_btn').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
    }, 10000);

    var id = $('#user_id').val();
    var siteurl = "{{url(Session::get('panel').'/users/resetAttempt')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            if(response == 'success'){
                $('#reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
            $this.button('reset');
        }
    });
});


$('#ussd_status_btn').on('click', function() {
    /* var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
    }, 10000); */

    var id = $('#user_id').val();
    var ussd_enable = $('#ussd_enable').val();
    var siteurl = "{{url(Session::get('panel').'/users/changeUssdStatus')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id,
            "ussd_enable":ussd_enable
        },
        success: function(response) {
            if(response == 'success'){

                if(ussd_enable == 1){
                    $('#ussd_enable_lable').text('On');
                    $('#ussd_enable').val('0');
                    $('.ussd-enable').text('Disable');        
                }else{
                    $('#ussd_enable_lable').text('Off');
                    $('#ussd_enable').val('1'); 
                    $('.ussd-enable').text('Enable');  
                }

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'USSD status change successfully.',
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
            //$this.button('reset');
        }
    });
});
</script>
@endpush