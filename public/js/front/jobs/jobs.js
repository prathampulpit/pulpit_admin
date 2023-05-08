$(document).ready(function () {
    $('.js-data-example-ajax').on('change', function () {
        $cityId = $(this).val();
        axios.post($baseUrl + '/empower-jobs/filters', {
            industryId: $("#industry").val(),
            level: $("#level").val(),
            categoryId: $("#job_category").val(),
            city: $("#location").val(),
        }).then(function (response) {
            var categories = response.data.categories;
            var industries = response.data.industries;
            var levels = response.data.levels;
            if ($("#job_category").val() != "") {
                var category_id = $("#job_category").val();
            } else {
                var category_id = undefined;
            }

            if ($("#industry").val() != "") {
                var industry_id = $("#industry").val();
            } else {
                var industry_id = undefined;
            }
            var levelId = $("#level").val();

            options("#job_category", categories, category_id);

            options("#industry", industries, industry_id);

            $("#level").empty();
            $("#level").append('<option value="" hidden>Select</option>')
            jQuery.each(levels, function (i, val) {
                $("#level").append('<option value="' + val.level.id + '" >' + val.level.name + '</option>');
            });
            if (levelId != "") {
                $("#level").val(levelId);
            } else {
                $("#level").val();
            }
        });
    });


    $("#industry").on("change", function () {
        $industryId = $(this).val();
        var levelId = $("#level").val();
        axios.post($baseUrl + '/empower-jobs/filters', {
            industryId: $("#industry").val(),
            level: $("#level").val(),
            categoryId: $("#job_category").val(),
            city: $("#location").val()
        }).then(function (response) {
            var categories = response.data.categories;
            var levels = response.data.levels;
            if ($("#job_category").val() != "") {
                var category_id = $("#job_category").val();
            } else {
                var category_id = undefined;
            }
            options("#job_category", categories, category_id);

            $("#level").empty();

            $("#level").append('<option value="" hidden>Select</option>')
            jQuery.each(levels, function (i, val) {
                $("#level").append('<option value="' + val.level.id + '" >' + val.level.name + '</option>');
            });
            if (levelId != "") {
                $("#level").val(levelId);
            } else {
                $("#level").val();
            }
        });
    });

    $("#job_category").on("change", function () {
        $categoryId = $(this).val();
        axios.post($baseUrl + '/empower-jobs/filters', {
            industryId: $("#industry").val(),
            level: $("#level").val(),
            categoryId: $("#job_category").val(),
            city: $("#location").val(),
        }).then(function (response) {
            var industries = response.data.industries;
            var levels = response.data.levels;
            var levelId = $("#level").val();

            if ($("#industry").val() != "") {
                var industry_id = $("#industry").val();
            } else {
                var industry_id = undefined;
            }
            options("#industry", industries, industry_id);

            $("#level").empty();

            $("#level").append('<option value="" hidden>Select</option>')
            jQuery.each(levels, function (i, val) {
                $("#level").append('<option value="' + val.level.id + '" >' + val.level.name + '</option>');
            });
            if (levelId != "") {
                $("#level").val(levelId);
            } else {
                $("#level").val();
            }
        });
    });
    $("#level").on("change", function () {
        $levelId = $(this).val();
        axios.post($baseUrl + '/empower-jobs/filters', {
            industryId: $("#industry").val(),
            level: $("#level").val(),
            categoryId: $("#job_category").val(),
            city: $("#location").val(),
        }).then(function (response) {
            var categories = response.data.categories;
            var industries = response.data.industries;

            var category_id = $("#job_category").val();
            var industry_id = $("#industry").val();

            options("#job_category", categories, category_id);

            options("#industry", industries, industry_id);
        });


    });

});


