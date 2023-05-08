$(window).on("load", function () {
	$(".sub-menu").before("<span></span>");

	$(".sidebar > ul").each(function () {
		$("li:has(ul) > a").css({ "padding-right": "50px" });
		$(".sidebar > ul > li").click(function () {
			$(this).siblings().find('.sub-menu').slideUp();
			$(this).children('ul').slideToggle();
			$(this).siblings().find('span').removeClass('close-menu');
			$(this).find('span').toggleClass('close-menu');
			$(".close-menu").click(function () {
				$(this).siblings().find(".sub-menu").slideUp();
				$(this).siblings().find('span').removeClass('close-menu');
			});
			$(".sidebar > ul > li").removeClass('active-menu');
			$(".close-menu").parent().addClass('active-menu');
		});
	});
	$(".sidebar-icon").click(function () {
		$("body").toggleClass('sidebar-hide');
	});
	if ($(".sidebar li").hasClass('sub-active')) {
		$(".sub-active").parents('li').addClass('active-link');
	}
	else {
		$(".sidebar li").parents('li').removeClass('active-link');
	}
	if ($(".sidebar li").hasClass("active-link")) {
		$(".sidebar li").removeClass('active-menu');
		$(".active-link").addClass('active-menu');
		$(".active-link").find('span').addClass('close-menu');

	}
	else {
		$(this).removeClass('active-menu');
	}
	$(".active-link").find('ul').slideDown();

	// JS for NProgress Start
	$('body').show();
	$('.version').text(NProgress.version);
	NProgress.start();
	setTimeout(function () { NProgress.done(); $('.fade').removeClass('out'); }, 1000);
	// JS for NProgress End

	// JS for Offline Page Start
	var run = function () {
		var req = new XMLHttpRequest();
		req.timeout = 5000;
		req.open('GET', 'https://dunches.com/designs/empower/branch-coaching/', true);
		req.send();
	}

	// setInterval(run, 3000);
	// JS for Offline Page End


});

$(window).scroll(function () {
	if ($(".footer").is(":visible")) {
		if ($(this).scrollTop()) {
			$('#scroll').fadeIn();
		} else {
			$('#scroll').fadeOut();
		}
	}
	else {
		$('#scroll').hide();
	}
});

$("#scroll").click(function () {
	$("html, body").animate({ scrollTop: 0 }, 500);
});

$(document).ready(function () {
	//$('[data-toggle="tooltip"]').tooltip();

	// JS for highlight column and row on Table hover start
	// $(".table-data table td").hover(function() {
	// 	$(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).addClass('highlight');
	// },
	// function() {
	// 	$(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).removeClass('highlight');
	// });
	// JS for highlight column and row on Table hover end

	// JS for freeze table column start
	// $(".table-responsive").freezeTable({
	// 	'shadow': true,
	// });

	// $(".table-checkable").freezeTable({
	// 	  'columnNum': 2,
	// 	  'shadow': true,
	// });
	// JS for freeze table column end

	// JS for table column filter start
	// $(".filter").click(function () {
	// 	$(this).next(".apply-filter").show();
	// });
	// $(".close-filter").click(function () {
	// 	$(this).parents(".apply-filter").hide();
	// });
	// JS for table column filter end
});

$(function () {

	var start = moment().subtract(29, 'days');
	var end = moment();

	function cb(start, end) {
		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}

	$('#reportrange').daterangepicker({
		startDate: start,
		endDate: end,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	}, cb);

	cb(start, end);
	
	var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
	$('#dob').datepicker({
		format: 'yyyy-mm-dd',
		maxDate: today
	});

	if (/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
		$("body").addClass('safari');
	}


});