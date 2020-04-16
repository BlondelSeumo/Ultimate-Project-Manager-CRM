<aside class="aside">
    <!-- START Sidebar (left)-->
    <?php
    $user_id = $this->session->userdata('user_id');
    $profile_info = $this->db->where('user_id', $user_id)->get('tbl_account_details')->row();
    $user_info = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
    if (!empty(config_item('layout-h'))) {
        $ul_class = 'navbar-nav';
    }
    ?>
    <div class="aside-inner">
        <nav data-sidebar-anyclick-close="" class="sidebar">
            <?php
            echo $this->menu->dynamicMenu(); ?>
            <!-- END sidebar nav-->
        </nav>
    </div>
    <!-- END Sidebar (left)-->
</aside>
