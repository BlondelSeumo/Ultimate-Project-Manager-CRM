// Created by Larry Ullman, www.larryullman.com, @LarryUllman
// Posted as part of the series "Processing Payments with Stripe"
// http://www.larryullman.com/series/processing-payments-with-stripe/
// Last updated February 20, 2013

// This page is intended to be stored in a public "js" directory.

// This function is just used to display error messages on the page.
// Assumes there's an element with an ID of "payment-errors".


function reportError(msg) {
    // Show the error in the form:
    $('#payment-errors').text(msg).addClass('alert alert-error');
    // re-enable the submit button:
    $('#submitBtn').prop('disabled', false);
    return false;
}

// Assumes jQuery is loaded!
// Watch for the document to be ready:
$(document).ready(function () {

    // Watch for a form submission:
    $("#payment-form").submit(function (event) {
        event.preventDefault();
        // Flag variable:
        var error = false;

        // disable the submit button to prevent repeated clicks:
        $('#submitBtn').attr("disabled", "disabled");

        // Get the values:
        var ccNum = $('.card-number').val(), cvcNum = $('.card-cvc').val(), expMonth = $('.card-expiry-month').val(), expYear = $('.card-expiry-year').val();

        // Validate the number:
        if (!Stripe.validateCardNumber(ccNum)) {
            error = true;
            reportError('The credit card number appears to be invalid.');
        }

        // Validate the CVC:
        if (!Stripe.validateCVC(cvcNum)) {
            error = true;
            reportError('The CVC number appears to be invalid.');
        }

        // Validate the expiration:
        if (!Stripe.validateExpiry(expMonth, expYear)) {
            error = true;
            reportError('The expiration date appears to be invalid.');
        }

        // Validate other form elements, if needed!

        // Check for errors:
        if (!error) {

            // Get the Stripe token:
            Stripe.createToken({
                number: ccNum,
                cvc: cvcNum,
                exp_month: expMonth,
                exp_year: expYear
            }, stripeResponseHandler);

        }

        // Prevent the form from submitting:
        return false;

    }); // Form submission

}); // Document ready.

// Function handles the Stripe response:
function stripeResponseHandler(status, response) {

    // Check for an error:
    if (response.error) {

        reportError(response.error.message);

    } else { // No errors, submit the form:

        var f = $("#payment-form");

        // Token contains id, last4, and card type:
        var token = response['id'];

        // Insert the token into the form so it gets submitted to the server
        f.append("<input type='hidden' name='stripeToken' value='" + token + "' />");

        // Submit the form:
        f.get(0).submit();

    }

} // End of stripeResponseHandler() function.