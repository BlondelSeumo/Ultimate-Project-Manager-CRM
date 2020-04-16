<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of payroll_model
 *
 * @author NaYeM
 */
class Payments_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    /**
     * Process subscription payment offline or online
     * @since  Version 1.0.1
     * @param  array $data $_POST data
     * @return boolean
     */
    public function invoice_payment($data)
    {
        if (!is_numeric($data['payment_method']) && !empty($data['payment_method'])) {
            if ($data['payment_method'] == 'braintree') {
                $data['payment_method'] = 'paypal_braintree';
            }
            $cf = $data['payment_method'] . '_gateway';
            $this->$cf->invoice_payment($data);
        }

        return false;
    }

    /**
     * Process invoice payment offline or online
     * @since  Version 1.0.1
     * @param  array $data $_POST data
     * @return boolean
     */
    public function addPayment($invoices_id, $amount, $trans_id = null, $gateway = null)
    {
        $this->load->model('invoice_model');
        $invoice_info = $this->db->where('invoices_id', $invoices_id)->get('tbl_invoices')->row();
        $client_info = $this->db->where('client_id', $invoice_info->client_id)->get('tbl_client')->row();
        $currency = $this->invoice_model->client_currency_symbol($invoice_info->client_id);
        if (empty($currency)) {
            $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        }
        $transaction = array(
            'invoices_id' => $invoices_id,
            'paid_by' => $invoice_info->client_id,
            'payer_email' => $client_info->email,
            'payment_method' => (!empty($gateway) ? $gateway : 'Online'),
            'notes' => "Payment for " . $invoice_info->reference_no,
            'amount' => $amount,
            'currency' => $invoice_info->currency,
            'trans_id' => (!empty($trans_id) ? $trans_id : $invoice_info->reference_no),
            'month_paid' => date('m'),
            'year_paid' => date('Y'),
            'payment_date' => date('d-m-Y')
        );
        $this->invoice_model->_table_name = 'tbl_payments';
        $this->invoice_model->_primary_key = 'payments_id';
        $payments_id = $this->invoice_model->save($transaction);

        $due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
        if ($amount < $due) {
            $status = 'partially_paid';
        } elseif ($amount == $due) {
            $status = 'paid';
        } elseif (!empty($payments_id)) {
            $status = 'partially_paid';
        } else {
            $status = 'Unpaid';
        }
        if (!empty($status)) {
            $invoice_data['status'] = $status;
            update('tbl_invoices', array('invoices_id' => $invoices_id), $invoice_data);
        }

        // Store the order in the database.
        $user = null;
        if ($client_info->primary_contact != 0) {
            $contacts = $client_info->primary_contact;
            $primary_contact = $this->invoice_model->check_by(array('account_details_id' => $contacts), 'tbl_account_details');
            $user = $primary_contact->user_id;
        }
        if (!empty($this->session->userdata('user_id'))) {
            $user = $this->session->userdata('user_id');
        }
        if ($payments_id != 0) {
            $account_id = config_item('default_account');
            if (!empty($account_id)) {
                $reference = lang('invoice') . ' ' . lang('reference_no') . ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $invoices_id) . "' >" . $invoice_info->reference_no . "</a> and " . lang('trans_id') . ": <a href='" . base_url('admin/invoice/manage_invoice/payments_details/' . $payments_id) . "'>" . (!empty($trans_id) ? $trans_id : $invoice_info->reference_no) . "</a>";
                // save into tbl_transaction
                $tr_data = array(
                    'name' => lang('invoice_payment', lang('trans_id') . '# ' . $trans_id),
                    'type' => 'Income',
                    'amount' => $amount,
                    'credit' => $amount,
                    'date' => date('Y-m-d'),
                    'paid_by' => $invoice_info->client_id,
                    'payment_methods_id' => (!empty($gateway) ? $gateway : 'Online'),
                    'reference' => $trans_id,
                    'notes' => lang('this_deposit_from_invoice_payment', $reference),
                    'permission' => 'all',
                );

                $account_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');
                if (!empty($account_info)) {
                    $ac_data['balance'] = $account_info->balance + $tr_data['amount'];
                    $this->invoice_model->_table_name = "tbl_accounts"; //table name
                    $this->invoice_model->_primary_key = "account_id";
                    $this->invoice_model->save($ac_data, $account_info->account_id);

                    $aaccount_info = $this->invoice_model->check_by(array('account_id' => $account_id), 'tbl_accounts');

                    $tr_data['total_balance'] = $aaccount_info->balance;
                    $tr_data['account_id'] = $account_id;

                    // save into tbl_transaction
                    $this->invoice_model->_table_name = "tbl_transactions"; //table name
                    $this->invoice_model->_primary_key = "transactions_id";
                    $return_id = $this->invoice_model->save($tr_data);

                    $deduct_account['account_id'] = $account_id;
                    $this->invoice_model->_table_name = 'tbl_payments';
                    $this->invoice_model->_primary_key = 'payments_id';
                    $this->invoice_model->save($deduct_account, $payments_id);

                    // save into activities
                    $activities = array(
                        'user' => $user,
                        'module' => 'transactions',
                        'module_field_id' => $return_id,
                        'activity' => 'activity_new_deposit',
                        'icon' => 'fa-building-o',
                        'link' => 'admin/transactions/deposit',
                        'value1' => $account_info->account_name,
                        'value2' => $amount,
                    );
                    // Update into tbl_project
                    $this->invoice_model->_table_name = "tbl_activities"; //table name
                    $this->invoice_model->_primary_key = "activities_id";
                    $this->invoice_model->save($activities);
                }
            }

            $currency = $this->invoice_model->client_currency_symbol($client_info->client_id);
            $activity = array(
                'user' => $user,
                'module' => 'invoice',
                'module_field_id' => $invoice_info->invoices_id,
                'activity' => 'activity_new_payment',
                'icon' => 'fa-usd',
                'value1' => display_money($amount, $currency->symbol),
                'value2' => $invoice_info->reference_no,
            );
            $this->invoice_model->_table_name = 'tbl_activities';
            $this->invoice_model->_primary_key = 'activities_id';
            $this->invoice_model->save($activity);

            $mobile = client_can_received_sms($client_info->client_id);
            if (!empty($mobile)) {
                $merge_fields = [];
                $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id));
                $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'payment', $payments_id));
                $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'client', $client_info->client_id));
                $this->sms->send(SMS_PAYMENT_RECORDED, $mobile, $merge_fields);
            }

            $this->send_payment_email($invoices_id, $amount); // Send email to client
            $this->notify_to_client($invoices_id, $invoice_info->reference_no); // Send email to client
            $result['type'] = 'success';
            $result['message'] = 'Payment received and applied to ' . $invoice_info->reference_no;
            set_message($result['type'], $result['message']);
        } else {
            $result['type'] = 'error';
            $result['message'] = 'Payment not recorded in the database. Please contact the system Admin.';
            set_message($result['type'], $result['message']);
        }

        return $result;

    }

    function send_payment_email($invoices_id, $paid_amount)
    {

        $this->load->model('invoice_model');
        $inv_info = $this->invoice_model->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $email_template = email_templates(array('email_group' => 'payment_email'), $inv_info->client_id);
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        $currency = $inv_info->currency;
        $reference = $inv_info->reference_no;

        $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
        $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
        $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $client_info = $this->invoice_model->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

        $address = $client_info->email;

        $params['recipient'] = $address;

        $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
        $params['message'] = $message;
        $params['resourceed_file'] = '';
        // Store the order in the database.
        $user = null;
        if ($client_info->primary_contact != 0) {
            $contacts = $client_info->primary_contact;
            $primary_contact = $this->invoice_model->check_by(array('account_details_id' => $contacts), 'tbl_account_details');
            $user = $primary_contact->user_id;
        }
        if (!empty($this->session->userdata('user_id'))) {
            $user = $this->session->userdata('user_id');
        }
        $activity = array(
            'user' => $user,
            'module' => 'invoice',
            'module_field_id' => $invoices_id,
            'activity' => lang('activity_send_payment'),
            'icon' => 'fa-usd',
            'value1' => $reference,
            'value2' => $currency . ' ' . $amount,
        );
        $this->invoice_model->_table_name = 'tbl_activities';
        $this->invoice_model->_primary_key = 'activities_id';
        $this->invoice_model->save($activity);

        $this->invoice_model->send_email($params);
    }

    function notify_to_client($client_id, $invoice_ref)
    {
        $this->load->model('invoice_model');
        $this->load->library('email');
        $client_info = $this->invoice_model->check_by(array('client_id' => $client_id), 'tbl_client');
        if (!empty($client_info->email)) {
            $data['invoice_ref'] = $invoice_ref;
            $email_msg = $this->load->view('payment/stripe_InvoicePaid', $data, TRUE);
            $email_subject = '[' . $this->config->item('company_name') . ' ] Purchase Confirmation';
            $params['recipient'] = $client_info->email;
            $params['subject'] = $email_subject;
            $params['message'] = $email_msg;
            $params['resourceed_file'] = '';
            $this->invoice_model->send_email($params);
        }
        return true;
    }


}
