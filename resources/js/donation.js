$(document).ready(function (){
    //if guest user then by default donor-type-1 will be highlighted
    if ($('#donor-type-1').is(':checked')) {
        addRequiredAttributes();
        highlightRadioById('donor-type-1');
    }
    //if registered user then by default donor-type-2 will be highlighted
    if ($('#donor-type-2').is(':checked')) {
        removeRequiredAttributes();
        highlightRadioById('donor-type-2');
    }

    if ($('#payment-type-1').is(':checked')) {
        highlightRadioById('payment-type-1');
    } else if($('#payment-type-2').is(':checked')){
        highlightRadioById('payment-type-2');
    }else if($('#payment-type-3').is(':checked')){
        highlightRadioById('payment-type-3');
    }
});
$('.czm-payment-radio-label').parent().on('change', function() {
    $('.czm-radio-option-container')
        .css({
            'background-color': '',
        })
        .find('label').css('color', '#000');

    if ($('#donor-type-1').is(':checked')) {
        addRequiredAttributes();
        highlightRadioById('donor-type-1');

    } else if ($('#donor-type-2').is(':checked')) {
        removeRequiredAttributes();
        highlightRadioById('donor-type-2');
    } else if ($('#donor-type-3').is(':checked')) {
        removeRequiredAttributes();
        highlightRadioById('donor-type-3');
    }

    if ($('#payment-type-1').is(':checked')) {
        highlightRadioById('payment-type-1');
    } else if($('#payment-type-2').is(':checked')){
        highlightRadioById('payment-type-2');
    }else if($('#payment-type-3').is(':checked')){
        highlightRadioById('payment-type-3');
    }
});

function highlightRadioById(radioId){
    $('#' + radioId).closest('.czm-radio-option-container')
        .css('background-color', '#1c4e6e')
        .find('label')
        .css('color', '#fff');
}

const paymentNameElement = $('.payment-name-wrapper');
const paymentEmailElement = $('.payment-email-wrapper');
const paymentPhoneElement = $('.payment-phone-wrapper');
function addRequiredAttributes(){
    paymentNameElement.find('.required').removeClass('d-none');
    paymentNameElement.find('input').attr('required', 'required');
    paymentNameElement.removeClass('d-none');

    paymentEmailElement.find('.required').removeClass('d-none');
    paymentEmailElement.find('input').attr('required', 'required');
    paymentEmailElement.removeClass('d-none');
    //
    // paymentPhoneElement.find('.required').removeClass('d-none');
    // paymentPhoneElement.find('input').attr('required', 'required');
    // paymentPhoneElement.removeClass('d-none');
}

function removeRequiredAttributes(){
    paymentNameElement.find('.required').addClass('d-none');
    paymentNameElement.find('input').removeAttr('required');
    paymentNameElement.addClass('d-none');

    paymentEmailElement.find('.required').addClass('d-none');
    paymentEmailElement.find('input').removeAttr('required');
    paymentEmailElement.addClass('d-none');

    // paymentPhoneElement.find('.required').addClass('d-none');
    // paymentPhoneElement.find('input').removeAttr('required');
    // paymentPhoneElement.addClass('d-none');
}
