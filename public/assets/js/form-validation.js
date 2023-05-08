$(function() {
    'use strict'

    $('.select2').select2({
        placeholder: 'Choose one',
        width: '100%'
    });
    $('#selectForm').parsley();
    /* $('#selectForm2').parsley(); */
    $('#selectForm2').parsley().on('form:validate', function(formInstance) {
        var ok = formInstance.isValid({ group: 'block1', force: true }) || formInstance.isValid({ group: 'block2', force: true });
        $('.invalid-form-error-message')
            .html(ok ? '' : 'You must correctly fill *at least one of these two blocks!')
            .toggleClass('filled', !ok);
        $('.btn').on('click', function() {
            var $this = $(this);
            $this.button('loading');
            setTimeout(function() {
                $this.button('reset');
            }, 5000);
        });

        if (!ok)
            formInstance.validationResult = false;
    });
});