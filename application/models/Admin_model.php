<?php class Admin_Model extends MY_Model
{
    public $_table_name;
    public $_order_by;
    public $_primary_key;

    public function select_menu_by_uri($uriSegment)
    {
        $user_type = $this->session->userdata('user_type');
        if ($user_type == '2') {
            $table = 'tbl_client_menu';
        } else {
            $table = 'tbl_menu';
        }
        $result = $this->db->where('link', $uriSegment)->get($table)->row();
        if (!empty($result) && count($result) > 0) {
            $menuId[] = $result->menu_id;
            $menuId = $this->select_menu_by_id($result->parent, $menuId, $table);
        } else {
            return false;
        }
        if (!empty($menuId)) {
            $lastId = end($menuId);
            $parrent = $this->select_menu_first_parent($lastId, $table);
            array_push($menuId, $parrent->parent);
            return $menuId;
        }
    }

    public
    function select_menu_by_id($id, $menuId, $table = null)
    {
        if (empty($table)) {
            $table = 'tbl_menu';
        }
        $result = $this->db->where('menu_id', $id)->get($table)->row();
        if (!empty($result) && count($result) > 0) {
            array_push($menuId, $result->menu_id);
            if ($result->parent != 0) {
                $result = self::select_menu_by_id($result->parent, $menuId);
            }
        }
        return $menuId;
    }

    public
    function select_menu_first_parent($lastId, $table)
    {
        return $this->db->where('menu_id', $lastId)->get($table)->row();
    }

    public
    function get_transactions_list_by_date($type, $start_date, $end_date)
    {
        $this->db->select('tbl_transactions.*', FALSE);
        $this->db->from('tbl_transactions');
        $this->db->where('type', $type);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public
    function get_goal_report_by_month($start_date, $end_date)
    {
        $this->db->select('tbl_goal_tracking.*', FALSE);
        $this->db->from('tbl_goal_tracking');
        $this->db->where('end_date >=', $start_date);
        $this->db->where('end_date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        $all_type = $this->db->get('tbl_goal_type')->result();
        foreach ($all_type as $v_type) {
            if (!empty($result)) {
                foreach ($result as $item) {
                    if ($v_type->goal_type_id == $item->goal_type_id) {
                        $goal_achieve[$v_type->goal_type_id]['target'][] = $item->achievement;
                        $goal_achieve[$v_type->goal_type_id]['achievement'][] = $this->get_progress($item, true);
                    } else {
                        $goal_achieve[$v_type->goal_type_id]['target'][] = 0;
                        $goal_achieve[$v_type->goal_type_id]['achievement'][] = array('achievement' => 0);
                    }
                }
            }
        }
        if (!empty($goal_achieve)) {
            $goal_achieve = $goal_achieve;
        } else {
            $goal_achieve = array();
        }
        return $goal_achieve;
    }

    public
    function get_transactions_list_by_month($start_date, $end_date)
    {
        $this->db->select('tbl_transactions.*', FALSE);
        $this->db->from('tbl_transactions');
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $query_result = $this->db->get();
        $result = $query_result->result();
        return $result;
    }

    public
    function calculate_amount($year, $month)
    {
        $amount = $this->db->select_sum('amount')
            ->where(array('month_paid' => $month, 'year_paid' => $year))
            ->get('tbl_payments')
            ->row()->amount;
        return ($amount > 0) ? $amount : 0;
    }

    public
    function check_user_name($user_name, $user_id = null)
    {
        $this->db->select('tbl_users.*', false);
        $this->db->from('tbl_users');
        if (!empty($user_id)) {
            $this->db->where('user_id !=', $user_id);
        }
        $this->db->where('username', $user_name);
        $query_result = $this->db->get();
        $result = $query_result->row();

        return $result;
    }


    public
    function get_lang()
    {
        if ($this->session->userdata('lang')) {
            return $this->session->userdata('lang');
        } else {
            $user_id = $this->session->userdata('user_id');
            if (!empty($user_id)) {
                $query = $this->db->select('language')->where('user_id', $this->session->userdata('user_id'))->get('tbl_account_details');
                if ($query->num_rows() > 0) {
                    $row = $query->row();
                    return $row->language;
                }
            } else {
                return config_item('default_language');
            }
        }
    }

    public
    function get_update_info()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_URL => UPDATE_URL . 'api/latest_version',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'current_version' => $this->get_current_db_version(),
                'item_id' => '16292398',
            )
        ));

        $result = curl_exec($curl);
        $error = '';

        if (!$curl || !$result) {
            $error = 'Curl Error - Contact your hosting provider with the following error as reference: Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
        }

        curl_close($curl);

        if ($error != '') {
            return $error;
        }

        return $result;
    }

    public
    function get_current_db_version()
    {
        $this->db->limit(1);
        return $this->db->get('tbl_migrations')->row()->version;
    }

    public
    function is_db_upgrade_required($v = '')
    {
        if (!is_numeric($v)) {
            $v = $this->get_current_db_version();
        }
        $this->load->config('migration');

        if ((int)config_item('migration_version') !== (int)$v) {
            return true;
        }
        return false;
    }

    public
    function upgrade_database_silent()
    {
        if (!is_really_writable(APPPATH . 'config/config.php')) {
            show_error('/config/config.php file is not writable. You need to change the permissions to 755. This error occurs while trying to update database to latest version.');
            die;
        }
        $this->load->config('migration');
        $this->load->library('migration', array(
            'migration_enabled' => true,
            'migration_type' => $this->config->item('migration_type'),
            'migration_table' => $this->config->item('migration_table'),
            'migration_auto_latest' => $this->config->item('migration_auto_latest'),
            'migration_version' => $this->config->item('migration_version'),
            'migration_path' => $this->config->item('migration_path')
        ));
        if ($this->migration->current() === FALSE) {
            return array(
                'success' => false,
                'message' => $this->migration->error_string()
            );
        } else {
            return array(
                'success' => true
            );
        }
    }

    public
    function upgrade_database()
    {
        if (!is_really_writable(APPPATH . 'config/config.php')) {
            show_error('/config/config.php file is not writable. You need to change the permissions to 755. This error occurs while trying to update database to latest version.');
            die;
        }
        $update = $this->upgrade_database_silent();
        if ($update['success'] == false) {
            show_error($update['message']);
        } else {
            $migration_version = config_item('migration_version');
            $text = (string)$migration_version;
            $arr = str_split($text, "1");
            $version = implode(".", $arr);

            $this->db->query("UPDATE `tbl_config` SET `value` = '$version' WHERE `tbl_config`.`config_key` = 'version';");
            $type = 'success';
            $message = "Your database is up to date";
            set_message($type, $message);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function get_taxes_dropdown($name, $taxname, $type = '', $item_id = '', $is_edit = false, $manual = false)
    {
        if ($manual == true) {
            if (is_array($taxname) || strpos($taxname, '+') !== false) {
                if (!is_array($taxname)) {
                    $__tax = explode('+', $taxname);
                } else {
                    $__tax = $taxname;
                }
                $taxname = array();
                foreach ($__tax as $t) {
                    $tax_array = explode('|', $t);
                    if (isset($tax_array[0]) && isset($tax_array[1])) {
                        array_push($taxname, $tax_array[0] . '|' . $tax_array[1]);
                    }
                }
            } else {
                $tax_array = explode('|', $taxname);
                if (isset($tax_array[0]) && isset($tax_array[1])) {
                    $tax = get_tax_by_name($tax_array[0]);
                    if ($tax) {
                        $taxname = $tax->name . '|' . $tax->taxrate;
                    }
                }
            }
        }
        $taxes = $this->db->order_by('tax_rate_percent', 'ASC')->get('tbl_tax_rates')->result_array();
        $i = 0;
        foreach ($taxes as $tax) {
            unset($taxes[$i]['tax_rates_id']);
            $taxes[$i]['tax_rate_name'] = $tax['tax_rate_name'] . '|' . $tax['tax_rate_percent'];
            $i++;
        }

        if (is_array($taxname)) {
            foreach ($taxname as $tax) {
                if ((!is_array($tax) && $tax == '') || is_array($tax) && $tax['tax_rate_name'] == '') {
                    continue;
                };
                if (!value_exists_in_array_by_key($taxes, 'tax_rate_name', $tax)) {
                    if (!is_array($tax)) {
                        $tmp_taxname = $tax;
                        $tax_array = explode('|', $tax);
                    } else {
                        $tax_array = explode('|', $tax['tax_rate_name']);
                        $tmp_taxname = $tax['tax_rate_name'];
                        if ($tmp_taxname == '') {
                            continue;
                        }
                    }
                    $taxes[] = array('tax_rate_name' => $tmp_taxname, 'tax_rate_percent' => $tax_array[1]);
                }
            }
        }
        $taxes = array_map("unserialize", array_unique(array_map("serialize", $taxes)));

        $select = '<select class="selectpicker display-block tax" data-width="100%" name="' . $name . '" multiple data-none-selected-text="' . lang('no_tax') . '">';
        foreach ($taxes as $tax) {
            $selected = '';
            if (is_array($taxname)) {
                foreach ($taxname as $_tax) {
                    if (is_array($_tax)) {
                        if ($_tax['tax_rate_name'] == $tax['tax_rate_name']) {
                            $selected = 'selected';
                        }
                    } else {
                        if ($_tax == $tax['tax_rate_name']) {
                            $selected = 'selected';
                        }
                    }
                }
            } else {
                if ($taxname == $tax['tax_rate_name']) {
                    $selected = 'selected';
                }
            }

            $select .= '<option value="' . $tax['tax_rate_name'] . '" ' . $selected . ' data-taxrate="' . $tax['tax_rate_percent'] . '" data-taxname="' . $tax['tax_rate_name'] . '" data-subtext="' . $tax['tax_rate_name'] . '">' . $tax['tax_rate_percent'] . '%</option>';
        }
        $select .= '</select>';

        return $select;
    }

    public function get_item_by_id($id = '')
    {
        $item = $this->db->where('saved_items_id', $id)->get('tbl_saved_items')->row();
        $group = $this->db->where('customer_group_id', $item->customer_group_id)->get('tbl_customer_group')->row();
        $tax_info = json_decode($item->tax_rates_id);
        if (!empty($tax_info)) {
            foreach ($tax_info as $tax_id) {
                $tax = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                $tax_name[] = $tax->tax_rate_name;
                $tax_rate[] = $tax->tax_rate_percent;
            }
        }

        $groupa = (object)[
            'group_name' => (!empty($group->customer_group) ? $group->customer_group : null),
        ];

        $tax = (object)[
            'taxname' => (!empty($tax_name) ? json_encode($tax_name) : null),
            'taxrate' => (!empty($tax_rate) ? json_encode($tax_rate) : null),
        ];

        return (object)array_merge((array)$item, (array)$groupa, (array)$tax);
    }

    public function get_stock_item_by_id($id = '')
    {
        $item = $this->db->where('stock_id', $id)->get('tbl_stock')->row();

        if (!empty($item->stock_sub_category_id)) {
            $sub_category = $this->db->where('stock_sub_category_id', $item->stock_sub_category_id)->get('tbl_stock_sub_category')->row();
            $category = $this->db->where('stock_category_id', $sub_category->stock_category_id)->get('tbl_stock_category')->row();
            if (!empty($category)) {
                $cat = lang('undefined_category');
            } else {
                $cat = $category->stock_category;
            }
            if (!empty($sub_category)) {
                $sucCate = $sub_category->stock_sub_category;
            } else {
                $sucCate = lang('undefined');
            }
            $cate_name = $cat . ' > ' . $sucCate;
        } else {
            $cate_name = lang('undefined_category');
        }
        $item->category = $cate_name;

        $tax_info = json_decode($item->tax_rates_id);
        if (!empty($tax_info)) {
            foreach ($tax_info as $tax_id) {
                $tax = $this->db->where('tax_rates_id', $tax_id)->get('tbl_tax_rates')->row();
                $tax_name[] = $tax->tax_rate_name;
                $tax_rate[] = $tax->tax_rate_percent;
            }
        }
        $tax = (object)[
            'taxname' => (!empty($tax_name) ? json_encode($tax_name) : null),
            'taxrate' => (!empty($tax_rate) ? json_encode($tax_rate) : null),
        ];
        return (object)array_merge((array)$item, (array)$tax);
    }

    public function get_todo_status()
    {
        $statuses = array(
            array(
                'id' => 1,
                'value' => '1',
                'name' => lang('in_progress'),
                'order' => 1,
            ),
            array(
                'id' => 2,
                'value' => '2',
                'name' => lang('on_hold'),
                'order' => 2,
            ),
            array(
                'id' => 3,
                'value' => '3',
                'name' => lang('done'),
                'order' => 3,
            ),

        );
        return $statuses;
    }

}