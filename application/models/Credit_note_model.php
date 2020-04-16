<?php

class Credit_note_model extends MY_Model
{

    public $_table_name;
    public $_order_by;
    public $_primary_key;

    function credit_note_calculation($credit_note_value, $credit_note_id)
    {
        switch ($credit_note_value) {
            case 'credit_note_cost':
                return $this->get_credit_note_cost($credit_note_id);
                break;
            case 'tax':
                return $this->get_credit_note_tax_amount($credit_note_id);
                break;
            case 'discount':
                return $this->get_credit_note_discount($credit_note_id);
                break;
            case 'credit_note_amount':
                return $this->get_credit_note_amount($credit_note_id);
                break;
            case 'total':
                return $this->get_total_credit_note_amount($credit_note_id);
                break;
            case 'credit_used':
                return $this->get_total_credit_used($credit_note_id);
                break;
            case 'credit_remaining':
                return $this->get_total_credit_remaining($credit_note_id);
                break;
        }
    }

    function get_total_credit_used($credit_note_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('credit_note_id', $credit_note_id);
        $this->db->from('tbl_credit_used');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->amount)) {
            $result = $cost->amount;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_total_credit_remaining($credit_note_id)
    {
        $credit_total = $this->get_total_credit_note_amount($credit_note_id);
        $credit_used = $this->get_total_credit_used($credit_note_id);
        if (empty($credit_used)) {
            $credit_used = 0;
        }
        return ($credit_total - $credit_used);
    }

    function get_available_credit_by_client($client_id)
    {
        $credit_note_by_client = get_result('tbl_credit_note', array('client_id' => $client_id));
        $total_credit = 0;
        if (!empty($credit_note_by_client)) {
            foreach ($credit_note_by_client as $v_credit_note) {
                $total_credit += $this->get_total_credit_remaining($v_credit_note->credit_note_id);
            }
        }
        return $total_credit;
    }

    function get_credit_note_cost($credit_note_id)
    {
        $this->db->select_sum('total_cost');
        $this->db->where('credit_note_id', $credit_note_id);
        $this->db->from('tbl_credit_note_items');
        $query_result = $this->db->get();
        $cost = $query_result->row();
        if (!empty($cost->total_cost)) {
            $result = $cost->total_cost;
        } else {
            $result = '0';
        }
        return $result;
    }

    function get_credit_note_tax_amount($credit_note_id)
    {
        $invoice_info = $this->check_by(array('credit_note_id' => $credit_note_id), 'tbl_credit_note');
        if (!empty($invoice_info->total_tax)) {
            $tax_info = json_decode($invoice_info->total_tax);
        }
        $tax = 0;
        if (!empty($tax_info)) {
            $total_tax = $tax_info->total_tax;
            if (!empty($total_tax)) {
                foreach ($total_tax as $t_key => $v_tax_info) {
                    $tax += $v_tax_info;
                }
            }
        }
        return $tax;
    }

    function get_credit_note_discount($credit_note_id)
    {
        $invoice_info = $this->check_by(array('credit_note_id' => $credit_note_id), 'tbl_credit_note');
        return $invoice_info->discount_total;
    }

    function get_credit_note_amount($credit_note_id)
    {

        $tax = $this->get_credit_note_tax_amount($credit_note_id);
        $discount = $this->get_credit_note_discount($credit_note_id);
        $credit_note_cost = $this->get_credit_note_cost($credit_note_id);
        return (($credit_note_cost - $discount) + $tax);
    }

    function get_total_credit_note_amount($credit_note_id)
    {
        $invoice_info = $this->check_by(array('credit_note_id' => $credit_note_id), 'tbl_credit_note');
        $tax = $this->get_credit_note_tax_amount($credit_note_id);
        $discount = $this->get_credit_note_discount($credit_note_id);
        $credit_note_cost = $this->get_credit_note_cost($credit_note_id);
        return (($credit_note_cost - $discount) + $tax + $invoice_info->adjustment);
    }

    function ordered_items_by_id($id)
    {
        $result = $this->db->where('credit_note_id', $id)->order_by('order', 'asc')->get('tbl_credit_note_items')->result();
        return $result;
    }


    public function check_for_merge_credit_note($client_id, $current_credit_note)
    {

        $credit_note_info = $this->db->where('client_id', $client_id)->get('tbl_credit_note')->result();

        foreach ($credit_note_info as $v_credit_note) {
            if ($v_credit_note->credit_note_id != $current_credit_note) {
                if (strtolower($v_credit_note->status) == 'pending' || $v_credit_note->status == 'draft') {
                    $credit_note[] = $v_credit_note;
                }
            }
        }
        if (!empty($credit_note)) {
            return $credit_note;
        } else {
            return array();
        }
    }

    public function get_credit_note_filter()
    {
        $all_invoice = $this->get_permission('tbl_credit_note');
        if (!empty($all_invoice)) {
            $all_invoice = array_reverse($all_invoice);
            foreach ($all_invoice as $v_invoices) {
                $year[] = date('Y', strtotime($v_invoices->credit_note_date));
            }
        }
        if (!empty($year)) {
            $result = array_unique($year);
        }

        $statuses = array(
            array(
                'id' => 1,
                'value' => 'open',
                'name' => lang('open'),
                'order' => 1,
            ),
            array(
                'id' => 1,
                'value' => 'closed',
                'name' => lang('closed'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'refund',
                'name' => lang('refund'),
                'order' => 1,
            ), array(
                'id' => 1,
                'value' => 'void',
                'name' => lang('void'),
                'order' => 1,
            ),
            array(
                'id' => 4,
                'value' => 'last_month',
                'name' => lang('last_month'),
                'order' => 4,
            ),
            array(
                'id' => 4,
                'value' => 'this_months',
                'name' => lang('this_months'),
                'order' => 4,
            )
        );
        if (!empty($result)) {
            foreach ($result as $v_year) {
                $test = array(
                    'id' => 1,
                    'value' => '_' . $v_year,
                    'name' => $v_year,
                    'order' => 1);
                if (!empty($test)) {
                    array_push($statuses, $test);
                }
            }
        }
        return $statuses;
    }

    public function get_credit_notes($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = get_result('tbl_credit_note', array('client_id' => $client_id));
        } else {
            $all_invoice = $this->get_permission('tbl_credit_note');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {

                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->credit_note_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == $v_invoices->status) {
                        $invoice[] = $v_invoices;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->credit_note_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }

                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }

    }

    public function get_client_credit_notes($filterBy = null, $client_id = null)
    {
        if (!empty($client_id)) {
            $all_invoice = get_result('tbl_credit_note', array('client_id' => $client_id, 'status !=' => 'draft'));
        } else {
            $all_invoice = $this->get_permission('tbl_credit_note');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            return $all_invoice;
        } else {
            if (!empty($all_invoice)) {
                $all_invoice = array_reverse($all_invoice);
                foreach ($all_invoice as $v_invoices) {

                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_invoices->credit_note_month) == strtotime($month)) {
                            $invoice[] = $v_invoices;
                        }
                    } else if ($filterBy == $v_invoices->status) {
                        $invoice[] = $v_invoices;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_invoices->credit_note_year) == strtotime($year)) {
                            $invoice[] = $v_invoices;
                        }
                    }

                }
            }
        }
        if (!empty($invoice)) {
            return $invoice;
        } else {
            return array();
        }

    }

    public function get_credit_note_report($filterBy = null, $range = null)
    {
        if (!empty($filterBy) && is_numeric($filterBy)) {
            $credit_notes = $this->db->where('client_id', $filterBy)->get('tbl_credit_note')->result();
        } else {
            $all_credit_notes = $this->get_permission('tbl_credit_note');
        }
        if (empty($filterBy) || !empty($filterBy) && $filterBy == 'all') {
            $credit_notes = $all_credit_notes;
        } else {
            if (!empty($all_credit_notes)) {
                $all_credit_notes = array_reverse($all_credit_notes);
                foreach ($all_credit_notes as $v_credit_note) {
                    if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                        if ($filterBy == 'last_month') {
                            $month = date('Y-m', strtotime('-1 months'));
                        } else {
                            $month = date('Y-m');
                        }
                        if (strtotime($v_credit_note->credit_note_month) == strtotime($month)) {
                            $credit_notes[] = $v_credit_note;
                        }
                    } else if ($filterBy == $v_credit_note->status) {
                        $credit_notes[] = $v_credit_note;
                    } else if (strstr($filterBy, '_')) {
                        $year = str_replace('_', '', $filterBy);
                        if (strtotime($v_credit_note->credit_note_year) == strtotime($year)) {
                            $credit_notes[] = $v_credit_note;
                        }
                    }

                }
            }
        }
        if (!empty($credit_notes)) {
            $credit_note_info = array();
            if (!empty($range[0])) {
                foreach ($credit_notes as $v_credit_note) {
                    if ($v_credit_note->credit_note_date >= $range[0] && $v_credit_note->credit_note_date <= $range[1]) {
                        array_push($credit_note_info, $v_credit_note);
                    }
                }
                return $credit_note_info;
            } else {
                return $credit_notes;
            }
        } else {
            return array();
        }

    }

    public function apply_credits($credit_note_id, $input_post)
    {
        $data = array(
            'invoices_id' => $input_post['invoices_id'],
            'credit_note_id' => $credit_note_id,
            'user_id' => my_id(),
            'date' => date('Y-m-d'),
            'date_applied' => date('Y-m-d H:i'),
            'amount' => $input_post['amount'],
        );
        $this->_table_name = 'tbl_credit_used';
        $this->_primary_key = 'credit_used_id';
        $credit_used_id = $this->save($data);
        if (!empty($credit_used_id)) {
            if ($input_post['added_into_payment'] == 'on') {
                $input_post['credit_note_id'] = $credit_note_id;
                $input_post['credit_used_id'] = $credit_used_id;
                $this->added_into_payment($input_post);
            }
        }

        return true;
    }

    private function added_into_payment($input_post)
    {
        $this->load->model('invoice_model');
        $this->load->helper('string_helper');
        $invoices_id = $input_post['invoices_id'];
        $paid_amount = $input_post['amount'];
        $due = $this->invoice_model->calculate_to('invoice_due', $invoices_id);
        $credit_notes = $this->db->where('credit_note_id', $input_post['credit_note_id'])->get('tbl_credit_note')->row();
        if ($paid_amount != 0) {
            $trans_id = random_string('nozero', 6);
            $inv_info = $this->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
            $data = array(
                'invoices_id' => $invoices_id,
                'paid_by' => $inv_info->client_id,
                'payment_method' => config_item('default_payment_method'),
                'currency' => client_currency($inv_info->client_id),
                'amount' => $paid_amount,
                'payment_date' => date('Y-m-d'),
                'trans_id' => $trans_id,
                'notes' => 'This Payment from Credit notes <a href="' . base_url('admin/credit_note/index/credit_note_details/' . $input_post['credit_note_id']) . '">' . $credit_notes->reference_no . '</a>',
                'month_paid' => date("m"),
                'year_paid' => date("Y"),
            );
            $this->_table_name = 'tbl_payments';
            $this->_primary_key = 'payments_id';
            $payments_id = $this->save($data);

            $this->_table_name = 'tbl_credit_used';
            $this->_primary_key = 'credit_used_id';
            $cu_data['payments_id'] = $payments_id;
            $this->save($cu_data, $input_post['credit_used_id']);

            if ($paid_amount < $due) {
                $status = 'partially_paid';
            }
            if ($paid_amount == $due) {
                $status = 'Paid';
            }
            $invoice_data['status'] = $status;
            update('tbl_invoices', array('invoices_id' => $invoices_id), $invoice_data);

            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'invoice',
                'module_field_id' => $invoices_id,
                'activity' => ('activity_new_payment'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                'value1' => display_money($paid_amount, client_currency($inv_info->client_id)),
                'value2' => $inv_info->reference_no,
            );
            $this->_table_name = 'tbl_activities';
            $this->_primary_key = 'activities_id';
            $this->save($activity);

            if (!empty($inv_info->user_id)) {
                $notifiedUsers = array($inv_info->user_id);
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'description' => 'not_new_invoice_payment',
                            'icon' => 'shopping-cart',
                            'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                            'value' => lang('invoice') . ' ' . $inv_info->reference_no . ' ' . lang('amount') . display_money($paid_amount, $currency->symbol),
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            if ($this->input->post('save_into_account') == 'on') {
                $account_id = config_item('default_account');
                if (!empty($account_id)) {
                    $reference = lang('invoice') . ' ' . lang('reference_no') . ": <a href='" . base_url('admin/invoice/manage_invoice/invoice_details/' . $inv_info->invoices_id) . "' >" . $inv_info->reference_no . "</a> and " . lang('trans_id') . ": <a href='" . base_url('admin/invoice/manage_invoice/payments_details/' . $payments_id) . "'>" . $this->input->post('trans_id', true) . "</a>";
                    // save into tbl_transaction
                    $tr_data = array(
                        'name' => lang('invoice_payment', lang('trans_id') . '# ' . $trans_id),
                        'type' => 'Income',
                        'amount' => $paid_amount,
                        'credit' => $paid_amount,
                        'date' => date('Y-m-d'),
                        'paid_by' => $inv_info->client_id,
                        'payment_methods_id' => config_item('default_payment_method'),
                        'reference' => $trans_id,
                        'notes' => lang('this_deposit_from_invoice_payment', $reference) . ' ' . 'from credit notes',
                        'permission' => 'all',
                    );

                    $account_info = $this->check_by(array('account_id' => $account_id), 'tbl_accounts');
                    if (!empty($account_info)) {
                        $ac_data['balance'] = $account_info->balance + $tr_data['amount'];
                        $this->_table_name = "tbl_accounts"; //table name
                        $this->_primary_key = "account_id";
                        $this->save($ac_data, $account_info->account_id);

                        $aaccount_info = $this->check_by(array('account_id' => $account_id), 'tbl_accounts');

                        $tr_data['total_balance'] = $aaccount_info->balance;
                        $tr_data['account_id'] = $account_id;

                        // save into tbl_transaction
                        $this->_table_name = "tbl_transactions"; //table name
                        $this->_primary_key = "transactions_id";
                        $return_id = $this->save($tr_data);

                        $deduct_account['account_id'] = $account_id;
                        $this->_table_name = 'tbl_payments';
                        $this->_primary_key = 'payments_id';
                        $this->save($deduct_account, $payments_id);

                        // save into activities
                        $activities = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'transactions',
                            'module_field_id' => $return_id,
                            'activity' => 'activity_new_deposit',
                            'icon' => 'fa-building-o',
                            'link' => 'admin/transactions/view_details/' . $return_id,
                            'value1' => $account_info->account_name,
                            'value2' => $paid_amount,
                        );
                        // Update into tbl_project
                        $this->_table_name = "tbl_activities"; //table name
                        $this->_primary_key = "activities_id";
                        $this->save($activities);

                    }
                }

                if ($this->input->post('send_thank_you') == 'on') {
                    $this->send_payment_email($invoices_id, $paid_amount); //send thank you email
                }

                if ($this->input->post('send_sms') == 'on') {
                    $this->send_payment_sms($invoices_id, $payments_id); //send thank you email
                }
            }
        }

    }

    function send_payment_email($invoices_id, $paid_amount)
    {
        $inv_info = $this->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $email_template = email_templates(array('email_group' => 'payment_email'), $inv_info->client_id);
        $message = $email_template->template_body;
        $subject = $email_template->subject;

        if (!empty($inv_info)) {
            $currency = $inv_info->currency;
            $reference = $inv_info->reference_no;

            $invoice_currency = str_replace("{INVOICE_CURRENCY}", $currency, $message);
            $reference = str_replace("{INVOICE_REF}", $reference, $invoice_currency);
            $amount = str_replace("{PAID_AMOUNT}", $paid_amount, $reference);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $amount);

            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);
            $client_info = $this->check_by(array('client_id' => $inv_info->client_id), 'tbl_client');

            // send notification to client
            if (!empty($client_info)) {
                $client_info = $this->check_by(array('client_id' => $client_info->client_id), 'tbl_client');
                if (!empty($client_info->primary_contact)) {
                    $notifyUser = array($client_info->primary_contact);
                } else {
                    $user_info = $this->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    if ($v_user != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $v_user,
                            'icon' => 'shopping-cart',
                            'description' => 'not_payment_received',
                            'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoices_id,
                            'value' => lang('invoice') . ' ' . $inv_info->reference_no . ' ' . lang('amount') . display_money($paid_amount, $inv_info->currency),
                        ));
                    }
                }
                show_notification($notifyUser);
            }

            $address = $client_info->email;

            $params['recipient'] = $address;

            $params['subject'] = '[ ' . config_item('company_name') . ' ]' . ' ' . $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';

            $activity = array(
                'user' => my_id(),
                'module' => 'invoice',
                'module_field_id' => $invoices_id,
                'activity' => ('activity_send_payment'),
                'icon' => 'fa-shopping-cart',
                'link' => 'admin/invoice/manage_invoice/invoice_details/' . $invoices_id,
                'value1' => $reference,
                'value2' => $currency . ' ' . $amount,
            );
            $this->_table_name = 'tbl_activities';
            $this->_primary_key = 'activities_id';
            $this->save($activity);
            $this->send_email($params);
        } else {
            return true;
        }
    }


    public function send_payment_sms($invoices_id, $payments_id)
    {
        $inv_info = $this->check_by(array('invoices_id' => $invoices_id), 'tbl_invoices');
        $mobile = client_can_received_sms($inv_info->client_id);
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id));
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'payment', $payments_id));
            $merge_fields = array_merge($merge_fields, merge_invoice_template($invoices_id, 'client', $inv_info->client_id));
            $this->sms->send(SMS_PAYMENT_RECORDED, $mobile, $merge_fields);
        }
        return true;
    }

}
