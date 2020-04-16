<?php

class Menu
{

    public function dynamicMenu()
    {

        $CI = &get_instance();
        $designations_id = $CI->session->userdata('designations_id');
        $user_type = $CI->session->userdata('user_type');

        if ($user_type != 1) {// query for employee user role
            $CI->db->select('tbl_user_role.*', FALSE);
            $CI->db->select('tbl_menu.*', FALSE);
            $CI->db->from('tbl_user_role');
            $CI->db->join('tbl_menu', 'tbl_user_role.menu_id = tbl_menu.menu_id', 'left');
            $CI->db->where('tbl_user_role.designations_id', $designations_id);
            $CI->db->where('tbl_menu.status', 1);
            $CI->db->order_by('sort');
            $query_result = $CI->db->get();
            $user_menu = $query_result->result();
        } else { // get all menu for admin
            $user_menu = $CI->db->where('status', 1)->order_by('sort', 'time')->get('tbl_menu')->result();
        }
        // Create a multidimensional array to conatin a list of items and parents
        $menu = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($user_menu as $v_menu) {
            $menu['items'][$v_menu->menu_id] = $v_menu;
            $menu['parents'][$v_menu->parent][] = $v_menu->menu_id;
        }
        // Builds the array lists with data from the menu table
        return $output = $this->buildMenu(0, $menu);
    }

    public function clientMenu()
    {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        $CI->db->select('tbl_client_role.*', FALSE);
        $CI->db->select('tbl_client_menu.*', FALSE);
        $CI->db->from('tbl_client_role');
        $CI->db->join('tbl_client_menu', 'tbl_client_role.menu_id = tbl_client_menu.menu_id', 'left');
        $CI->db->where('tbl_client_role.user_id', $user_id);
        $CI->db->order_by('sort');
        $query_result = $CI->db->get();
        $client_menu = $query_result->result();

        // Create a multidimensional array to conatin a list of items and parents
        $menu = array(
            'items' => array(),
            'parents' => array()
        );
        foreach ($client_menu as $v_menu) {
            $menu['items'][$v_menu->menu_id] = $v_menu;
            $menu['parents'][$v_menu->parent][] = $v_menu->menu_id;
        }
        // Builds the array lists with data from the menu table
        return $output = $this->buildMenu(0, $menu);
    }

    public function buildMenu($parent, $menu, $sub = NULL)
    {
        $html = "";
        $ul_class = "";
        if (!empty(config_item('layout-h'))) {
            $ul_class = 'horizontal_menu navbar-nav';
        }
        if (isset($menu['parents'][$parent])) {
            if (!empty($sub)) {
                $html .= "<ul id=" . $sub . " class='nav s-menu sidebar-subnav collapse'><li class=\"sidebar-subnav-header\">" . lang($sub) . "</li>\n";
            } else {
                $html .= "<ul class='nav s-menu $ul_class'>\n";
            }
            foreach ($menu['parents'][$parent] as $itemId) {
                $result = $this->active_menu_id($menu['items'][$itemId]->menu_id);
                if ($result) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                if ($menu['items'][$itemId]->link == 'knowledgebase') {
                    $terget = 'target="_blank"';
                } else {
                    $terget = null;
                }
                if (!isset($menu['parents'][$itemId])) { //if condition is false only view menu
                    $html .= "<li class='" . $active . "' >\n  <a $terget title='" . lang($menu['items'][$itemId]->label) . "' href='" . base_url() . $menu['items'][$itemId]->link . "'>\n <em class='" . $menu['items'][$itemId]->icon . "'></em><span>" . lang($menu['items'][$itemId]->label) . "</span></a>\n</li> \n";
                }
                if (isset($menu['parents'][$itemId])) { //if condition is true show with submenu
                    $html .= "<li class='sub-menu " . $active . "'>\n  <a data-toggle='collapse' href='#" . $menu['items'][$itemId]->label . "'> <em class='" . $menu['items'][$itemId]->icon . "'></em><span>" . lang($menu['items'][$itemId]->label) . "</span></a>\n";
                    $html .= self::buildMenu($itemId, $menu, $menu['items'][$itemId]->label);
                    $html .= "</li> \n";
                }
            }
            $html .= "</ul> \n";
        }
        return $html;
    }

    public function active_menu_id($id)
    {
        $CI = &get_instance();
        $activeId = $CI->session->userdata('menu_active_id');
        if (!empty($activeId)) {
            foreach ($activeId as $v_activeId) {
                if ($id == $v_activeId) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

}
