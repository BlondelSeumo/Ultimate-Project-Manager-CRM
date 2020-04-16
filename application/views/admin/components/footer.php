<div class="pusher"></div>
<!-- ===============  SCRIPTS ===============-->

<!-- MODERNIZR-->
<script src="<?= base_url() ?>assets/plugins/modernizr/modernizr.custom.js"></script>
<!-- BOOTSTRAP-->
<script src="<?= base_url() ?>assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- STORAGE API-->
<script src="<?= base_url() ?>assets/plugins/jQuery-Storage-API/jquery.storageapi.min.js"></script>
<!-- ANIMO-->
<script src="<?= base_url() ?>assets/plugins/animo.js/animo.min.js"></script>
<?php if (empty($select_2)) { ?>
    <!-- SELECT2-->
    <script src="<?= base_url() ?>assets/plugins/select2/dist/js/select2.min.js"></script>
<?php } ?>
<!-- Data Table -->
<?php if (empty($dataTables)) { ?>
    <?php include_once 'assets/plugins/dataTables/js/jquery.dataTables.min.php'; ?>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.print.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.colVis.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/jszip.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/pdfmake.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/vfs_fonts.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.select.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/dataTables/js/dataTables.bootstrapPagination.js"></script>
<?php } ?>
<!-- summernote Editor -->
<script src="<?php echo base_url() ?>assets/plugins/summernote/summernote.min.js"></script>
<?php if (empty($datepicker)) { ?>
    <!-- =============== Date and time picker ===============-->
    <?php include_once 'assets/js/bootstrap-datepicker.php'; ?>
<?php } ?>
<script src="<?= base_url() ?>assets/js/timepicker.min.js"></script>
<!-- bootstrap-slider -->
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
<!-- bootstrap-editable -->
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-editable/bootstrap-editable.min.js"></script>
<!-- jquery-classyloader -->
<script src="<?php echo base_url() ?>assets/plugins/jquery-classyloader/jquery.classyloader.min.js"></script>
<!-- =============== Toastr ===============-->
<script src="<?= base_url() ?>assets/js/toastr.min.js"></script>
<!-- =============== Toastr ===============-->
<script src="<?= base_url() ?>assets/js/jasny-bootstrap.min.js"></script>
<!-- EASY PIE CHART-->
<script src="<?php echo base_url() ?>assets/plugins/easy-pie-chart/jquery.easypiechart.min.js"></script>

<!-- sparkline CHART-->
<script src="<?php echo base_url() ?>assets/plugins/sparkline/index.min.js"></script>

<script src="<?php echo base_url() ?>assets/plugins/parsleyjs/parsley.min.js"></script>

<!--- bootstrap-select ---->
<link href="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<!--- push_notification ---->
<script src="<?php echo base_url() ?>assets/plugins/push_notification/push_notification.min.js"></script>

<script src='<?= base_url() ?>assets/plugins/jquery-validation/jquery.validate.min.js'></script>
<script src='<?= base_url() ?>assets/plugins/jquery-validation/jquery.form.min.js'></script>
<!--- dropzone ---->
<?php if (!empty($dropzone)) { ?>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.css">
    <script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>assets/plugins/dropzone/dropzone.custom.min.js"></script>
<?php } ?>
<!--- malihu-custom-scrollbar ---->
<link rel="stylesheet" type="text/css"
      href="<?= base_url() ?>assets/plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.min.css">
<script type="text/javascript"
        src="<?= base_url() ?>assets/plugins/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<?php
$realtime_notification = config_item('realtime_notification');
if (!empty($realtime_notification)) { ?>
    <!--    <script src="--><?php //echo base_url() ?><!--assets/plugins/pusher/pusher.min.js"></script>-->
    <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
    <script type="text/javascript">
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;
        <?php $pusher_options = array();
        if (!isset($pusher_options['cluster']) && config_item('pusher_cluster') != '') {
            $pusher_options['cluster'] = config_item('pusher_cluster');
        } ?>
        var pusher_options = <?php echo json_encode($pusher_options); ?>;
        var pusher = new Pusher("<?php echo config_item('pusher_app_key'); ?>", pusher_options);
        var channel = pusher.subscribe('notifications-channel-<?php echo $this->session->userdata('user_id'); ?>');
        channel.bind('notification', function (data) {
            fetch_notifications();
        });
    </script>
<?php } ?>
<!-- =============== APP SCRIPTS ===============-->
<script src="<?= base_url() ?>assets/js/app.js"></script>
<?php if (empty($dataTables)) { ?>
    <?php include_once 'assets/plugins/dataTables/js/dataTables.php'; ?>
<?php } ?>
<?php
$profile = profile();
$chat = false;
if (!empty($profile)) {
    $role = $profile->role_id;
    if ($role == 2) { // check client menu permission
        $chat_menu = get_row('tbl_client_role', array('user_id' => $profile->user_id, 'menu_id' => '19'));
        if (!empty($chat_menu)) {
            $chat = true;
        }
        $this->view = 'client/';
    } elseif ($role != 1) {// check staff menu permission
        if (!empty($profile->designations_id)) {
            $user_menu = get_row('tbl_user_role', array('designations_id' => $profile->designations_id, 'menu_id' => '139'));
            if (!empty($user_menu)) {
                $chat = true;
            }
        }
    } else if ($role == 1) {
        $chat = true;
    }
}
if (!empty($chat)) {
    ?>
    <!--star live_chat_section-->
    <?php $this->load->view('chat/chat_list') ?>
<?php } ?>
</body>

</html>
