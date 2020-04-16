
<div class="panel panel-custom">
    <!-- Default panel contents -->

    <div class="panel-heading">
        <div class="panel-title">
            <strong>
                <?= lang('jobs_application_details') ?>
            </strong>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                    class="sr-only">Close</span></button>
        </div>
    </div>
    <div class="panel-body form-horizontal">
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('job_title') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $job_application_info->job_title; ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('designation') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php
                    if (!empty($job_application_info->designations_id)) {
                        $design_info = $this->db->where('designations_id', $job_application_info->designations_id)->get('tbl_designations')->row();
                        $designation = $design_info->designations;
                    } else {
                        $designation = '-';
                    }
                    echo $designation;
                    ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('name') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $job_application_info->name ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('email') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $job_application_info->email ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('mobile') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $job_application_info->mobile ?></p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('cover_later') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static">
                <blockquote style="font-size: 12px;word-wrap: break-word;"><?php
                    if (!empty($job_application_info->cover_letter)) {
                        echo $job_application_info->cover_letter;
                    }
                    ?></blockquote>
                </p>
            </div>
        </div>
        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('apply_date') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static text-justify"><?= strftime(config_item('date_format'), strtotime($job_application_info->apply_date)) ?></p>
            </div>
        </div>
        <?php if (!empty($job_application_info->send_email)) { ?>
            <div class="col-md-12">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('send_email') ?> : </strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static text-justify"><?= $job_application_info->send_email ?></p>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($job_application_info->interview_date)) { ?>
            <div class="col-md-12">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('interview_date') ?> : </strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static text-justify"><?= strftime(config_item('date_format'), strtotime($job_application_info->interview_date)) ?></p>
                </div>
            </div>
        <?php } ?>


        <div class="col-md-12">
            <div class="col-sm-4 text-right">
                <label class="control-label"><strong><?= lang('resume') ?> : </strong></label>
            </div>
            <div class="col-sm-8">
                <p class="form-control-static pull-left">
                    <a class="label label-info"
                       href="<?php echo base_url(); ?>admin/job_circular/download_resume/<?= $job_application_info->resume ?>"
                       style="text-decoration: none;background: #22313F;"><?= lang('download') . ' ' . lang('resume') ?></a>
                </p>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
    </div>
</div>





