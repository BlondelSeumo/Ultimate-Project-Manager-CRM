<?php

// SETUP:
// 1. Customize all the settings (stripe api key, email settings, email text)
// 2. Put this code somewhere where it's accessible by a URL on your server.
// 3. Add the URL of that location to the settings at https://manage.stripe.com/#account/webhooks
// 4. Have fun!
// set your secret key: remember to change this to your live secret key in production
// see your keys here https://manage.stripe.com/account
Stripe::setApiKey("YOUR STRIPE SECREY KEY");
// retrieve the request's body and parse it as JSON
$body = @file_get_contents('php://input');
$event_json = json_decode($body);
// for extra security, retrieve from the Stripe API
$event_id = $event_json->id;
$event = Stripe_Event::retrieve($event_id);
// This will send receipts on succesful invoices
if ($event->type == 'invoice.payment_succeeded') {
    email_invoice_receipt($event->data->object);
}

function email_invoice_receipt($invoice) {
    $customer = Stripe_Customer::retrieve($invoice->customer);
//Make sure to customize your from address
    $subject = 'Your payment has been received';
    $headers = 'From: "MyApp Support" <support@myapp.com>';
    mail($customer->email, $subject, message_body(), $headers);
}

function format_stripe_amount($amount) {
    return sprintf('$%0.2f', $amount / 100.0);
}

function format_stripe_timestamp($timestamp) {
    return strftime("%m/%d/%Y", $timestamp);
}

function payment_received_body($invoice, $customer) {
    $subscription = $invoice->lines->subscriptions[0];
    return '<<\'EOF\'
Dear {$customer->email}:
This is a receipt for your subscription. This is only a receipt,
no payment is due. Thanks for your continued support!
-------------------------------------------------
SUBSCRIPTION RECEIPT
Email: {$customer->email}
Plan: {$subscription->plan->name}
Amount: {format_stripe_amount($invoice->total)} (USD)
For service between {format_stripe_timestamp($subscription->period->start)} and {format_stripe_timestamp($subscription->period->end)}
-------------------------------------------------
EOF';
}
