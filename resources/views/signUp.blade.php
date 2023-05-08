@extends('layout.app')

@section('content')


<div class="hero-section business-page">

<div class="hero-content">
    <div class="container d-flex align-items-center justify-content-center">
        <div class="row w-100">
            <div class="col-sm-12 col-md-6"></div>
            <div class="col-sm-12 col-md-6">
                <div class="content w-100">

                    <div class="heading">Fill up the Details</div>

                    <form action="#" class="business-form">
                        <div class="row">
                            <div class="col-6">
                               <div class="input-box  d-flex align-items-center justify-content-between">
                                <input type="text" placeholder="First Name" class="date">
                                <img src="{{url('/')}}/landing/images/hero-section/People.png" width="20px" height="20px" alt="fpeople">
                               </div>
                            </div>
                            <div class="col-6">
                            <div class="input-box d-flex align-items-center justify-content-between">
                                <input type="text" placeholder="Last Name" class="date">
                                <img src="{{url('/')}}/landing/images/hero-section/People.png" width="20px" height="20px" alt="lpeople">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-box  d-flex align-items-center justify-content-between">
                                <input type="email" placeholder="E-Mail" class="date">
                                <img src="{{url('/')}}/landing/images/icons/Mail.png"  width="20px" height="20px" alt="Mail">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-box  d-flex align-items-center justify-content-between">
                                <input type="tel" placeholder="Phone Number" class="date">
                                <img src="{{url('/')}}/landing/images/icons/Call.png"  width="20px" height="20px" alt="Call">
                            </div>
                        </div>
                        <div class="col-12">

                            <div class="input-box d-flex align-items-center justify-content-between">
                                <input type="text" placeholder="Referral Code" class="date">
                              
                            </div>
                        </div>
                        <div class="col-12">
                            <input type="submit" value="Sign Up" class="btn btn-submit">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="hero-img">
   
            <img src="{{url('/')}}/landing/images/business/banner -01-01-01 1.png" alt="banner -01-01-01 1.png">
</div>

</div>



<div class="footer-section">
<div class="clients-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-6 content">
                <h1>Partners Testimonials</h1>
                <div class="owl-carousel-quotes owl-carousel owl-theme">
                    
                    <div class="quote quote-item">
                        <img src="{{url('/')}}/landing/images/icons/quote.png" alt="quote">
                        <p>I am using this application from past some times and have experienced growth in my business. I would surely recommend other agents to use this service. </p>
                        <p class="by name">- Harpreet Singh</p>
                        <p class="text">Agent</p>
                        
                    </div>
                    
                    <div class="quote quote-item">
                        <img src="{{url('/')}}/landing/images/icons/quote.png" alt="quote">
                        <p> As a driver I have been benefited from this application. Subscription plans are designed to help driver community.</p>
                        <p class="by name">- Mukesh Jani</p>
                        <p class="text">Driver</p>
                        
                    </div>
                    <div class="quote quote-item">
                        <img src="{{url('/')}}/landing/images/icons/quote.png" alt="quote">
                        <p> After using this application, I can take trip from anywhere in India without having fear of loosing money. And this feature gives me confidence while taking trips.</p>
                        <p class="by name">- Vipul Parmar</p>
                        <p class="text">Agent</p>
                        
                    </div>
                    <div class="quote quote-item">
                        <img src="{{url('/')}}/landing/images/icons/quote.png" alt="quote">
                        <p> Travel management feature of Pulpit Mobility helps me organize all my fleets very efficiently and comfortably.</p>
                        <p class="by name">- Himanshu Faldu</p>
                        <p class="text">Travel Agency</p>
                        
                    </div>
                    <div class="quote quote-item">
                        <img src="{{url('/')}}/landing/images/icons/quote.png" alt="quote">
                        <p>  I have been associated with Pulpit Mobility from past some times and unique bidding process helps me to earn more money and negotiate comfortably with others.</p>
                        <p class="by name">- Kirit Jain</p>
                        <p class="text">Driver</p>
                        
                    </div>

                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <img src="{{url('/')}}/landing/images/footer/Images.png" alt="" class="w-100"> 
            </div>
        </div>
    </div>



    <div class="cta-section d-flex align-items-center justify-content-center flex-column">
        <h1>Get Pulpit on Your Device And Start Riding</h1>
        <p>Download Pulpit for riders on your iOS or Android device and start riding now. Get real-time prices and taxi availabilities near you!</p>
        <div class="actions d-flex align-items-center">
            <a href="https://go.pulpitmobility.com/App" target="_blank" class="me-2">
                <img src="{{url('/')}}/landing/images/cta/App Store.png" height="54px" width="172px" alt="app store">
            </a>
            <a href="https://go.pulpitmobility.com/App" target="_blank">
                <img src="{{url('/')}}/landing/images/cta/Play Store.png" height="84px" width="202px" alt="play store">
            </a>
        </div>
    </div>

</div>
</div>



@endsection

@push('js')
<script>
$('.nav-link').removeClass('active');
$('.nav_rider').addClass('active');
</script>

@endpush

