$(document).ready(function () {

    // $('.money').mask('000,000,000,000,000,00');
    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.thumb-user-image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    // $('.user-profile').on('change', function () { readFile(this); });   

    /******************************************/
    // Init Tooltip
    /******************************************/
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body',
        placement: 'top'
    })
    $('[data-toggle="left-tooltip"]').tooltip({
        container: 'body',
        placement: 'left'
    })
    $(".mobile").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && e.which != 40 && e.which != 41 && e.which != 45 && e.which != 43 && e.which != 47 && e.which != 32 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

});
function showLoader() {
    $(".loader").css("display", "block");
}
function hideLoder() {
    $(".loader").css("display", "none");
}