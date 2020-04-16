<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 *
 * @package    Freelancer Office
 */
class Checkout extends Client_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    function pay($invoice_id = NULL)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');

        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }
        $data['invoice_info'] = array(
            'item_name' => $invoice_info->reference_no,
            'item_number' => $invoice_id,
            'currency' => $invoice_info->currency,
            'amount' => $invoice_due);

        $data['subview'] = $this->load->view('client/checkout/confimation_form', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);
    }

    function process()
    {

        if ($this->input->post()) {
            $errors = array();
            $invoice_id = $this->input->post('invoice_id',true);
            if (!isset($_POST['token'])) {
                $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
            }
            // If no errors, process the order:
            if (empty($errors)) {

                require_once('./' . APPPATH . 'libraries/2checkout/Twocheckout.php');

                Twocheckout::privateKey(config_item('2checkout_private_key'));
                Twocheckout::sellerId(config_item('2checkout_seller_id'));
                Twocheckout::sandbox(false);
                $user_info = $this->invoice_model->check_by(array('user_id' => $this->session->userdata('user_id')), 'tbl_users');
                $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
                $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');

                try {

                    $charge = Twocheckout_Charge::auth(array(
                        "merchantOrderId" => $invoice_info->invoices_id,
                        "token" => $this->input->post('token',true),
                        "currency" => $invoice_info->currency,
                        "total" => $this->input->post('amount',true),
                        "billingAddr" => array(
                            "name" => $client_info->name,
                            "addrLine1" => $client_info->address,
                            "city" => $client_info->city,
                            "country" => $client_info->country,
                            "email" => $client_info->email,
                            "phoneNumber" => $client_info->phone
                        )
                    ));


                    if ($charge['response']['responseCode'] == 'APPROVED') {
                        $transaction = array(
                            'invoices_id' => $charge['response']['merchantOrderId'],
                            'paid_by' => $client_info->client_id,
                            'payer_email' => $charge['response']['billingAddr']['email'],
                            'payment_method' => '1',
                            'notes' => 'Paid by ' . $user_info->username,
                            'amount' => $charge['response']['total'],
                            'trans_id' => $charge['response']['transactionId'],
                            'month_paid' => date('m'),
                            'year_paid' => date('Y'),
                            'payment_date' => date('Y-m-d')
                        );

                        $this->invoice_model->_table_name = 'tbl_payments';
                        $this->invoice_model->_primary_key = 'payments_id';
                        $this->invoice_model->save($transaction);

                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'invoice',
                            'module_field_id' => $invoice_info->invoices_id,
                            'activity' => 'activity_new_payment',
                            'icon' => 'fa-usd',
                            'value1' => display_money($charge['response']['total'], client_currency($client_info->client_id)),
                            'value2' => $invoice_info->reference_no,
                        );
                        $this->invoice_model->_table_name = 'tbl_activities';
                        $this->invoice_model->_primary_key = 'activities_id';
                        $this->invoice_model->save($activity);
                    }
                } catch (Twocheckout_Error $e) {
                    $type = 'error';
                    $message = 'Payment declined with error: ' . $e->getMessage();
                    set_message($type, $message);
                    redirect('client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id);
                }
            }
        }
    }

    function _send_payment_email($invoice_id, $paid_amount)
    {
        $message = Applib::get_table_field(Applib::$email_templates_table, array('email_group' => 'payment_email'
        ), 'template_body');
        $subject = Applib::get_table_field(Applib::$email_templates_table, array('email_group' => 'payment_email'
        ), 'subject');
        $currency = Applib::get_table_field(Applib::$invoices_table, array('inv_id' => $invoice_id), 'currency');

        $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
        $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $invoice_currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $client = Applib::get_table_field(Applib::$invoices_table, array('inv_id' => $invoice_id), 'client');

        $address = Applib::get_table_field(Applib::$companies_table, array('co_id' => $client), 'company_email');
        $params = array(
            'recipient' => $address,
            'subject' => '[ ' . config_item('company_name') . ' ]' . $subject,
            'message' => $message,
            'resourceed_file' => ''
        );

        modules::run('fomailer/send_email', $params);
    }

    function _log_activity($invoice_id, $activity, $icon, $user, $value1 = '', $value2 = '')
    {
        $this->db->set('module', 'invoices');
        $this->db->set('module_field_id', $invoice_id);
        $this->db->set('user', $user);
        $this->db->set('activity', $activity);
        $this->db->set('icon', $icon);
        $this->db->set('value1', $value1);
        $this->db->set('value2', $value2);
        $this->db->insert('activities');
    }

}

////end 