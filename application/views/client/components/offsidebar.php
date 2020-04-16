<?php

$user_id = $this->session->userdata('user_id');
?>
<style type="text/css">
    .offsidebar {
        background-color: #3a3f51
    }
</style>
<aside class="offsidebar hide">
    <!-- START Off Sidebar (right)-->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" style="background:none;" id="control-sidebar-home-tab">
            <h2 style="color: #EFF3F4;font-weight: 100;text-align: center;">
                <?php echo date("l"); ?>
                <br/>
                <?php echo date("jS F, Y"); ?>
            </h2>
            <div id="idCalculadora"></div>

        </div><!-- /.tab-pane -->
    </div>
    <!-- END Off Sidebar (right)-->
</aside>
<link rel="stylesheet" href="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.css">
<script src="<?= base_url() ?>asset/js/plugins/calculator/SimpleCalculadorajQuery.js"></script>
<script>
    $("#idCalculadora").Calculadora({'EtiquetaBorrar': 'Clear', TituloHTML: ''});
</script>