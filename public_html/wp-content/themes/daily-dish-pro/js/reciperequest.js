/**
 * This script handles everything related to the recipe wish box.
 */
(function($) {

    const form = $('.reciperequest__box');
    const subjectInput = form.find('[name=ticket_subject]');
    const descriptionInput = form.find('[name=ticket_description]');
    const categoryInput = form.find('[name=ticket_category]');

    const modals = $('.reciperequest__modal');
    const waitingModal = $('.reciperequest__waiting');
    const failedModal = $('.reciperequest__failed');
    const successModal = $('.reciperequest__success');
    const backButton = $('.reciperequest__back');

    form.submit( () => {

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            headers: { 'X-WP-Nonce': form.find('[name=_wpnonce]').val() },
            beforeSend: () => {
                if( ! subjectInput.val() ) {
                    return false;
                }
                waitingModal.addClass('reciperequest__modal--visible');
            },
            data: {
                'fields_data': JSON.stringify({
                    'ticket_subject': subjectInput.val(),
                    'ticket_description': descriptionInput.val(),
                    'ticket_category': categoryInput.val()
                })
            },
            error: () => {
                modals.removeClass('reciperequest__modal--visible');
                failedModal.addClass('reciperequest__modal--visible');
            },
            success: () => {
                modals.removeClass('reciperequest__modal--visible');
                successModal.addClass('reciperequest__modal--visible');
                subjectInput.val('');
                descriptionInput.val('');
            },
        });

        return false;
    });


    backButton.click( () => {
        
        modals.removeClass('reciperequest__modal--visible');

        return false;
    });

})(jQuery);