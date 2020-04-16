<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title"
            id="myModalLabel"><?= lang('change_status') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form id="form" role="form" enctype="multipart/form-data"
              action="<?php echo base_url() ?>admin/job_circular/change_application_status/<?php
              if (!empty($job_application_info->job_appliactions_id)) {
                  echo $job_application_info->job_appliactions_id;
              }
              ?>" method="post" class="form-horizontal form-groups-bordered">

            <div class="form-group" id="border-none">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('status') ?><span
                        class="required"> *</span></label>
                <div class="col-sm-5">
                    <select class="form-control" id="job_applicarion_status" name="status">
                        <option
                            value="0" <?= ($job_application_info->application_status == 0 ? 'selected' : '') ?> ><?= lang('unread') ?></option>
                        <option
                            value="2" <?= ($job_application_info->application_status == 2 ? 'selected' : '') ?>><?= lang('primary_selected') ?></option>
                        <option
                            value="3" <?= ($job_application_info->application_status == 3 ? 'selected' : '') ?>><?= lang('call_for_interview') ?></option>
                        <option
                            value="1" <?= ($job_application_info->application_status == 1 ? 'selected' : '') ?>><?= lang('approved') ?></option>
                        <option
                            value="4" <?= ($job_application_info->application_status == 4 ? 'selected' : '') ?>><?= lang('rejected') ?></option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="flag" value="1"/>
            <div class="form-group send_email"
                 style="display: <?= ($job_application_info->application_status == 3 ? 'block' : 'none') ?>">
                <label for="field-1" class="col-sm-3 control-label"><?= lang('send_email') ?></label>
                <div class="col-sm-5">
                    <div class="checkbox-inline c-checkbox">
                        <label>
                            <input <?= (!empty($job_application_info->send_email) && $job_application_info->send_email == 'Yes' ? 'checked' : ''); ?>
                                class="select_one" type="checkbox" name="send_email" value="Yes">
                            <span class="fa fa-check"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group send_email"
                 style="display: <?= ($job_application_info->application_status == 3 ? 'block' : 'none') ?>">
                <label class="control-label col-sm-3"><?= lang('interview_date') ?><span
                        class="required">*</span></label>
                <div class="col-sm-5">
                    <div class="input-group ">
                        <input type="text" name="interview_date" value="<?php
                        if (!empty($job_application_info->interview_date)) {
                            echo $job_application_info->interview_date;
                        }
                        ?>" class="form-control datepicker interview_date" >
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-calendar"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary btn-block"><?= lang('update') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".interview_date").removeAttr('required');
        $("select[name='status']").change(function () {
            if ($("select[name='status']").val() == 3) {
                $('.send_email').show();
                $(".send_email").removeAttr('disabled');
                $(".interview_date").attr('required', true);
            } else {
                $('.send_email').hide();
                $(".interview_date").removeAttr('required');
                $(".send_email").attr('disabled', 'disabled');
            }
        });
    });
</script>


