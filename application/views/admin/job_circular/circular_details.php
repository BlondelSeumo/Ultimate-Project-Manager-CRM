<div class="panel panel-custom">
    <div class="panel-heading">

        <h4 class="modal-title"
            id="myModalLabel"><?= lang('view_circular_details') ?>
            <div class="pull-right">
                <?= btn_pdf('admin/job_circular/jobs_posted_pdf/' . $job_posted->job_circular_id) ?>
            </div>
        </h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <div class="panel-body form-horizontal">
            <div class="col-md-12 notice-details-margin">
                <div class="col-md-12 notice-details-margin">
                    <div class="col-sm-4 text-right">
                        <label class="control-label"><strong><?= lang('job_title') ?> :</strong></label>
                    </div>
                    <div class="col-sm-8">
                        <p class="form-control-static"><?= $job_posted->job_title; ?></p>
                    </div>
                </div>
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('designation') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if (!empty($job_posted->designations_id)) {
                            $design_info = $this->db->where('designations_id', $job_posted->designations_id)->get('tbl_designations')->row();
                            $designation = $design_info->designations;
                        } else {
                            $designation = '-';
                        }
                        echo $designation;
                        ?></p>
                </div>
            </div>


            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('employment_type') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= lang($job_posted->employment_type); ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('experience') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $job_posted->experience; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('age') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $job_posted->age; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('salary_range') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $job_posted->salary_range; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('vacancy_no') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= $job_posted->vacancy_no; ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('posted_date') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($job_posted->posted_date)) ?></p>
                </div>
            </div>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('last_date_to_apply') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?= strftime(config_item('date_format'), strtotime($job_posted->last_date)) ?></p>
                </div>
            </div>
            <?php $show_custom_fields = custom_form_label(14, $job_posted->job_circular_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <div class="col-md-12 notice-details-margin">
                            <div class="col-sm-4 text-right">
                                <label class="control-label"><strong><?= $c_label ?> :</strong></label>
                            </div>
                            <div class="col-sm-8">
                                <p class="form-control-static"><?= $v_fields ?></p>
                            </div>
                        </div>
                    <?php }
                }
            }
            ?>
            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('status') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <p class="form-control-static"><?php
                        if ($job_posted->status == 'unpublished') : ?>
                            <span class="label label-danger"><?= lang('unpublished') ?></span>
                        <?php else : ?>
                            <span class="label label-success"><?= lang('published') ?></span>
                        <?php endif; ?></p>
                </div>
            </div>

            <div class="col-md-12 notice-details-margin">
                <div class="col-sm-4 text-right">
                    <label class="control-label"><strong><?= lang('description') ?> :</strong></label>
                </div>
                <div class="col-sm-8">
                    <blockquote style="font-size: 12px"><?php echo $job_posted->description; ?></blockquote>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
    </div>
</div>
