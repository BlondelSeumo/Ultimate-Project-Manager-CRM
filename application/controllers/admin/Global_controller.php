<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Global_Controller extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('global_model');
        $this->load->model('admin_model');
    }

    public function fetch_address_info_gmaps()
    {
        include_once(APPPATH . 'third_party/JD_Geocoder_Request.php');
        $data = $this->input->post();
        $address = '';
        $address .= $data['address'];
        if (!empty($data['city'])) {
            $address .= ', ' . $data['city'];
        }
        if (!empty($data['country'])) {
            $address .= ', ' . $data['country'];
        }
        $georequest = new JD_Geocoder_Request();
        $georequest->forwardSearch($address);
        echo json_encode($georequest);
        exit();
    }

    public function get_project_by_client_id($client_id)
    {
        $HTML = null;
        $client_project_info = $this->db->where(array('client_id' => $client_id))->get('tbl_project')->result();
        if (!empty($client_project_info)) {
            $HTML .= "<option value='" . 0 . "'>" . lang('none') . "</option>";
            foreach ($client_project_info as $v_client_project) {
                $HTML .= "<option value='" . $v_client_project->project_id . "'>" . $v_client_project->project_name . "</option>";
            }
        }
        echo $HTML;
        exit();
    }

    public function get_milestone_by_project_id($project_id)
    {
        $milestone_info = $this->db->where(array('project_id' => $project_id))->get('tbl_milestones')->result();
        $HTML = null;
        if (!empty($milestone_info)) {
            $HTML .= "<option value='" . 0 . "'>" . lang('none') . "</option>";
            foreach ($milestone_info as $v_milestone) {
                $HTML .= "<option value='" . $v_milestone->milestones_id . "'>" . $v_milestone->milestone_name . "</option>";
            }
        }
        echo $HTML;
        exit();
    }

    public function get_related_moduleName_by_value($val, $proposal = null)
    {
        if ($val == 'project') {
            $all_project_info = $this->admin_model->get_permission('tbl_project');
            $HTML = null;
            if ($all_project_info) {
                $HTML .= '<div class="col-sm-5"><select onchange="get_milestone_by_id(this.value)" name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true" >';
                foreach ($all_project_info as $v_project) {
                    $HTML .= "<option value='" . $v_project->project_id . "'>" . $v_project->project_name . "</option>";
                }
                $HTML .= '</select></div>';

            }
            echo $HTML;
            exit();
        } elseif ($val == 'opportunities') {
            $HTML = null;
            $all_opp_info = $this->admin_model->get_permission('tbl_opportunities');
            if ($all_opp_info) {

                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_opp_info as $v_opp) {
                    $HTML .= "<option value='" . $v_opp->opportunities_id . "'>" . $v_opp->opportunity_name . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        } elseif ($val == 'leads') {
            $all_leads_info = $this->admin_model->get_permission('tbl_leads');
            $HTML = null;
            if ($all_leads_info) {

                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_leads_info as $v_leads) {
                    $HTML .= "<option value='" . $v_leads->leads_id . "'>" . $v_leads->lead_name . "</option>";
                }
                $HTML .= '</select></div>';
                if (!empty($proposal)) {
                    $HTML .= '<div class="form-group ml0 mr0 pt-lg" style="margin-top: 35px"><label class="col-lg-3 control-label">' . lang("currency") . '</label><div class="col-lg-7"><select name="currency" class="form-control selectpicker m0 " data-live-search="true">';
                    $all_currency = $this->db->get('tbl_currencies')->result();
                    foreach ($all_currency as $v_currency) {
                        $HTML .= "<option " . (config_item('default_currency') == $v_currency->code ? ' selected="selected"' : '') . " value='" . $v_currency->code . "'>" . $v_currency->name . "</option>";
                    }
                    $HTML .= '</select></div></div>';
                }
            }
            echo $HTML;
            exit();
        } elseif ($val == 'client') {
            $all_client_info = $this->db->get('tbl_client')->result();
            $HTML = null;
            if ($all_client_info) {
                $HTML .= '<div class="col-sm-7"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true" required>';
                $HTML .= "<option value=''>" . lang('none') . "</option>";
                foreach ($all_client_info as $v_client) {
                    $HTML .= "<option value='" . $v_client->client_id . "'>" . $v_client->name . "</option>";
                }
                $HTML .= '</select></div>';

            }
            echo $HTML;
            exit();
        } elseif ($val == 'supplier') {
            $all_supplier = $this->db->get('tbl_suppliers')->result();
            $HTML = null;
            if ($all_supplier) {
                $HTML .= '<div class="col-sm-7"><select  name="' . $val . '_id" id="related_to"  data-live-search="true" class="form-control selectpicker m0 ">';
                $HTML .= "<option value=''>" . lang('none') . "</option>";
                foreach ($all_supplier as $v_supplier) {
                    $HTML .= "<option value='" . $v_supplier->supplier_id . "'>" . $v_supplier->name . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        } elseif ($val == 'bug') {
            $all_bugs_info = $this->admin_model->get_permission('tbl_bug');
            $HTML = null;
            if ($all_bugs_info) {

                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_bugs_info as $v_bugs) {
                    $HTML .= "<option value='" . $v_bugs->bug_id . "'>" . $v_bugs->bug_title . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        } elseif ($val == 'goal') {
            $all_goal_info = $this->admin_model->get_permission('tbl_goal_tracking');
            $HTML = null;
            if ($all_goal_info) {

                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_tracking_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_goal_info as $v_goal) {
                    $HTML .= "<option value='" . $v_goal->goal_tracking_id . "'>" . $v_goal->subject . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        } elseif ($val == 'sub_task') {
            $all_task_info = $this->admin_model->get_permission('tbl_task');
            $HTML = null;
            if ($all_task_info) {

                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_task_info as $v_task) {
                    $HTML .= "<option value='" . $v_task->task_id . "'>" . $v_task->task_name . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        } elseif ($val == 'expenses') {
            $all_expenses = $this->admin_model->get_permission('tbl_transactions');
            $HTML = null;
            if ($all_expenses) {
                $val = 'transactions_id';
                $HTML .= '<div class="col-sm-5"><select name="' . $val . '_id" id="related_to"  class="form-control selectpicker m0 " data-live-search="true">';
                foreach ($all_expenses as $expenses) {
                    $HTML .= "<option value='" . $expenses->transactions_id . "'>" . $expenses->name . (!empty($expenses->reference) ? '#' . $expenses->reference : '') . "</option>";
                }
                $HTML .= '</select></div>';
            }
            echo $HTML;
            exit();
        }
    }

    public function check_current_password()
    {
        $old_password = $this->input->post('name', true);
        if (!empty($old_password)) {
            if (!empty($old_password)) {
                $password = $this->hash($old_password);
            }
            $check_dupliaction_id = $this->admin_model->check_by(array('user_id' => my_id(), 'password' => $password), 'tbl_users');
            if (empty($check_dupliaction_id)) {
                $result['error'] = lang("password_does_not_match");
            } else {
                $result['success'] = 1;

                $encrypt_password = $this->input->post('encrypt_password', true);
                if (!empty($encrypt_password)) {
                    $result['password'] = decrypt($encrypt_password);
                }
            }
            echo json_encode($result);
            exit();
        }
    }

    public function check_existing_user_name($user_id = null)
    {
        $username = $this->input->post('name', true);
        if (!empty($username)) {
            $check_user_name = $this->admin_model->check_user_name($username, $user_id);
            if (!empty($check_user_name)) {
                $result['error'] = lang("name_already_exist");
            } else {
                $result['success'] = 1;
            }
            echo json_encode($result);
            exit();
        }
    }


    public function check_duplicate_emp_id($user_id = null)
    {
        $employment_id = $this->input->post('name', true);
        if (!empty($employment_id)) {
            $where = array('employment_id' => $employment_id);
            if (!empty($user_id)) {
                $where['user_id !='] = $user_id;
            }
            $check_dupliaction_id = $this->admin_model->check_by($where, 'tbl_account_details');
            if (!empty($check_dupliaction_id)) {
                $result['error'] = lang("employee_id_exist");
            } else {
                $result['success'] = 1;
            }
            echo json_encode($result);
            exit();
        }
    }

    public function check_email_addrees($user_id = null)
    {
        $email_address = $this->input->post('name', true);
        if (!empty($email_address)) {
            $where = array('email' => $email_address);
            if (!empty($user_id)) {
                $where['user_id !='] = $user_id;
            }
            $check_email_address = $this->admin_model->check_by($where, 'tbl_users');
            if (!empty($check_email_address)) {
                $result['error'] = lang("this_email_already_exist");
            } else {
                $result['success'] = 1;
            }
            echo json_encode($result);
            exit();
        }
    }


    public function get_item_name_by_id($stock_sub_category_id)
    {
        $HTML = NULL;
        $this->admin_model->_table_name = 'tbl_stock';
        $this->admin_model->_order_by = 'stock_sub_category_id';
        $stock_info = $this->admin_model->get_by(array('stock_sub_category_id' => $stock_sub_category_id, 'total_stock >=' => '1'), FALSE);
        if (!empty($stock_info)) {
            foreach ($stock_info as $v_stock_info) {
                $HTML .= "<option value='" . $v_stock_info->stock_id . "'>" . $v_stock_info->item_name . "</option>";
            }
        }
        echo $HTML;
        exit();
    }

    public function check_available_leave($user_id, $start_date = NULL, $end_date = NULL, $leave_category_id = NULL)
    {

        $office_hours = config_item('office_hours');
        $result = null;
        if (!empty($leave_category_id) && !empty($start_date)) {

            $total_leave = $this->global_model->check_by(array('leave_category_id' => $leave_category_id), 'tbl_leave_category');
            $leave_total = $total_leave->leave_quota;

            $all_leave = $this->db->where(array('user_id' => $user_id))->get('tbl_leave_application')->result();

            if (!empty($all_leave)) {
                foreach ($all_leave as $v_all_leave) {

                    if (empty($v_all_leave->leave_end_date)) {
                        $v_all_leave->leave_end_date = $v_all_leave->leave_start_date;
                    }
                    $get_dates = $this->global_model->GetDays($v_all_leave->leave_start_date, $v_all_leave->leave_end_date);
                    $result_start = in_array($start_date, $get_dates);

                    if (!empty($end_date) && $end_date != 'null') {
                        $result_end = in_array($end_date, $get_dates);
                    }
                    if (!empty($result_start) || !empty($result_end)) {
                        $result = lang('leave_date_conflict');
                    }
                }

            }

            $token_leave = $this->db->where(array('user_id' => $user_id, 'leave_category_id' => $leave_category_id, 'application_status' => '2'))->get('tbl_leave_application')->result();

            $total_token = 0;
            $total_hourly = 0;
            if (!empty($token_leave)) {
                $ge_days = 0;
                $m_days = 0;
                foreach ($token_leave as $v_leave) {
                    if ($v_leave->leave_type != 'hours') {
                        $month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($v_leave->leave_start_date)), date('Y', strtotime($v_leave->leave_start_date)));
                        $datetime1 = new DateTime($v_leave->leave_start_date);
                        if (empty($v_leave->leave_end_date)) {
                            $v_leave->leave_end_date = $v_leave->leave_start_date;
                        }
                        $datetime2 = new DateTime($v_leave->leave_end_date);
                        $difference = $datetime1->diff($datetime2);

                        if ($difference->m != 0) {
                            $m_days += $month;
                        } else {
                            $m_days = 0;
                        }
                        $ge_days += $difference->d + 1;
                        $total_token = $m_days + $ge_days;
                    }
                    if ($v_leave->leave_type == 'hours') {
                        $total_hourly += ($v_leave->hours / $office_hours);
                    }
                }
            }
            if (empty($total_token)) {
                $total_token = 0;
            }
            if (empty($total_hourly)) {
                $total_hourly = 0;
            }
            $total_token = $total_hourly + $total_token;


            $input_ge_days = 0;
            $input_m_days = 0;
            if (!empty($end_date) && $end_date != 'null') {
                $input_month = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($start_date)), date('Y', strtotime($end_date)));

                $input_datetime1 = new DateTime($start_date);
                $input_datetime2 = new DateTime($end_date);
                $input_difference = $input_datetime1->diff($input_datetime2);

                if ($input_difference->m != 0) {
                    $input_m_days += $input_month;
                } else {
                    $input_m_days = 0;
                }
                $input_ge_days += $input_difference->d + 1;
                $input_total_token = $input_m_days + $input_ge_days;

            } else {
                $input_total_token = 1;
            }
            $taken_with_input = $total_token + $input_total_token;

            $left_leave = $leave_total - $total_token;

            if ($leave_total < $taken_with_input) {
                if ($user_id == $this->session->userdata('user_id')) {
                    $t = 'You ';
                } else {
                    $profile = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
                    $t = $profile->fullname;
                }
                $result = "$t already took  $total_token $total_leave->leave_category You can apply maximum for $left_leave more";
            }
        } else {
            $result = lang('all_required_fill');
        }
        echo $result;
        exit();
    }

    public function get_leave_details($user_id)
    {
        if ($user_id == $this->session->userdata('user_id')) {
            $title = lang('my_leave');
        } else {
            $profile = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
            $title = $profile->fullname;
        }
        $panel = null;
        $panel .= '<div class="panel panel-custom"><div class="panel-heading"><div class="panel-title"><strong>' . $title . ' ' . lang('details') . '</strong></div></div><table class="table"><tbody>';
        $total_taken = 0;
        $total_quota = 0;
        $leave_report = leave_report($user_id);
        if (!empty($leave_report['leave_category'])) {
            foreach ($leave_report['leave_category'] as $lkey => $v_l_report) {
                $total_quota += $leave_report['leave_quota'][$lkey];
                $total_taken += $leave_report['leave_taken'][$lkey];

                $panel .= '<tr><td><strong>' . $leave_report['leave_category'][$lkey] . '</strong>:</td><td>';
                $panel .= $leave_report['leave_taken'][$lkey] . '/' . $leave_report['leave_quota'][$lkey];
                $panel .= '</td></tr>';
            }
        }
        $panel .= '<tr><td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;"><strong>' . lang('total') . '</strong>:</td><td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;">' . $total_taken . '/' . $total_quota . '</td></tr></tbody></table></div>';
        echo $panel;
        exit();
    }

    public function get_employee_by_designations_id($designation_id)
    {
        $HTML = NULL;
        $this->admin_model->_table_name = 'tbl_account_details';
        $this->admin_model->_order_by = 'designations_id';
        $employee_info = $this->admin_model->get_by(array('designations_id' => $designation_id), FALSE);
        if (!empty($employee_info)) {
            foreach ($employee_info as $v_employee_info) {
                $HTML .= "<option value='" . $v_employee_info->user_id . "'>" . $v_employee_info->fullname . "</option>";
            }
        }
        echo $HTML;
        exit();
    }

    public function check_advance_amount($amount, $user_id = null)
    {
        $result = $this->global_model->get_advance_amount($user_id);
        if (!empty($result)) {
            if ($result < $amount) {
                echo lang('exced_basic_salary');
                exit();
            } else {
                echo null;
                exit();
            }
        } else {
            echo lang('you_can_not_apply');
            exit();
        }
    }

    public function get_taxes_dropdown()
    {
        $name = $this->input->post('name', true);
        $taxname = $this->input->post('taxname', true);
        echo $this->admin_model->get_taxes_dropdown($name, $taxname);
        exit();
    }

    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item = $this->admin_model->get_item_by_id($id);
            echo json_encode($item);
            exit();
        }
    }

    public function update_ei_items_order($type)
    {
        $data = $this->input->post();
        foreach ($data['items_id'] as $order) {
            if ($type == 'estimate') {
                $this->db->where('estimate_items_id', $order[0]);
                $this->db->update('tbl_estimate_items', array(
                    'order' => $order[1]
                ));
            } else if ($type == 'credit_note') {
                $this->db->where('credit_note_items_id', $order[0]);
                $this->db->update('tbl_credit_note_items', array(
                    'order' => $order[1]
                ));
            } else if ($type == 'proposal') {
                $this->db->where('proposals_items_id', $order[0]);
                $this->db->update('tbl_proposals_items', array(
                    'order' => $order[1]
                ));
            } else if ($type == 'todo') {
                $this->db->where('todo_id', $order[0]);
                $this->db->update('tbl_todo', array(
                    'order' => $order[1]
                ));
            } else {
                $this->db->where('items_id', $order[0]);
                $this->db->update('tbl_items', array(
                    'order' => $order[1]
                ));
            }

        }
    }

    /* Set notifications to read */
    public function mark_as_read()
    {
        if ($this->input->is_ajax_request()) {
            $this->db->where('to_user_id', $this->session->userdata('user_id'));
            $this->db->update('tbl_notifications', array(
                'read' => 1
            ));
            if ($this->db->affected_rows() > 0) {
                echo json_encode(array(
                    'success' => true
                ));
            } //$this->db->affected_rows() > 0
            return false;
        }
    }

    public function read_inline($id)
    {
        $this->db->where('to_user_id', $this->session->userdata('user_id'));
        $this->db->where('notifications_id', $id);
        $this->db->update('tbl_notifications', array(
            'read_inline' => 1
        ));
    }

    public function mark_desktop_notification_as_read($id)
    {
        $this->db->where('to_user_id', $this->session->userdata('user_id'));
        $this->db->where('notifications_id', $id);
        $this->db->update('tbl_notifications', array(
            'read' => 1,
            'read_inline' => 1
        ));
    }

    public function mark_all_as_read()
    {
        $this->db->where('to_user_id', $this->session->userdata('user_id'));
        $this->db->update('tbl_notifications', array(
            'read' => 1,
            'read_inline' => 1
        ));
    }

    public function get_notification()
    {
        $notificationsIds = array();

        if (config_item('desktop_notifications') == "1") {
            $notifications = $this->global_model->get_user_notifications(false);

            $notificationsPluck = array_filter($notifications, function ($n) {
                return $n->read == 0;
            });
            $notificationsIds = array_pluck($notificationsPluck, 'notifications_id');

        }
        echo json_encode(array(
            'html' => $this->load->view('admin/components/notifications', array(), true),
            'notificationsIds' => $notificationsIds
        ));
        exit();
    }

    /* upload a post file */

    function upload_file()
    {
        upload_file_to_temp();
    }

    /* check valid file for project */

    function validate_project_file()
    {
        return validate_post_file($this->input->post("file_name", true));
    }

    function set_media_view($type, $module)
    {
        $k_session[$module . '_media_view'] = $type;
        $this->session->set_userdata($k_session);
        return true;
    }

    public function hash($string)
    {
        return hash('sha512', $string . config_item('encryption_key'));
    }

    public function set_language($lang)
    {
        $this->session->set_userdata('lang', $lang);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/dashboard');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function download_all_attachment($type, $id)
    {
        $attachment_info = $this->db->where($type, $id)->get('tbl_task_attachment')->result();
        if ($type == 'project_id') {
            $info = $this->db->where($type, $id)->get('tbl_project')->row();
            $FileName = $info->project_name;
        } elseif ($type == 'bug_id') {
            $info = $this->db->where($type, $id)->get('tbl_bug')->row();
            $FileName = $info->bug_title;
        } elseif ($type == 'opportunities_id') {
            $info = $this->db->where($type, $id)->get('tbl_opportunities')->row();
            $FileName = $info->opportunity_name;
        } elseif ($type == 'leads_id') {
            $info = $this->db->where($type, $id)->get('tbl_leads')->row();
            $FileName = $info->lead_name;
        } elseif ($type == 'task_id') {
            $info = $this->db->where($type, $id)->get('tbl_task')->row();
            $FileName = $info->task_name;
        }
        $this->load->library('zip');
        if (!empty($attachment_info) && !empty($FileName)) {
            foreach ($attachment_info as $v_attach) {
                $uploaded_files_info = $this->db->where('task_attachment_id', $v_attach->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                $filename = slug_it($FileName);
                foreach ($uploaded_files_info as $v_files) {
                    $down_data = ($v_files->files); // Read the file's contents
                    $this->zip->read_file($down_data);
                }
                $this->zip->download($filename . '.zip');
            }
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/dashboard');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
}
