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
              action="<?php echo base_url() ?>admin/payroll/set_salary_status/<?php if (!empty($advance_salary->advance_salary_id)) echo $advance_salary->leave_application_id; ?>"
              method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group ">
                <label for="field-1" class="col-sm-3 control-label row"><?= lang('give_comment') ?>: </label>

                <div class="col-sm-8">
                    <textarea class="form-control" name="comment"><?php echo $advance_salary->comments; ?></textarea>

                </div>
                <!-- Hidden Input ---->
                <input type="hidden" name="status"
                       value="<?php echo $status ?>">
                <input type="hidden" name="approve_by" value="<?php echo $this->session->userdata('user_id') ?>">
            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="submit" <?php
                if (!empty($approved)) {
                    ?>
                    onclick="return confirm('<?= lang('delete_alert') ?>')"
                <?php }
                ?> class="btn btn-primary"><?= lang('update') ?></button>
            </div>
        </form>
    </div>
</div>
