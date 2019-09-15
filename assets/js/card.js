/*
 The MIT License (MIT)

 Copyright (c) 2015 William Hilton

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 */
var $form = $('#payment-information-form');
var submitPaymentButton = $('#submit-payment');
var delegateForm = $('#payment-form');
var stripeTokenElement = delegateForm.find('input[name="stripe_token"]');
submitPaymentButton.on('click', payWithStripe);

/* If you're using Stripe for payments */
function payWithStripe(e) {

    e.preventDefault();
    function stripeResponseHandler(status, response) {console.log(response)
        if (response.error) {
            /* Visual feedback */
            submitPaymentButton.html('Try again').prop('disabled', false);
            /* Show Stripe errors on the form */
            $form.find('.payment-errors').text(response.error.message);
            $form.find('.payment-errors').closest('.row').show();
        } else {
            /* Visual feedback */
            submitPaymentButton.html('Processing <i class="fa fa-spinner fa-pulse"></i>');
            /* Hide Stripe errors on the form */
            $form.find('.payment-errors').closest('.row').hide();
            $form.find('.payment-errors').text("");
            // response contains id and card, which contains additional card details

            var token = response.id;
            stripeTokenElement.val(token);
            delegateForm.submit();
        }
    }

    /* Visual feedback */
    $('#submit-payment').html('Validating <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true).removeClass('btn-success');

}
