<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cronjob extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('cron_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('proposal_model');
        $this->load->helper('string');
        $this->load->helper('file');
        $this->load->library('email');
    }

    function index($manually = null)
    {
        if (config_item('active_cronjob') == "on" && time() > (config_item('last_cronjob_run') + 300) || !empty($manually)) {
            $input_data['last_cronjob_run'] = time();
            foreach ($input_data as $key => $value) {
                $data = array('value' => $value);
                $this->db->where('config_key', $key)->update('tbl_config', $data);
                $exists = $this->db->where('config_key', $key)->get('tbl_config');
                if ($exists->num_rows() == 0) {
                    $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
                }
            }
            // send overdue invoice email
            $this->invoices_cron();
            //send recurring_invoice email
            $this->recurring_invoice();
            //send recurring_expenses email
            $this->recurring_expenses();
            // send expire_estimate email
            $this->expire_estimate();
            // send expire_poposals email
            $this->expire_poposals();
            // send projects_cron email
            $this->projects_cron();
            // send goal_tracking_cron email
            $this->goal_tracking_cron();
            $this->set_attendance();
            $this->database_backup();
            $this->autoclose_tickets();
            $this->reminders();
            $this->outgoing_emails();
        }
    }

    private function recurring_expenses()
    {

        $this->db->where('recurring', 'Yes');
        $this->db->where("(total_cycles != done_cycles OR total_cycles=0)");
        $recurring_expenses = $this->db->get('tbl_transactions')->result();
        if (!empty($recurring_expenses)) {
            foreach ($recurring_expenses as $expense) {
                $type = $expense->recurring_type;
                $repeat_every = $expense->repeat_every;
                $last_recurring_date = $expense->last_recurring_date;
                $expense_date = $expense->date;
                // Current date
                $date = new DateTime(date('Y-m-d'));
                // Check if is first recurring
                if (!$last_recurring_date) {
                    $last_recurring_date = date('Y-m-d', strtotime($expense_date));
                } else {
                    $last_recurring_date = date('Y-m-d', strtotime($last_recurring_date));
                }
                $re_create_at = date('Y-m-d', strtotime('+' . $repeat_every . ' ' . strtoupper($type), strtotime($last_recurring_date)));

                if (date('Y-m-d') >= $re_create_at) {
                    // Ok we can repeat the expense now
                    $new_expense['name'] = $expense->name;
                    $new_expense['notes'] = $expense->notes;
                    $new_expense['category_id'] = $expense->category_id;
                    $new_expense['reference'] = $expense->reference;
                    $new_expense['project_id'] = $expense->project_id;
                    $new_expense['billable'] = $expense->billable;
                    $new_expense['client_visible'] = $expense->client_visible;
                    $new_expense['type'] = 'Expense';
                    $new_expense['create_date'] = date('Y-m-d H:i:s');
                    $new_expense['date'] = $re_create_at;
                    $new_expense['recurring_from'] = $expense->transactions_id;
                    $new_expense['added_by'] = $expense->added_by;
                    $new_expense['recurring_type'] = null;
                    $new_expense['repeat_every'] = 0;
                    $new_expense['recurring'] = 0;
                    $new_expense['total_cycles'] = 0;
                    $new_expense['done_cycles'] = 0;
                    $new_expense['custom_recurring'] = 0;
                    $new_expense['last_recurring_date'] = null;
                    $custom_form_cron = custom_form_cron(2, $expense->transactions_id);
                    if (!empty($custom_form_cron)) {
                        foreach ($custom_form_cron as $c_label => $v_fields) {
                            $new_expense[$c_label] = $v_fields;
                        }
                    }
                    $new_expense['account_id'] = $expense->account_id;

                    $account_info = $this->invoice_model->check_by(array('account_id' => $new_expense['account_id']), 'tbl_accounts');
                    if (empty($account_info)) {
                        $account_info = $this->db->get('tbl_accounts')->row();
                    }
                    $new_expense['amount'] = $expense->amount;

                    if (!empty(admin_head($expense->added_by))) {
                        $new_expense['debit'] = $new_expense['amount'];

                        $ac_data['balance'] = $account_info->balance - $new_expense['amount'];
                        $this->invoice_model->_table_name = "tbl_accounts"; //table name
                        $this->invoice_model->_primary_key = "account_id";
                        $this->invoice_model->save($ac_data, $account_info->account_id);

                        $new_expense['total_balance'] = $account_info->balance;
                        $new_expense['status'] = 'paid';
                    }

                    $this->db->insert('tbl_transactions', $new_expense);
                    $insert_id = $this->db->insert_id();
                    if ($insert_id) {

                        $this->db->where('transactions_id', $expense->transactions_id);
                        $this->db->update('tbl_transactions', [
                            'last_recurring_date' => $re_create_at,
                            // In case cron job is late use the date actually when the recurring supposed to happen
                        ]);
                        $this->db->where('transactions_id', $expense->transactions_id);
                        $this->db->set('done_cycles', 'total_cycles+1', false);
                        $this->db->update('tbl_transactions');

                        $this->send_transactions_sms('expense', $insert_id);
                        if (!empty(staff()) && empty(admin_head($expense->added_by))) {
                            $this->expense_request_email($new_expense, $insert_id);
                        }

                        $description = 'not_recurring_expense_saved';
                        $not_value = lang('title') . ': ' . $expense->name . ' ' . lang('account') . ': ' . $account_info->account_name . ' ' . lang('amount') . ': ' . display_money($new_expense['amount']);
                        $dept_head_id = my_department_head($expense->added_by);
                        // get departments head by departments id
                        $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();
                        if (!empty($dept_head)) {
                            $head = $this->db->where('user_id', $dept_head_id)->get('tbl_users')->row();
                            array_push($all_admin, $head);
                        }

                        $notifyUser = array();
                        if (!empty($all_admin)) {
                            foreach ($all_admin as $v_user) {
                                if (!empty($v_user)) {
                                    if ($v_user->user_id != $expense->added_by) {
                                        array_push($notifyUser, $v_user->user_id);
                                        add_notification(array(
                                            'to_user_id' => $v_user->user_id,
                                            'icon' => 'building-o',
                                            'description' => $description,
                                            'link' => 'admin/transactions/view_details/' . $insert_id,
                                            'value' => $not_value,
                                        ));
                                    }
                                }
                            }
                        }
                        if (!empty($notifyUser)) {
                            show_notification($notifyUser);
                        }
                    }
                }
            }
        }
    }

    function expense_request_email($data, $id)
    {
        $dept_head_id = my_department_head($data['added_by']);
        $all_admin = $this->db->where('role_id', 1)->get('tbl_users')->result();

        if (!empty($dept_head_id) || !empty($all_admin)) {
            $head = $this->db->where('user_id', $dept_head_id)->get('tbl_users')->row();
            $expense_email = config_item('expense_email');
            if (!empty($expense_email) && $expense_email == 1) {
                $email_template = email_templates(array('email_group' => 'expense_request_email'), $dept_head_id, true);
                if (!empty($email_template)) {
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $username = str_replace("{NAME}", fullname($data['added_by']), $message);
                    $amount = str_replace("{AMOUNT}", $data['amount'], $username);
                    $Link = str_replace("{URL}", base_url() . 'admin/transactions/view_details/' . $id, $amount);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    if (!empty($all_admin)) {
                        foreach ($all_admin as $v_admin) {
                            if (!empty($v_admin)) {
                                $params['recipient'] = $v_admin->email;
                                $this->invoice_model->send_email($params);
                                if (!empty($dept_head_id)) {
                                    if ($dept_head_id == $v_admin->user_id) {
                                        $already_send = 1;
                                    }
                                }
                            }
                        }
                    }
                    if (empty($already_send) && !empty($head->email)) {
                        $params['recipient'] = $head->email;
                        $this->invoice_model->send_email($params);
                    }

                }
            } else {
                return true;
            }
        }
    }

    private function send_transactions_sms($type, $transaction_id)
    {
        $mobile = can_received_sms('transaction_record_sms_number');
        if (!empty($mobile)) {
            $merge_fields = [];
            $merge_fields = array_merge($merge_fields, merge_transactions_template($type, $transaction_id));
            $this->sms->send(SMS_TRANSACTION_RECORD, $mobile, $merge_fields);
        }
        return true;
    }

    function outgoing_emails()
    {
        // Get a list of overdue projects
        $email_lists = $this->db->where('delivered', '0')->get('tbl_outgoing_emails')->result();
        if (count($email_lists) > 0) {
            foreach ($email_lists as $em) {
                $params = array(
                    'recipient' => $em->sent_to,
                    'subject' => $em->subject,
                    'message' => $em->message,
                    'resourceed_file' => ''
                );
                $this->invoice_model->send_email($params);
                // We have sent an alert email
                $this->db->where('id', $em->id)->update('tbl_outgoing_emails', array('delivered' => '1'));
                // Clean outgoing emails table
                $this->db->truncate('tbl_outgoing_emails');
            }
            return array('success' => TRUE, 'result' => count($email_lists) . ' outgoing emails sent');
        } else {
            log_message('error', 'There are no outgoing emails to send');
            return array('success' => TRUE, 'result' => 'There are no outgoing emails');
        }

    }

    function client_sms_reminder($data, $mobile)
    {
        $message = config_item('sms_reminder_template');

        $description = str_replace("{reminder_description}", $data['description'], $message);
        $date = str_replace("{reminder_date}", strftime(config_item('date_format'), strtotime($data['date'])), $description);
        $message = str_replace("{site_name}", config_item('company_name'), $date);

        if (!empty($mobile)) {
            if (function_exists('twilio_send_sms')) {
                $retval = call_user_func_array('twilio_send_sms', array($mobile, clear_textarea_breaks($message)));
                return $retval;
            }
        }
    }

    private function reminders()
    {
        // Customer reminders
        $this->db->where('notified', 'No');
        $reminders = $this->db->get('tbl_reminders')->result();
        if (!empty($reminders)) {
            foreach ($reminders as $reminder) {
                if (!empty($reminder)) {
                    if (date('Y-m-d H:i:s') >= $reminder->date) {
                        $module = $reminder->module;
                        $module_id = $reminder->module_id;
                        if ($module == 'invoice') {
                            $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $module_id), 'tbl_invoices');
                            $email_template = email_templates(array('email_group' => 'invoice_reminder'), $invoice_info->client_id);

                            $amount = $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id);
                            $due_date = $invoice_info->due_date;
                            $message = $reminder->description . '<br> ' . $email_template->template_body;

                            $client_name = str_replace("{CLIENT}", client_name($invoice_info->client_id), $message);
                            $Amount = str_replace("{AMOUNT}", $amount, $client_name);
                            $Currency = str_replace("{CURRENCY}", client_currency($invoice_info->client_id), $Amount);
                            $Due_date = str_replace("{DUE_DATE}", $due_date, $Currency);
                            $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id, $Due_date);
                            $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);
                            $ref = $invoice_info->reference_no;
                            $not_link = 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id;
                            $value = $invoice_info->reference_no;
                        } else if ($module == 'estimate') {
                            $estimate_info = $this->invoice_model->check_by(array('estimates_id' => $module_id), 'tbl_estimates');
                            $client_info = $this->invoice_model->check_by(array('client_id' => $estimate_info->client_id), 'tbl_client');
                            $ref = $estimate_info->reference_no;
                            $message = $reminder->description;
                            $not_link = 'client/estimates/index/estimates_details/' . $module_id;
                            $value = $estimate_info->reference_no;
                        } else if ($module == 'proposal') {
                            $proposal_info = $this->invoice_model->check_by(array('module' => 'client', 'module_id' => $module_id), 'tbl_proposals');
                            $client_info = $this->invoice_model->check_by(array('client_id' => $proposal_info->module_id), 'tbl_client');
                            $ref = $proposal_info->reference_no;
                            $message = $reminder->description;
                            $not_link = 'client/proposals/index/proposals_details/' . $module_id;
                            $value = $proposal_info->reference_no;
                        } else if ($module == 'client') {
                            $client_info = $this->invoice_model->check_by(array('client_id' => $module_id), 'tbl_client');
                            $ref = null;
                            $message = $reminder->description;
                            $not_link = 'client/settings';
                            $value = client_name($module_id);
                        } else if ($module == 'leads') {
                            $client_info = $this->invoice_model->check_by(array('leads_id' => $module_id), 'tbl_leads');
                            $ref = null;
                            $message = $reminder->description;
                            $not_link = '#';
                            $value = $client_info->lead_name;
                        }
                        $subject = lang($module) . ' ' . lang('reminder') . ' ' . $ref;
                        if (!empty($client_info)) {
                            $recipient = $client_info->email;
                            $data['message'] = $message;
                            $message = $this->load->view('email_template', $data, TRUE);
                            $params = array(
                                'recipient' => $recipient,
                                'subject' => $subject,
                                'message' => $message
                            );
                            $params['resourceed_file'] = '';
                            $this->invoice_model->send_email($params);
                            if (!empty($client_info->primary_contact)) {
                                $notifyUser = array($client_info->primary_contact);
                            } else {
                                $user_info = $this->invoice_model->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
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
                                        'description' => 'not_reminder',
                                        'link' => $not_link,
                                        'value' => $value,
                                    ));
                                }
                            }
                            show_notification($notifyUser);
                        }

                        if ($reminder->notify_by_email == 'Yes') {

                            $user_info = get_staff_details($reminder->user_id);
                            if (!empty($user_info)) {
                                if (!empty($user_info->mobile)) {
                                    $merge_fields = [];
                                    $merge_fields = array_merge($merge_fields, merge_reminder_template($reminder->reminder_id, $value, $not_link));
                                    $this->sms->send(SMS_STAFF_REMINDER, $user_info->mobile, $merge_fields);
                                }
                                $params = array(
                                    'recipient' => $user_info->email,
                                    'subject' => $subject,
                                    'message' => $message
                                );
                                $params['resourceed_file'] = '';
                                $this->invoice_model->send_email($params);

                                $notifyUser = array($user_info->user_id);
                                if (!empty($notifyUser)) {
                                    foreach ($notifyUser as $v_user) {
                                        if ($v_user != $this->session->userdata('user_id')) {
                                            add_notification(array(
                                                'to_user_id' => $v_user,
                                                'icon' => 'shopping-cart',
                                                'description' => 'not_reminder',
                                                'link' => $not_link,
                                                'value' => $value,
                                            ));
                                        }
                                    }
                                    show_notification($notifyUser);
                                }
                            }
                        }
                        $this->db->where('reminder_id', $reminder->reminder_id);
                        $this->db->update('tbl_reminders', array('notified' => 'Yes'));
                    }
                }
            }
        }
    }

    function autoclose_tickets()
    {
        $auto_close_ticket = config_item('auto_close_ticket');
        if (config_item('auto_close_ticket') > 0) {
            $all_tickets = $this->db->where('status !=', 'closed')->get('tbl_tickets')->result();
            if (!empty($all_tickets)) {
                foreach ($all_tickets as $ticket) {
                    $close_ticket = false;
                    if (!is_null($ticket->last_reply)) {
                        $last_reply = strtotime($ticket->last_reply);
                        if ($last_reply <= strtotime('-' . $auto_close_ticket . ' hours')) {
                            $close_ticket = true;
                        }
                    } else {

                        $created = strtotime($ticket->created);
                        if ($created <= strtotime('-' . $auto_close_ticket . ' hours')) {
                            $close_ticket = true;
                        }
                    }
                    if ($close_ticket == true) {
                        $this->db->where('tickets_id', $ticket->tickets_id);
                        $this->db->update('tbl_tickets', array(
                            'status' => 'closed'
                        ));
                    }
                }
            }
        }
        return TRUE;
    }

    function database_backup()
    {
        // Auto Backup every 7 days
        if ((config_item('automatic_database_backup') == 'on') && time() > (config_item('last_autobackup') + 7 * 24 * 60 * 60)) {
            $this->load->dbutil();
            $prefs = array('format' => 'zip', 'filename' => 'Database-auto-full-backup_' . date('Y-m-d_H-i'));
            $backup = $this->dbutil->backup($prefs);
            if (!write_file('./uploads/backup/BD-backup_' . date('Y-m-d_H-i') . '.zip', $backup)) {
                log_message('error', "Error while creating auto database backup!");
            } else {
                $input_data['last_autobackup'] = time();
                foreach ($input_data as $key => $value) {
                    $data = array('value' => $value);
                    $this->db->where('config_key', $key)->update('tbl_config', $data);
                    $exists = $this->db->where('config_key', $key)->get('tbl_config');
                    if ($exists->num_rows() == 0) {
                        $this->db->insert('tbl_config', array("config_key" => $key, "value" => $value));
                    }
                }
                log_message('error', "Auto backup has been created.");

            }
        }
        return TRUE;
    }

    function set_attendance()
    {
        // get all attendance by date
        $where = array('role_id !=' => 2, 'activated' => 1);
        $all_employee_info = $this->db->where($where)->get('tbl_users')->result();

        foreach ($all_employee_info as $v_employee) {
//             set timezone to user timezone

            $date = date('Y-m-d');
            // get office houre info

            // get all attendance by date
            $this->invoice_model->_table_name = 'tbl_attendance';
            $this->invoice_model->_order_by = 'attendance_id';
            $all_attendance_info = $this->invoice_model->get_by(array('user_id' => $v_employee->user_id, 'date_in' => $date), FALSE);
            // get working holiday
            $holidays = $this->global_model->get_holidays(); //tbl working Days Holiday

            $day_name = date("l", strtotime(date('Y-m-d')));
            if (!empty($holidays)) {
                foreach ($holidays as $v_holiday) {
                    if ($v_holiday->day == $day_name) {
                        $yes_holiday[] = $day_name;
                    }
                }
            }
            // get public holiday
            $public_holiday = $this->invoice_model->check_by(array('start_date' => date('Y-m-d')), 'tbl_holiday');

            if (empty($public_holiday) || empty($yes_holiday)) {
                if (!empty($all_attendance_info)) {

                } else {
                    // get leave info
                    $atdnc_data['user_id'] = $v_employee->user_id;
                    $atdnc_data['date_in'] = $date;
                    $atdnc_data['date_out'] = $date;
                    $atdnc_data['attendance_status'] = 0;
                    $this->invoice_model->_table_name = 'tbl_attendance';
                    $this->invoice_model->_primary_key = "attendance_id";
                    $this->invoice_model->save($atdnc_data);
                }
            }

        }
        return TRUE;
    }

    function goal_tracking_cron()
    {
        $mdate = date('Y-m-d');
        $all_goal_tracking = $this->cron_model->get_permission('tbl_goal_tracking');

        if (!empty($all_goal_tracking)) {
            foreach ($all_goal_tracking as $v_goal_track) {
                $goal_achieve = $this->cron_model->get_progress($v_goal_track);
                if ($v_goal_track->end_date <= $mdate) { // check today is last date or not
                    if ($v_goal_track->email_send == 'no') {// check mail are send or not
                        if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                            if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                                $this->cron_model->send_goal_mail('goal_achieve', $v_goal_track);
                            }
                        } else {
                            if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                                $this->cron_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                            }
                        }
                    }
                }
            }
        }
        return TRUE;
    }


    function expire_poposals()
    {
        $expire_proposal = $this->cron_model->get_overdue('tbl_proposals', 'proposal');
        if (!empty($expire_proposal)) {
            foreach ($expire_proposal as $v_proposal) {
                if ($v_proposal->module == 'client') {
                    $client_info = $this->db->where('client_id', $v_proposal->module_id)->get('tbl_client')->row();
                    $email_template = email_templates(array('email_group' => 'proposal_overdue_email'), $v_proposal->module_id);
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;

                    $client_name = str_replace("{CLIENT}", client_name($v_proposal->module_id), $message);
                    $Ref = str_replace("{PROPOSAL_REF}", $v_proposal->reference_no, $client_name);
                    $Amount = str_replace("{AMOUNT}", $this->proposal_model->proposal_calculation('total', $v_proposal->proposals_id), $Ref);
                    $Currency = str_replace("{CURRENCY}", client_currency($v_proposal->module_id), $Amount);

                    $link = str_replace("{PROPOSAL_LINK}", base_url() . 'client/proposals/index/proposals_details/' . $v_proposal->proposals_id, $Currency);
                    $Due_date = str_replace("{DUE_DATE}", strftime(config_item('date_format'), strtotime($v_proposal->due_date)), $link);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Due_date);

                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);
                    $params = array(
                        'recipient' => $client_info->email,
                        'subject' => $subject,
                        'message' => $message
                    );

                    $params['resourceed_file'] = '';
                    $this->proposal_model->send_email($params);

                    $mobile = client_can_received_sms($v_proposal->module_id);
                    if (!empty($mobile)) {
                        $merge_fields = [];
                        $merge_fields = array_merge($merge_fields, merge_proposal_template($v_proposal->proposals_id));
                        $this->sms->send(SMS_PROPOSAL_EXP_REMINDER, $mobile, $merge_fields);
                    }
                    $data = array('alert_overdue' => '1', 'status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

                    $this->proposal_model->_table_name = 'tbl_proposals';
                    $this->proposal_model->_primary_key = 'proposals_id';
                    $this->proposal_model->save($data, $v_proposal->proposals_id);

                    if ($v_proposal->primary_contact) {
                        $notifyUser = array($v_proposal->primary_contact);
                    } else {
                        $user_info = $this->proposal_model->check_by(array('company' => $v_proposal->client_id), 'tbl_account_details');
                        if (!empty($user_info)) {
                            $notifyUser = array($user_info->user_id);
                        }
                    }
                    if (!empty($notifyUser)) {
                        foreach ($notifyUser as $v_user) {
                            if ($v_user != $this->session->userdata('user_id')) {
                                add_notification(array(
                                    'to_user_id' => $v_user,
                                    'icon' => 'shopping-cart',
                                    'description' => 'not_proposal_overdue',
                                    'link' => 'client/proposals/index/proposals_details/' . $v_proposal->proposals_id,
                                    'value' => $v_proposal->reference_no,
                                ));
                            }
                        }
                        show_notification($notifyUser);
                    }

                }
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue invoices to send emails');
            return TRUE;
        }

    }

    function expire_estimate()
    {
        $expire_estimate = $this->cron_model->get_overdue('tbl_estimates');
        if (!empty($expire_estimate)) {
            foreach ($expire_estimate as $v_estimate) {
                $email_template = email_templates(array('email_group' => 'estimate_overdue_email'), $v_estimate->client_id);
                $message = $email_template->template_body;
                $subject = $email_template->subject;

                $client_name = str_replace("{CLIENT}", $v_estimate->name, $message);
                $Ref = str_replace("{ESTIMATE_REF}", $v_estimate->reference_no, $client_name);
                $Amount = str_replace("{AMOUNT}", $this->estimates_model->estimate_calculation('estimate_amount', $v_estimate->estimates_id), $Ref);
                $Currency = str_replace("{CURRENCY}", $v_estimate->client_id, $Amount);

                $link = str_replace("{ESTIMATE_LINK}", base_url() . 'client/estimates/index/estimates_details/' . $v_estimate->estimates_id, $Currency);
                $Due_date = str_replace("{DUE_DATE}", strftime(config_item('date_format'), strtotime($v_estimate->due_date)), $link);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $Due_date);


                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);
                $params = array(
                    'recipient' => $v_estimate->email,
                    'subject' => $subject,
                    'message' => $message
                );

                $params['resourceed_file'] = '';
                $this->invoice_model->send_email($params);

                $mobile = client_can_received_sms($v_estimate->client_id);
                if (!empty($mobile)) {
                    $merge_fields = [];
                    $merge_fields = array_merge($merge_fields, merge_estimate_template($v_estimate->estimates_id));
                    $merge_fields = array_merge($merge_fields, merge_invoice_template('', 'client', $v_estimate->client_id));
                    $this->sms->send(SMS_ESTIMATE_EXP_REMINDER, $mobile, $merge_fields);
                }

                $data = array('alert_overdue' => '1', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

                $this->estimates_model->_table_name = 'tbl_estimates';
                $this->estimates_model->_primary_key = 'estimates_id';
                $this->estimates_model->save($data, $v_estimate->estimates_id);


                if ($v_estimate->primary_contact) {
                    $notifyUser = array($v_estimate->primary_contact);
                } else {
                    $user_info = $this->estimates_model->check_by(array('company' => $v_estimate->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        if ($v_user != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $v_user,
                                'icon' => 'shopping-cart',
                                'description' => 'not_estimate_overdue',
                                'link' => 'client/estimates/index/estimates_details/' . $v_estimate->estimates_id,
                                'value' => $v_estimate->reference_no,
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }

            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue invoices to send emails');
            return TRUE;
        }

    }

    function invoices_cron()
    {
        $overdue_invoices = $this->cron_model->get_overdue('tbl_invoices');

        if (!empty($overdue_invoices)) {
            foreach ($overdue_invoices as $invoice_info) {

                $email_template = email_templates(array('email_group' => 'invoice_overdue_email'), $invoice_info->client_id);

                $message = $email_template->template_body;

                $subject = $email_template->subject;

                $client_name = str_replace("{CLIENT}", $invoice_info->name, $message);
                $Ref = str_replace("{REF}", $invoice_info->reference_no, $client_name);
                $Amount = str_replace("{AMOUNT}", $this->invoice_model->calculate_to('invoice_due', $invoice_info->invoices_id), $Ref);
                $Currency = str_replace("{CURRENCY}", $invoice_info->currency, $Amount);
                $Due_date = str_replace("{DUE_DATE}", $invoice_info->due_date, $Currency);
                if (!empty($Due_date)) {
                    $Due_date = $Due_date;
                } else {
                    $Due_date = $Currency;
                }
                $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id, $Due_date);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);
                $params = array(
                    'recipient' => $invoice_info->email,
                    'subject' => $subject,
                    'message' => $message
                );
                $params['resourceed_file'] = '';
                $this->invoice_model->send_email($params);

                $u_invoice['alert_overdue'] = 1;
                update('tbl_invoices', array('invoices_id' => $invoice_info->invoices_id), $u_invoice);
                $mobile = client_can_received_sms($invoice_info->client_id);
                if (!empty($mobile)) {
                    $merge_fields = [];
                    $merge_fields = array_merge($merge_fields, merge_invoice_template($invoice_info->invoices_id));
                    $merge_fields = array_merge($merge_fields, merge_invoice_template($invoice_info->invoices_id, 'client', $invoice_info->client_id));
                    $this->sms->send(SMS_INVOICE_OVERDUE, $mobile, $merge_fields);
                }
                if (!empty($invoice_info->primary_contact)) {
                    $notifyUser = array($invoice_info->primary_contact);
                } else {
                    $user_info = $this->invoice_model->check_by(array('company' => $invoice_info->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        if ($v_user != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $v_user,
                                'icon' => 'shopping-cart',
                                'description' => 'not_invoice_overdue',
                                'link' => 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id,
                                'value' => $invoice_info->reference_no,
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue invoices to send emails');
            return TRUE;
        }

    }

    public function recurring_invoice()
    {
        // Gather a list of recurring invoices to generate
        $invoices_recurring = $this->cron_model->get_recurring_invoice();
        if (!empty($invoices_recurring)) {
            foreach ($invoices_recurring as $v_r_invoice) {
                // Create the new invoice
                $invoice_date = array(
                    'client_id' => $v_r_invoice->client_id,
                    'due_date' => $this->cron_model->get_date_due($v_r_invoice->recur_next_date),
                    'reference_no' => $this->invoice_model->generate_invoice_number(),
                    'discount' => $v_r_invoice->discount,
                    'tax' => $v_r_invoice->tax,
                    'currency' => $v_r_invoice->currency,
                    'notes' => $v_r_invoice->notes
                );
                $this->invoice_model->_table_name = 'tbl_invoices';
                $this->invoice_model->_primary_key = 'invoices_id';
                $return_id = $this->invoice_model->save($invoice_date);

                // Copy the original invoice to the new invoice
                $this->cron_model->copy_invoice_items($v_r_invoice->invoices_id, $return_id);

                // Update the next recur date for the recurring invoice
                $this->cron_model->set_next_recur_date($v_r_invoice->invoices_id);

                // Email the new invoice if applicable
                if (config_item('send_email_when_recur') == 'TRUE') {
                    $new_invoice = $this->db->where('invoices_id', $return_id)->get('tbl_invoices')->row();
                    $client_info = $this->db->where('client_id', $new_invoice->client_id)->get('tbl_client')->row();
                    $email_template = email_templates(array('email_group' => 'invoice_message'), $new_invoice->client_id);
                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $ClientName = str_replace("{CLIENT}", client_name($new_invoice->client_id), $message);
                    $Amount = str_replace("{AMOUNT}", $this->invoice_model->calculate_to('invoice_due', $new_invoice->invoices_id), $ClientName);
                    $Currency = str_replace("{CURRENCY}", $new_invoice->currency, $Amount);
                    $link = str_replace("{INVOICE_LINK}", base_url() . 'client/invoice/manage_invoice/invoice_details/' . $new_invoice->invoices_id, $Currency);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

                    $this->send_email_invoice($new_invoice->invoices_id, $message, $subject); // Email Invoice
                    $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));
                    $this->db->where('invoices_id', $new_invoice->invoices_id)->update('tbl_invoices', $data);

                    if (!empty($client_info->primary_contact)) {
                        $notifyUser = array($client_info->primary_contact);
                    } else {
                        $user_info = $this->invoice_model->check_by(array('company' => $client_info->client_id), 'tbl_account_details');
                        if (!empty($user_info)) {
                            $notifyUser = array($user_info->user_id);
                        }
                    }
                    if (!empty($notifyUser)) {
                        foreach ($notifyUser as $v_user) {
                            if ($v_user != $this->session->userdata('user_id')) {
                                add_notification(array(
                                    'to_user_id' => $v_user,
                                    'icon' => 'shopping-cart',
                                    'description' => 'not_invoice_created',
                                    'link' => 'client/invoice/manage_invoice/invoice_details/' . $new_invoice->invoices_id,
                                    'value' => $new_invoice->reference_no,
                                ));
                            }
                        }
                        show_notification($notifyUser);
                    }

                }
            }
        }
        return TRUE;
    }

    function send_email_invoice($invoice_id, $message, $subject)
    {
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->invoice_model->check_by(array('client_id' => $invoice_info->client_id), 'tbl_client');
        $recipient = $client_info->email;
        $data['message'] = $message;
        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = '';
        $this->invoice_model->send_email($params);


    }

    function projects_cron()
    {
        $overdue_project = $this->cron_model->get_overdue('tbl_project');

        if ($overdue_project) {
            foreach ($overdue_project as $v_project) {
                $email_template = email_templates(array('email_group' => 'project_overdue_email'), $v_project->client_id);
                $message = $email_template->template_body;
                $subject = $email_template->subject;

                $client_name = str_replace("{CLIENT}", $v_project->name, $message);
                $projectName = str_replace("{PROJECT_NAME}", $v_project->project_name, $client_name);
                $Due_date = str_replace("{DUE_DATE}", strftime(config_item('date_format'), strtotime($v_project->end_date)), $projectName);
                $link = str_replace("{PROJECT_LINK}", base_url() . 'client/projects/project_details/' . $v_project->project_id, $Due_date);
                $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

                $data['message'] = $message;
                $message = $this->load->view('email_template', $data, TRUE);
                $params = array(
                    'recipient' => $v_project->email,
                    'subject' => $subject,
                    'message' => $message
                );
                $params['resourceed_file'] = '';
                $this->invoice_model->send_email($params);

                $u_project['alert_overdue'] = 1;
                update('tbl_project', array('project_id' => $v_project->project_id), $u_project);

                if ($v_project->primary_contact) {
                    $notifyUser = array($v_project->primary_contact);
                } else {
                    $user_info = $this->invoice_model->check_by(array('company' => $v_project->client_id), 'tbl_account_details');
                    if (!empty($user_info)) {
                        $notifyUser = array($user_info->user_id);
                    }
                }
                if (!empty($notifyUser)) {
                    foreach ($notifyUser as $v_user) {
                        if ($v_user != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $v_user,
                                'icon' => 'folder-open-o',
                                'description' => 'not_project_overdue',
                                'link' => 'client/projects/project_details/' . $v_project->project_id,
                                'value' => $v_project->name,
                            ));
                        }
                    }
                    show_notification($notifyUser);
                }
            }
            return TRUE;
        } else {
            log_message('error', 'There are no overdue projects to send emails');
            return TRUE;
        }
    }

    public function manually()
    {
        $this->index(true);
        redirect('admin/settings/cronjob');
    }


}
