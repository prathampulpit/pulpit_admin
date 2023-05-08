$("#resume_file").liteUploader({

    url: uoloadDocumentUrl,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
    }
}).on("lu:start", function () {
    showLoader();
})
    .on("lu:success", function (e, {
        response
    }) {

        $('.resume').empty();
        var res = JSON.parse(response);
        var html = '';
        html += '<div class="col-md-12 documentList">';
        html += '<div class="form-group file-upload">';
        html += '<label class="control-label"><a target="_blank" href="' + res.url + '" >' + res.title + '</a></label>';
        html += '<div class="file-act">';
        html += '<a href="javascript:void(0)" class="removeResume" data-id="' + res.id + '" title="remove">';
        html += '<i class="material-icons">close</i>';
        html += '</a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('.resume').append(html);

        $('#cvdownload').empty();
        var cvhtml = '';
        cvhtml += '<a href="' + res.url + '" class="new-box d-flex align-items-start mb-3">';
        cvhtml += '<img src="' + uploadIcon + '" alt="Upload CV" class="mt-2" style="width: 38px;">';
        cvhtml += '<div class="ml-3">';
        cvhtml += '<h4>Your CV</h4>';
        cvhtml += '<p>Word document</p>';
        cvhtml += '<span class="em-chip green">Download</span>';
        cvhtml += '</div>';
        cvhtml += '</a>';
        $('#cvdownload').append(cvhtml);

        $("#progressBarVal").attr("value", res.score);
        $("#progressBarText").html(res.score);
        if (res.score == 100) {
            $(".note").addClass('d-none');
        } else {
            $(".note").removeClass('d-none');
        }
        hideLoder();
    })
    .on("lu:fail", function (e, {
        xhr
    }) {
        var error = JSON.parse(xhr.responseText);
        $('.resume-error').text(error.errors.resume_file[0]);
        hideLoder();
    });

$("#resume_file").change(function () {
    $('.resume-error').text('');
    $(this).data("liteUploader").startUpload();
});

$(document).on('click', '.removeResume', function () {
    let id = $(this).attr('data-id');
    let url = deleteDocUrl;
    let el = this;

    axios.post(url, {
        id: id
    })
        .then(function (response) {
            var res = response.data.score;

            $('#cvdownload').empty();
            var cvhtml = '';
            cvhtml += '<a href="#documents-box" class="new-box d-flex align-items-start mb-3">';
            cvhtml += '<img src="' + uploadIcon + '" alt="Upload CV" class="mt-2" style="width: 38px;">';
            cvhtml += '<div class="ml-3">';
            cvhtml += '<h4>Upload CV</h4>';
            cvhtml += '<p>Word document</p>';
            cvhtml += '<span class="em-chip">Pending</span>';
            cvhtml += '</div>';
            cvhtml += '</a>';
            $('#cvdownload').append(cvhtml);

            $(el).parents('.documentList').remove();
            $("#progressBarVal").attr("value", res);
            $("#progressBarText").html(res);
            if (res == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
        })
        .catch(function (error) {
            console.log(error);
        });
});
//selary slip uploader
$("#salary_slip").liteUploader({
    url: uoloadSaleryUrl,

    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
    }
}).on("lu:start", function () {
    showLoader();
})
    .on("lu:success", function (e, {
        response
    }) {
        var res = JSON.parse(response);
        var html = '';
        html += '<div class="col-md-12 documentList">';
        html += '<div class="form-group file-upload">';
        html += '<label class="control-label"><a target="_blank" href="' + res.url + '" >' + res.title + '</a></label>';
        html += '<div class="file-act">';
        html += '<a href="javascript:void(0)" class="removeResume" data-id="' + res.id + '" title="remove">';
        html += '<i class="material-icons">close</i>';
        html += '</a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('.salary').append(html);
        $("#progressBarVal").attr("value", res.score);
        $("#progressBarText").html(res.score);
        if (res.score == 100) {
            $(".note").addClass('d-none');
        } else {
            $(".note").removeClass('d-none');
        }
        hideLoder();
    })
    .on("lu:fail", function (e, {
        xhr
    }) {
        var error = JSON.parse(xhr.responseText);
        $('.salary-error').text(error.errors.salary_slip[0]);
        hideLoder();
    });

$("#salary_slip").change(function () {
    $('.salary-error').text('');
    $(this).data("liteUploader").startUpload();
});
$("#custom_document").click(function () {
    console.log($("#other_document_title").val());
});
