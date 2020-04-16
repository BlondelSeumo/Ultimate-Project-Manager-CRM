<?php

class Global_Model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function get_public_holidays($yymm)
    {
        $this->db->select('tbl_holiday.*', FALSE);
        $this->db->from('tbl_holiday');
        $this->db->like('start_date', $yymm);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_holidays()
    {
        $this->db->select('tbl_working_days.day_id,tbl_working_days.flag', FALSE);
        $this->db->select('tbl_days.day', FALSE);
        $this->db->from('tbl_working_days');
        $this->db->join('tbl_days', 'tbl_days.day_id = tbl_working_days.day_id', 'left');
        $this->db->where('flag', 0);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function select_user_roll($designations_id)
    {
        $this->db->select('tbl_user_role.*', FALSE);
        $this->db->select('tbl_menu.link, tbl_menu.label', FALSE);
        $this->db->from('tbl_user_role');
        $this->db->join('tbl_menu', 'tbl_user_role.menu_id = tbl_menu.menu_id', 'left');
        $this->db->where('tbl_user_role.designations_id', $designations_id);
//        $this->db->where('tbl_menu.status !=', 2);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function select_client_roll($user_id)
    {
        $this->db->select('tbl_client_role.*', FALSE);
        $this->db->select('tbl_client_menu.link, tbl_client_menu.label', FALSE);
        $this->db->from('tbl_client_role');
        $this->db->join('tbl_client_menu', 'tbl_client_role.menu_id = tbl_client_menu.menu_id', 'left');
        $this->db->where('tbl_client_role.user_id', $user_id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function check_uri($uri)
    {
        $this->db->select('tbl_user_role.*', FALSE);
        $this->db->select('tbl_menu.link, tbl_menu.label', FALSE);
        $this->db->from('tbl_user_role');
        $this->db->join('tbl_menu', 'tbl_user_role.menu_id = tbl_menu.menu_id', 'left');
        $this->db->where('tbl_user_role.designations_id', $this->session->userdata('designations_id'));
        $this->db->where('tbl_menu.link', $uri);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_holiday_list_by_date($start_date, $end_date = null)
    {
        $this->db->select('tbl_holiday.*', FALSE);
        $this->db->from('tbl_holiday');
        $this->db->where('start_date >=', $start_date);
        if (!empty($end_date)) {
            $this->db->where('end_date <=', $end_date);
        }
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_advance_amount($user_id)
    {
        $emp_payroll = $this->db->where('user_id', $user_id)->get('tbl_employee_payroll')->row();
        if (!empty($emp_payroll)) {
            if (!empty($emp_payroll->salary_template_id)) {
                $emp_salary = $this->db->where('salary_template_id', $emp_payroll->salary_template_id)->get('tbl_salary_template')->row();
                $basic_salary = $emp_salary->basic_salary;
            }
            if (!empty($emp_payroll->hourly_rate_id)) {
                $hourly_salary = $this->db->where('hourly_rate_id', $emp_payroll->hourly_rate_id)->get('tbl_hourly_rate')->row();
                $basic_salary = $hourly_salary->hourly_rate * 10;
            }
        }
        if (!empty($basic_salary)) {
            return $basic_salary;
        } else {
            return null;
        }
    }

    public function get_total_attendace_by_date($start_date, $end_date, $user_id, $flag = null)
    {
        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->from('tbl_attendance');
        $this->db->where('user_id', $user_id);
        $this->db->where('date_in >=', $start_date);
        $this->db->where('date_in <=', $end_date);
        if (!empty($flag) && $flag == 'absent') {
            $this->db->where('attendance_status', '0');
        } elseif (!empty($flag) && $flag == 'leave') {
            $this->db->where('attendance_status', '3');
        } else {
            $this->db->where('attendance_status', '1');
        }

        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_total_working_hours($id)
    {
        $total_hh = 0;
        $total_mm = 0;

        $clock_time = $this->get_attendance_info($id);

        foreach ($clock_time as $mytime) {
            if (!empty($mytime)) {
                // calculate the start timestamp
                $startdatetime = strtotime($mytime->date_in . " " . $mytime->clockin_time);
                // calculate the end timestamp
                $enddatetime = strtotime($mytime->date_out . " " . $mytime->clockout_time);
                // calulate the difference in seconds
                $difference = $enddatetime - $startdatetime;
                $years = abs(floor($difference / 31536000));
                $days = abs(floor(($difference - ($years * 31536000)) / 86400));
                $hours = abs(floor(($difference - ($years * 31536000) - ($days * 86400)) / 3600));
                $mins = abs(floor(($difference - ($years * 31536000) - ($days * 86400) - ($hours * 3600)) / 60));#floor($difference / 60);
                $total_mm += $mins;
                $total_hh += $hours;
                // output the result
                $total['minute'] = round($total_mm);
                $total['hour'] = round($total_hh);

            }
        }
        if (!empty($total)) {
            $total = $total;
        } else {
            $total['minute'] = 0;
            $total['hour'] = 0;
        }
        return $total;
    }

    public function get_attendance_info($id)
    {

        $this->db->select('tbl_clock.*', FALSE);
        $this->db->select('tbl_attendance.*', FALSE);
        $this->db->from('tbl_clock');
        $this->db->join('tbl_attendance', 'tbl_attendance.attendance_id = tbl_clock.attendance_id', 'left');
        $this->db->where('tbl_clock.attendance_id', $id);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public function get_user_notifications($read = 1, $no_limit = null)
    {
        $total = 15;

        $total_unread = $this->db->where(array('to_user_id' => $this->session->userdata('user_id'), 'read' => $read))->get('tbl_notifications')->result();
        if (count($total_unread) > 0) {
            $total_unread = count($total_unread);
        } else {
            $total_unread = 0;
        }

        $total_unread_inline = $this->db->where(array('to_user_id' => $this->session->userdata('user_id'), 'read_inline' => $read))->get('tbl_notifications')->result();
        if (count($total_unread_inline) > 0) {
            $total_unread_inline = count($total_unread_inline);
        } else {
            $total_unread_inline = 0;
        }
        if (is_numeric($read)) {
            $this->db->where('read', $read);
        } //is_numeric($read)
        if ($total_unread > $total) {
            $_diff = $total_unread - $total;
            $total = $_diff + $total;
        } elseif ($total_unread_inline > $total) {
            $_diff = $total_unread_inline - $total;
            $total = $_diff + $total;
        }
        $this->db->where('to_user_id', $this->session->userdata('user_id'));
        if (empty($no_limit)) {
            $this->db->limit(10);
        }
        $this->db->order_by('date', 'desc');
        $result = $this->db->get('tbl_notifications')->result();
        return $result;
    }


}
