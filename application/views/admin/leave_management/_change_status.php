<style type="text/css">
    #myModal {
        z-index: 1051 !important;
    }
</style>
<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <?php
        if ($status == '1') {
            $text = lang('pending');
        } elseif ($status == '2') {
            $approved = true;
            $text = lang('approved');
        } else {
            $status = 3;
            $text = lang('rejected');
        }
        ?>
        <h4 class="modal-title"
            id="myModalLabel"><?= lang('change') . ' ' . lang('status') . ' ' . lang('leave_to') . ' ' . $text

            ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form_validation"
              action="<?php echo base_url() ?>admin/leave_management/set_action/<?php if (!empty($application_info->leave_application_id)) echo $application_info->leave_application_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group ">
                <label for="field-1" class="col-sm-3 control-label row"><?= lang('give_comment') ?>: </label>

                <div class="col-sm-8">
                    <textarea class="form-control" name="comment"><?php echo $application_info->comments; ?></textarea>

                </div>
                <!-- Hidden Input ---->
                <input type="hidden" name="application_status"
                       value="<?php echo $status ?>">
                <input type="hidden" name="approve_by" value="<?php echo $this->session->userdata('user_id') ?>">
                <input type="hidden" name="user_id" value="<?php echo $application_info->user_id; ?>">
                <input type="hidden" name="leave_category_id"
                       value="<?php echo $application_info->leave_category_id; ?>">
                <input type="hidden" name="leave_start_date" value="<?php echo $application_info->leave_start_date; ?>">
                <?php
                if (empty($application_info->leave_end_date)) {
                    $application_info->leave_end_date = $application_info->leave_start_date;
                }
                ?>
                <input type="hidden" name="leave_end_date" value="<?php echo $application_info->leave_end_date; ?>">
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" <?php
                if (!empty($approved)) {
                    ?>
                    onclick="return confirm('<?= lang('delete_leave_alert') ?>')"
                <?php }
                ?> class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('body').on('hidden.bs.modal', '.modal', function () {
        location.reload();
    });
</script>