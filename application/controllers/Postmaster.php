<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Postmaster extends MY_Controller
{
    private $manually = false;

    private $lock_handle;

    public function __construct()
    {
        $temp_dir = $this->get_temp_dir();

        if (!defined('APP_DISABLE_CRON_LOCK') || defined('APP_DISABLE_CRON_LOCK') && !APP_DISABLE_CRON_LOCK) {
            register_shutdown_function([$this, '__destruct']);

            $f = fopen($temp_dir . 'pcrm-cron-lock', 'c');
            if (!$f) {
                $this->lock_handle = fopen(MAIN_TEMP_FOLDER . 'pcrm-cron-lock', 'c');
                // Again? Disable the lock
                if (!$this->lock_handle && !defined('APP_DISABLE_CRON_LOCK')) {
                    // Defined this constant manually here so the cron is able to run
                    // Used in method can_cron_run
                    define('APP_DISABLE_CRON_LOCK', true);
                }
            } else {
                $this->lock_handle = $f;
            }
        }

        parent::__construct();
        $this->load->model('tickets_model');
        $this->load->helper('string');
    }

    public function leads()
    {
        $username = config_item('config_username');
        $password = decrypt(config_item('config_password'));
        $mailbox = config_item('config_host');
        $encryption = config_item('encryption');
        $folder = config_item('config_mailbox');
        $unread_email = config_item('unread_email');
        $delete_mail_after_import = config_item('delete_mail_after_import');

        if ($this->can_cron_run()) {
            update_config('last_postmaster_run', time());
            require_once(APPPATH . 'libraries/Imap.php');
            // open connection
            $imap = new Imap($mailbox, $username, $password, $encryption);
            if ($imap->isConnected() === false) {
                return false;
            }
            if ($folder == '') {
                $folder = 'INBOX';
            }
            $imap->selectFolder($folder);
            if ($unread_email == 'on') {
                $emails = $imap->getUnreadMessages();
            } else {
                $emails = $imap->getMessages();
            }
            foreach ($emails as $email) {
                $from = $email['from'];
                $fromname = preg_replace('/(.*)<(.*)>/', '\\1', $from);
                $fromname = trim(str_replace('"', '', $fromname));
                $fromemail = trim(preg_replace('/(.*)<(.*)>/', '\\2', $from));
                $body = $this->convert_to_body($email['body']);

                $this->db->where('email', $fromemail);
                $lead = $this->db->get(' tbl_leads')->row();
                if (!empty($lead)) {
                    // Check if the lead uid is the same with the email uid
                    if ($lead->email_integration_uid == $email['uid']) {
                        // Set message to seen to in the next time we dont need to loop over this message
                        $imap->setUnseenMessage($email['uid']);
                        continue;
                    }
                    // Set message to seen to in the next time we dont need to loop over this message
                    $imap->setUnseenMessage($email['uid']);
                    $this->notification_lead_integration('not_received_one_or_more_messages_lead', $lead->leads_id);
                    $this->upload_lead_attachments($email, $lead->leads_id, $imap);
                    // Exists not need to do anything except to add the email
                    continue;
                }

                $reporter = $this->db->where('email', $fromemail)->get('tbl_users')->row();
                if (!empty($reporter)) {
                    $profile_info = $this->db->where('user_id', $reporter->user_id)->get('tbl_account_details')->row();
                }
                if (!empty($profile_info)) {
                    $client_id = $profile_info->company;
                } else {
                    $client_id = '-';
                }

                //Ticket Data
                $leads_data = array(
                    'client_id' => $client_id,
                    'lead_name' => $fromname,
                    'lead_status_id' => config_item('default_lead_status'),
                    'lead_source_id' => config_item('default_leads_source'),
                    'contact_name' => $fromname,
                    'email' => $fromemail,
                    'notes' => $body,
                    'imported_from_email' => 1,
                    'email_integration_uid' => $email['uid'],
                    'permission' => config_item('default_lead_permission'),

                );

                $this->tickets_model->_table_name = 'tbl_leads';
                $this->tickets_model->_primary_key = 'leads_id';
                $insert_id = $this->tickets_model->save($leads_data);

                $this->notification_lead_integration('not_received_one_or_more_messages_lead', $insert_id);
                $this->upload_lead_attachments($email, $insert_id, $imap);

                if ($delete_mail_after_import == 'on') {
                    $imap->deleteMessage($email['uid']);
                } else {
                    $imap->setUnseenMessage($email['uid']);
                }

            }
        }
        exit();
    }

    private function notification_lead_integration($description, $leads_id)
    {
        $leads_info = $this->tickets_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->tickets_model->allowed_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => $description,
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
    }

    private function upload_lead_attachments($email, $leads_id, &$imap)
    {
        // Check for any attachments
        if (isset($email['attachments'])) {
            $data['user_id'] = $this->session->userdata('user_id');
            $data['title'] = trim($email['subject']);
            $data['leads_id'] = $leads_id;
            // save and update into tbl_files
            $this->tickets_model->_table_name = "tbl_task_attachment"; //table name
            $this->tickets_model->_primary_key = "task_attachment_id";
            $id = $this->tickets_model->save($data);

            foreach ($email['attachments'] as $key => $attachment) {
                $email_attachment = $imap->getAttachment($email['uid'], $key);
                $path = getcwd() . "/uploads/";
                $file_name = unique_filename($path, $attachment['name']);
                $path = $path . $file_name;
                $fp = fopen($path, 'w+');
                if (fwrite($fp, $email_attachment['content'])) {
                    $is_image = check_image_extension($file_name);
                    $up_data = [];
                    $up_data[] = [
                        'file_name' => $file_name,
                        "uploaded_path" => getcwd() . "/uploads/" . $file_name,
                        'ext' => get_mime_by_extension($attachment['name']),
                        "is_image" => $is_image,
                        "image_width" => 0,
                        "image_height" => 0,
                        "task_attachment_id" => $id
                    ];
                    $this->tickets_model->_table_name = "tbl_task_uploaded_files"; // table name
                    $this->tickets_model->_primary_key = "uploaded_files_id"; // $id
                    $uploaded_files_id = $this->tickets_model->save($up_data);
                }
                fclose($fp);
            }
            // save into activities
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $data['leads_id'],
                'activity' => 'activity_new_leads_attachment',
                'icon' => 'fa-rocket',
                'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/5',
                'value1' => $data['title'],
            );
            // Update into tbl_project
            $this->tickets_model->_table_name = "tbl_activities"; //table name
            $this->tickets_model->_primary_key = "activities_id";
            $this->tickets_model->save($activities);
            $leads_info = $this->tickets_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->tickets_model->allowed_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_uploaded_attachment',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/5',
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        }
    }


    public function tickets()
    {
        // get notify user
        $notified_user = json_decode(config_item('notified_user'));
        $action = array('edit', 'delete', 'view');
        if (!empty($notified_user)) {
            foreach ($notified_user as $v_user) {
                $permission[$v_user] = $action;
            }
        }
        $dept_info = $this->db->where('host !=', '')->where('password !=', '')->where('email !=', '')->get('tbl_departments')->result();
        if ($this->can_cron_run()) {
            if (!empty($dept_info)) {
                update_config('last_tickets_postmaster_run', time());
                foreach ($dept_info as $v_dept) {
                    require_once(APPPATH . 'libraries/Imap.php');

                    $mailbox = $v_dept->host;
                    $username = $v_dept->email;
                    if (!empty($v_dept->username)) {
                        $username = $v_dept->username;
                    }
                    $password = decrypt($v_dept->password);
                    $encryption = $v_dept->encryption;
                    $folder = $v_dept->mailbox;
                    $unread_email = $v_dept->unread_email;
                    $delete_mail_after_import = $v_dept->delete_mail_after_import;
                    // open connection
                    $imap = new Imap($mailbox, $username, $password, $encryption);
                    if ($imap->isConnected() === false) {
                        $activity['module'] = lang('tickets');
                        $activity['activity'] = 'failed_to_connect_import_tickets';
                        $activity['value'] = $v_dept->deptname;
                        activity_log($activity);
                        continue;
                    }
                    if ($folder == '') {
                        $folder = 'INBOX';
                    }
                    $imap->selectFolder($folder);
                    if ($unread_email == 1) {
                        $emails = $imap->getUnreadMessages();
                    } else {
                        $emails = $imap->getMessages();
                    }

                    foreach ($emails as $email) {
                        $from = $email['from'];
                        $fromname = preg_replace('/(.*)<(.*)>/', '\\1', $from);
                        $fromname = trim(str_replace('"', '', $fromname));
                        $fromemail = trim(preg_replace('/(.*)<(.*)>/', '\\2', $from));
                        $subject = $this->fix_encoding_chars($email['subject']);

                        // Check if empty body
                        if (isset($email['body']) && $email['body'] == '' || !isset($email['body'])) {
                            $email['body'] = 'No message found';
                        }
                        $body = $this->convert_to_body($email['body']);

                        // Prevent insert ticket to database if mail delivery error happen
                        // This will stop createing a thousand tickets
                        $system_blocked_subjects = [
                            'Mail delivery failed',
                            'failure notice',
                            'Returned mail: see transcript for details',
                            'Undelivered Mail Returned to Sender',
                        ];

                        $subject_blocked = false;

                        foreach ($system_blocked_subjects as $sb) {
                            if (strpos('x' . $subject, $sb) !== false) {
                                $subject_blocked = true;

                                break;
                            }
                        }

                        if ($subject_blocked == true) {
                            return;
                        }

                        $to = trim($v_dept->email);
                        $toemails = explode(',', $to);

                        $department_id = false;
                        $userid = false;
                        foreach ($toemails as $toemail) {
                            if (!$department_id) {
                                $this->db->where('email', $toemail);
                                $data = $this->db->get('tbl_departments')->row();
                                if ($data) {
                                    $department_id = $data->departments_id;
                                    $to = $data->email;
                                }
                            }
                        }
                        if (!$department_id) {
                            $mailstatus = 'department_not_found';
                        } else {
                            if ($to == $from) {
                                $mailstatus = 'block_potential_email';
                            } else {
                                $filterdate = date('YmdHis', mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y')));
                                $query = 'SELECT count(*) as total FROM tbl_tickets WHERE created > "' . $filterdate . '" AND (email="' . $this->db->escape($from) . '"';
                                $query .= ')';
                                $result = $this->db->query($query)->row();
                                if (10 < $result->total) {
                                    $mailstatus = 'exceed_limit_10_minutes';
                                } else {
                                    $reporter = $this->db->where('email', $fromemail)->get('tbl_users')->row();
                                    if (!empty($reporter)) {
                                        $user_id = $reporter->user_id;
                                    } else {
                                        $user_id = null;
                                    }
                                    //Ticket Data
                                    $ticket_data = array(
                                        'ticket_code' => strtoupper(random_string('alnum', 7)),
                                        'subject' => $subject,
                                        'body' => $this->fix_encoding_chars($body),
                                        'status' => config_item('default_status'),
                                        'reporter' => $user_id,
                                        'priority' => config_item('default_priority'),
                                        'permission' => json_encode($permission),
                                    );

                                    $up_data = [];
                                    if (isset($email['attachments'])) {
                                        foreach ($email['attachments'] as $key => $attachment) {
                                            $email_attachment = $imap->getAttachment($email['uid'], $key);
                                            $path = getcwd() . "/uploads/";
                                            $file_name = unique_filename($path, $attachment['name']);
                                            $path = $path . $file_name;
                                            $is_image = check_image_extension($file_name);
                                            $fp = fopen($path, 'w+');
                                            if (fwrite($fp, $email_attachment['content'])) {
                                                $up_data[] = [
                                                    'fileName' => $file_name,
                                                    "path" => $path,
                                                    "is_image" => $is_image,
                                                    "fullPath" => getcwd() . "/uploads/" . $file_name,
                                                    "size" => $attachment['size'] * 1024,
                                                ];
                                            }
                                            fclose($fp);
                                        }
                                        $ticket_data['upload_file'] = json_encode($up_data);
                                    }
                                    $this->tickets_model->_table_name = 'tbl_tickets';
                                    $this->tickets_model->_primary_key = 'tickets_id';
                                    $id = $this->tickets_model->save($ticket_data);

                                    if (!empty($id)) {
                                        if ($delete_mail_after_import == 0) {
                                            $imap->setUnseenMessage($email['uid']);
                                        } else {
                                            $imap->deleteMessage($email['uid']);
                                        }
                                    } else {
                                        // Set unseen message in all cases to prevent looping throught the message again
                                        $imap->setUnseenMessage($email['uid']);
                                    }
                                    // save into activities
                                    $activities = array(
                                        'user' => $this->session->userdata('user_id'),
                                        'module' => 'tickets',
                                        'module_field_id' => $id,
                                        'activity' => 'activity_create_tickets',
                                        'icon' => 'fa-ticket',
                                        'link' => 'admin/tickets/index/tickets_details/' . $id,
                                        'value1' => $ticket_data['ticket_code'],
                                    );
                                    // Update into tbl_project
                                    $this->tickets_model->_table_name = "tbl_activities"; //table name
                                    $this->tickets_model->_primary_key = "activities_id";
                                    $this->tickets_model->save($activities);

                                    // send email to reporter
                                    $this->send_tickets_info_by_email($ticket_data);
                                    // send email to client
                                    $this->send_tickets_info_by_email($ticket_data, true);

                                }
                                if (!empty($mailstatus)) {
                                    // save into activities
                                    $activities = array(
                                        'user' => $this->session->userdata('user_id'),
                                        'module' => 'tickets',
                                        'module_field_id' => '',
                                        'activity' => $mailstatus,
                                        'icon' => 'fa-ticket',
                                        'link' => '',
                                        'value1' => '',
                                    );
                                    // Update into tbl_project
                                    $this->tickets_model->_table_name = "tbl_activities"; //table name
                                    $this->tickets_model->_primary_key = "activities_id";
                                    $this->tickets_model->save($activities);
                                }
                            }
                        }
                    }
                }
            }
        }
        exit();
    }

    function send_tickets_info_by_email($postdata, $client = NULL)
    {

        if (!empty($postdata['reporter'])) {
            $postdata['reporter'] = $postdata['reporter'];
        } else {
            $postdata['reporter'] = $this->session->userdata('user_id');
        }

        $user_login_info = $this->tickets_model->check_by(array('user_id' => $postdata['reporter']), 'tbl_users');
        $ticket_info = $this->tickets_model->check_by(array('ticket_code' => $postdata['ticket_code']), 'tbl_tickets');

        if (!empty($client)) {
            $email_template = email_templates(array('email_group' => 'ticket_client_email'), $postdata['reporter'], true);
            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $client_email = str_replace("{CLIENT_EMAIL}", $user_login_info->email, $message);
            $ticket_code = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $client_email);
            $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'client/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ticket_code);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
            $data['message'] = $message;

            $message = $this->load->view('email_template', $data, TRUE);

            $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

            $params['recipient'] = $user_login_info->email;
            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';

            $this->tickets_model->send_email($params);

        } else {
            $email_template = email_templates(array('email_group' => 'ticket_staff_email'), $postdata['reporter'], true);
            $department = config_item('default_department');
            if (!empty($department) && $department != 0) {
                $departments_id = $department;
            } else {
                $department_info = $this->db->get('tbl_departments')->row();
                if (!empty($department_info)) {
                    $departments_id = $department_info;
                }
            }
            if (!empty($departments_id)) {
                $designation_info = $this->db->where('departments_id', $departments_id)->get('tbl_designations')->result();
                if (!empty($designation_info)) {
                    foreach ($designation_info as $v_designation) {
                        $user_info[] = $this->db->where('designations_id', $v_designation->designations_id)->get('tbl_account_details')->row();
                    }
                }
            }
            $message = $email_template->template_body;
            $subject = $email_template->subject;

            $TicketCode = str_replace("{TICKET_CODE}", $postdata['ticket_code'], $message);
            $ReporterEmail = str_replace("{REPORTER_EMAIL}", $user_login_info->email, $TicketCode);
            $TicketLink = str_replace("{TICKET_LINK}", base_url() . 'admin/tickets/index/tickets_details/' . $ticket_info->tickets_id, $ReporterEmail);
            $message = str_replace("{SITE_NAME}", config_item('company_name'), $TicketLink);
            $data['message'] = $message;
            $message = $this->load->view('email_template', $data, TRUE);

            $subject = str_replace("[TICKET_CODE]", '[' . $postdata['ticket_code'] . ']', $subject);

            $params['subject'] = $subject;
            $params['message'] = $message;
            $params['resourceed_file'] = '';
            if (!empty($user_info)) {
                foreach ($user_info as $v_user) {
                    if (!empty($v_user)) {
                        $login_info = $this->tickets_model->check_by(array('user_id' => $v_user->user_id), 'tbl_users');
                        $params['recipient'] = $login_info->email;
                        $this->tickets_model->send_email($params);
                    }
                }
            }
        }
    }

    /**
     * Return server temporary directory
     * @return string
     */
    public function get_temp_dir()
    {
        if (function_exists('sys_get_temp_dir')) {
            $temp = sys_get_temp_dir();
            if (@is_dir($temp) && is_writable($temp)) {
                return rtrim($temp, '/\\') . '/';
            }
        }

        $temp = ini_get('upload_tmp_dir');
        if (@is_dir($temp) && is_writable($temp)) {
            return rtrim($temp, '/\\') . '/';
        }

        $temp = MAIN_TEMP_FOLDER;
        if (is_dir($temp) && is_writable($temp)) {
            return $temp;
        }

        return '/tmp/';
    }

    public
    function __destruct()
    {
        if ($this->lock_handle) {
            flock($this->lock_handle, LOCK_UN);
            $this->lock_handle = null;
        }
    }

    private
    function can_cron_run()
    {
        return ($this->lock_handle && flock($this->lock_handle, LOCK_EX | LOCK_NB))
            || (defined('APP_DISABLE_CRON_LOCK') && APP_DISABLE_CRON_LOCK);
    }

    private
    function convert_to_body($body)
    {
        // Trim message
        $body = trim($body);
        $body = str_replace('&nbsp;', ' ', $body);
        // Remove html tags - strips inline styles also
        $body = trim(strip_html_tags($body, '<br/>, <br>, <a>'));
        // Once again do security
        $body = $this->security->xss_clean($body);
        // Remove duplicate new lines
        $body = preg_replace("/[\r\n]+/", "\n", $body);
        // new lines with <br />
        $body = preg_replace('/\n(\s*\n)+/', '<br />', $body);
        $body = preg_replace('/\n/', '<br>', $body);

        return $body;
    }

    private function fix_encoding_chars($text)
    {
        $text = str_replace('ð', 'ğ', $text);
        $text = str_replace('þ', 'ş', $text);
        $text = str_replace('ý', 'ı', $text);
        $text = str_replace('Ý', 'İ', $text);
        $text = str_replace('Ð', 'Ğ', $text);
        $text = str_replace('Þ', 'Ş', $text);

        return $text;
    }

}
