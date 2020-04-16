<!-- Modal -->
<style type="text/css">
    .bootstrap-timepicker-widget.dropdown-menu.open {
        display: inline-block;
        z-index: 99999 !important;
    }
</style>
<div class="modal fade" id="myModal_extra_lg" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal_extra_lg">
        <div class="modal-content">

        </div>
    </div>
</div>
<?php if (!empty($select_2)) { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2.min.css">
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/plugins/select2/dist/css/select2-bootstrap.min.css">
    <script src="<?= base_url() ?>assets/plugins/select2/dist/js/select2.min.js"></script>
<?php } ?>

<?php if (!empty($datepicker)) { ?>
    <!-- =============== Datepicker ===============-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker.min.css">
    <?php include_once 'assets/js/bootstrap-datepicker.php'; ?>
<?php } ?>
<?php if (!empty($timepicker)) { ?>
    <!-- =============== timepicker ===============-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/timepicker.min.css">
    <script src="<?= base_url() ?>assets/js/timepicker.min.js"></script>
<?php } ?>
<script src="<?php echo base_url() ?>assets/plugins/parsleyjs/parsley.min.js"></script>

<?php $direction = $this->session->userdata('direction');
if (!empty($direction) && $direction == 'rtl') {
    $RTL = 'on';
} else {
    $RTL = config_item('RTL');
}
?>
<script type="text/javascript">
    $('#myModal_extra_lg').on('loaded.bs.modal', function () {
        $(function () {
            $('.selectpicker').selectpicker({});
            $('.select_box').select2({
                theme: 'bootstrap',
                <?php
                if (!empty($RTL)) {?>
                dir: "rtl",
                <?php }
                ?>
            });
            $('.select_multi').select2({
                theme: 'bootstrap',
                <?php
                if (!empty($RTL)) {?>
                dir: "rtl",
                <?php }
                ?>
            });
            $('.start_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayBtn: "linked"
                // update "toDate" defaults whenever "fromDate" changes
            }).on('changeDate', function () {
                // set the "toDate" start to not be later than "fromDate" ends:
                $('.end_date').datepicker('setStartDate', new Date($(this).val()));
            });

            $('.end_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayBtn: "linked"
// update "fromDate" defaults whenever "toDate" changes
            }).on('changeDate', function () {
                // set the "fromDate" end to not be later than "toDate" starts:
                $('.start_date').datepicker('setEndDate', new Date($(this).val()));
            });
            $('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayBtn: "linked",
            });
            $('.timepicker2').timepicker({
                minuteStep: 1,
                showSeconds: false,
                showMeridian: false,
                defaultTime: false
            });
            $('.textarea_2').summernote({
                height: 100,
                codemirror: {// codemirror options
                    theme: 'monokai'
                }
            });
            $('.note-toolbar .note-fontsize,.note-toolbar .note-help,.note-toolbar .note-fontname,.note-toolbar .note-height,.note-toolbar .note-table').remove();

            $('.textarea').summernote({
                height: 200,
                codemirror: {// codemirror options
                    theme: 'monokai'
                }
            });
            $('.note-toolbar .note-fontsize,.note-toolbar .note-help,.note-toolbar .note-fontname,.note-toolbar .note-height,.note-toolbar .note-table').remove();

            $('input.select_one').on('change', function () {
                $('input.select_one').not(this).prop('checked', false);
            });
        });
        $(document).ready(function () {
            // Init bootstrap select picker
            function init_selectpicker() {
                $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
                    showSubtext: true,
                });
            }

            $('#permission_user').hide();
            $("div.action").hide();
            $("input[name$='permission']").click(function () {
                $("#permission_user").removeClass('show');
                if ($(this).attr("value") == "custom_permission") {
                    $("#permission_user").show();
                } else {
                    $("#permission_user").hide();
                }
            });

            $("input[name$='assigned_to[]']").click(function () {
                var user_id = $(this).val();
                $("#action_" + user_id).removeClass('show');
                if (this.checked) {
                    $("#action_" + user_id).show();
                } else {
                    $("#action_" + user_id).hide();
                }

            });
        });
    });
    $('#myModal_extra_lg').on('hidden.bs.modal', function () {
        $(this).removeData('bs.modal');
//        location.reload();
    });

</script>
