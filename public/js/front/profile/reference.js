function openReferenceModal(id) {
    showLoader();
    if(id != undefined) {
        id = id;
    } else {
        id = null
    }
    axios.get(referenceUrl + '/' + id)
        .then(function (response) {
            var relationTypes = response.data.relationTypes
            var companies = response.data.companies;
            var reference = response.data.reference;
            var edit = response.data.edit;

            if (edit == true) {
                var relationId = (reference.relation_types != undefined)?reference.relation_types.id:""
            } else {
                var relationId = undefined;
            }
            options("#relation_type_id", relationTypes, relationId);


            if (edit == true) {
                var data = {
                    id: (reference.companies != undefined)?reference.companies.id:"",
                    text: (reference.companies != undefined)?reference.companies.name:""
                };
                var newOption = new Option(data.text, data.id, true, true);
                $('#company_id').append(newOption).trigger('change');

            }

            if (edit == true) {
                $("#name").val(reference.name);
                $("#position").val(reference.position);
                $("#refemail").val(reference.email);
                $("#refmobile").val(reference.phone);
                $("#reference_id").val(reference.id);
                $(".frmreferencesubmit").val("Update");
            } else {
                $("#reference_id").val('');
                $(".frmreferencesubmit").val("Add");
            }
            singleSelcet2("#relation_type_id");
            // $(".phone").mask('(000) 000-0000');
            hideLoder();
            $("#reference-information").modal({
                backdrop: 'static',
                keyboard: false
            }, "show");

        });
}
var frmreference = $("#frmreference").validate({
    rules: {
        refemail: {
            email: true
        }
    },
    messages: {
        company_id: "Company Name is required",
        name: "Name is required",
        relation_type_id: "Relationship is required",
        refemail: "Email is required",
        refmobile: "Mobile is required"
    },
    errorElement: 'span',
    errorClass: 'invalid-feedback',
    errorPlacement: function (error, element) {
        if (element.type == 'input') {
            error.insertAfter(element.sibling(a));
        } else {
            if (element.hasClass("select2-hidden-accessible")) {
                if (element.hasClass("required")) {
                    el = element.parent('div');
                    error.appendTo(el);
                    // $(content).appendTo(selector);
                    // error.app(el);
                } else {
                    el = $("#select2-" + element.attr("id") + "-container").parent();
                    error.insertAfter(el);
                }
            } else if (element.prop('type') === 'radio') {
                error.appendTo(element.parent().parent());

            } else {
                if (element.is('#fb-userId')) {
                    el = $(".name-opt .dropdown-menu").parent();
                    error.insertAfter(el);
                } else {
                    error.insertAfter(element);
                }
            }
        }
    },
    submitHandler: function (form) {
        $(".frmreferencesubmit", this).attr("disabled", true).val("Please Wait...");
        axios.post(storeReferenceUrl, {
            id: $("#reference_id").val(),
            name: $("#name").val(),
            company_id: $("#company_id").val(),
            position: $("#position").val(),
            relation_type_id: $("#relation_type_id").val(),
            email: $("#refemail").val(),
            mobile: $("#refmobile").val(),
        }).then(function (response) {
            $(".reference-info-ajax").empty();
            $("#progressBarVal").attr("value", response.data.score);
            $("#progressBarText").html(response.data.score);
            if (response.data.score == 100) {
                $(".note").addClass('d-none');
            } else {
                $(".note").removeClass('d-none');
            }
            $(".reference-info-ajax").append(response.data.html);
            $("#reference-information").modal("hide");
        })
    }
});
$('#reference-information').on('hidden.bs.modal', function () {
    var $frmreference = $('#frmreference');
    $('#frmreference')[0].reset();
    $frmreference.validate().resetForm();
    $frmreference.find('.invalid-feedback').removeClass('invalid-feedback');
    var data = {
        id: "",
        text: ""
    };
    var newOption = new Option(data.text, data.id, true, true);
    $('#company_id').append(newOption).trigger('change');
    // $(".phone").unmask();
});