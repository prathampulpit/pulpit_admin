$(window).on("load",function(){
	if (window.matchMedia('(max-width: 767px)').matches){
		$( "#abt-nav" ).hide();
		$( "#abt-nav" ).next('tab-content').children('tab-pane').hide();
		$('.toggle-tab').change(function () {
			$('.job-detail .tab-pane').hide().removeClass('active show');
			$('#'+$(this).val()).show().addClass('active show');
		})
		 $(".wow").removeClass("wow");
	}

	if (window.matchMedia('(max-width: 1199px)').matches) {
		$(".navbar-toggler").click(function(e){
			e.preventDefault();
			$(this).toggleClass("open");
			$("#collapsibleNavbar").toggleClass("menu-show");
			$(".close-menu").removeClass("closed");
			$(".close-menu").nextAll('ul').slideUp();
		});
		$(".nav-link").click(function(){
			$(".navbar-toggler").removeClass("open");
			$("#collapsibleNavbar").removeClass("menu-show, show");
			$(".close-menu").nextAll('ul').slideUp();
		});

		$(".navbar-toggler").click(function(){
			$("body").toggleClass("menu-open");
		});
		$(".nav-link, .overlay").click(function(){
			$("body").removeClass("menu-open");
			$(".navbar-toggler").removeClass("open");
			$(".navbar-toggler").removeClass("open");
			$("#collapsibleNavbar").removeClass("menu-show show");
		});	
	}

	$(".megamenu").each(function(){
		$(".megamenu .nav-item").has('ul').addClass('has-menu').prepend("<span class='close-menu'></span>");
		$(".close-menu").click(function(){
				// $(".close-menu").nextAll('ul').slideUp();
				// $(this).nextAll('ul').slideToggle();

				if($(this).nextAll("ul").is(":visible")){
					$(this).nextAll('ul').slideUp();
					$(".close-menu").removeClass("closed");
					$(this).removeClass("closed");
				}	
				else{
					$(".close-menu").nextAll('ul').slideUp();
					$(this).nextAll('ul').slideDown();
					$(".close-menu").removeClass("closed");
					$(this).addClass("closed");
				}


			});
	});

	$(document).ready(function() {
		var hov = '1';
		$('.has-menu').mouseenter(function(){
			hov = '1';
			$('.has-menu').removeClass("mouse-enter");
			$(this).addClass("mouse-enter");
		});

		$('.has-menu').mouseleave(function(){
			hov = '0';
			setTimeout(function(){
				if(hov=='0'){
					$('.has-menu').removeClass("mouse-enter");
				}
			}, 300);
			$('.has-menu').mouseenter(function(){
				clearTimeout();
			});
		});

	});

	// JS for Testimonial Slider Start
	var owl = $('.em-slider');
	owl.owlCarousel({
		items: 1,
		loop: true,
		margin: 0,
		autoplay: true,
		animateIn: 'fadeIn',
		animateOut: 'fadeOut',
		autoplayTimeout: 3000,
		autoplayHoverPause: true,


	});
	
	// JS for Testimonial Slider End

	// JS for Recruiter Slider Start
	var owlOne = $('.recruit-slider #recruit');
	owlOne.owlCarousel({

		items: 6,
		loop: true,
		margin: 30,
		autoplay: true,
		autoplayTimeout: 2000,
		autoplayHoverPause: true,
		dots: false,
		responsiveClass:true,
		lazyLoad: true,
		responsive:{
			0:{
				items:2
			},
			576:{
				items:3
			},
			768:{
				items:4
			},
			992:{
				items:6
			}
		}

	});



	// JS for Recruiter Slider End

	// JS for Scrollspy Start
	var headerHeight = $(".navbar").outerHeight(true);
	var topPosition = headerHeight + 30 ;

	$('body').scrollspy({target: ".sidebar-menu, .new-box"});
	$(".sidebar-menu a[href*='#'], .new-box").click(function () {
		$("html, body").animate({scrollTop: $($(this).attr("href")).offset().top - topPosition}, 500);
	});
	// JS for Scrollspy End

	// JS for FAQ Start
	$(".faq .title").each(function(){
		$(".active").next().show();
		$(this).click(function(){
			if($(this).next(".content").is(":visible")){
				$(this).next().slideUp();
				$(this).removeClass('active');
			}	
			else{
				$(".faq .content").slideUp();
				$(".faq .title").removeClass('active');
				$(this).next().slideDown();
				$(this).addClass('active');
			}
		});
	});
	// JS for FAQ End

	var wow = new WOW();
	wow.init();

	// JS for Material Design Gauge Start
	// var gauge = new Gauge(document.getElementById("gauge"));
	// gauge.value(0.6);
	// JS for Material Design Gauge End
});

$(window).scroll(function() {
	

	if($(".footer").is(":visible")){
		if ($(this).scrollTop()) {
			$('#scroll').fadeIn();
		} else {
			$('#scroll').fadeOut();
		}	
	}
	else{
		$('#scroll').hide();
	}

	// JS for Sticky sidebar Start
	var headerHeight = $(".navbar").outerHeight(true);
	var topPosition = headerHeight + 30 ;
	$(".sidebar").css("top",topPosition+"px");
	// JS for Sticky sidebar End
});

$("#scroll").click(function () {
	$("html, body").animate({scrollTop: 0}, 500);
});

// Js for select 2 start
$('select.select-two').select2({
	placeholder: 'Select'
});
// Js for select 2 end