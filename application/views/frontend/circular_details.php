<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<?php if (!empty($circular_details)):
$last_date = $circular_details->last_date;
$last_date = strtotime($last_date);
$current_time = strtotime(date('Y-m-d'));
if ($current_time > $last_date) {
    $ribon = 'danger';
    $text = lang('expire');
} elseif ($current_time == $last_date) {
    $ribon = 'info';
    $text = lang('last_date');
} else {
    $lastdate = date('Y-m-d', strtotime($circular_details->last_date));
    $today = date('Y-m-d');
    $datetime1 = new DateTime($today);
    $datetime2 = new DateTime($lastdate);
    $interval = $datetime1->diff($datetime2);

    $ribon = 'success';
    $text = $interval->format('%R%a') . lang('days');
}
$designation = '-';
if (!empty($circular_details->designations_id)) {
    $design_info = $this->db->where('designations_id', $circular_details->designations_id)->get('tbl_designations')->row();
    if (!empty($design_info)) {
        $designation = $design_info->designations;
    }
}
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('view_circular_details') ?></strong>
            <div class="pull-right">
                <?= btn_pdf('frontend/jobs_posted_pdf/' . $circular_details->job_circular_id) ?>
            </div>
        </div>
    </div>
    <div class="panel-body form-horizontal">
        <p>
            <strong
                style="font-size: 20px;: "><?= $circular_details->job_title . ' ( ' . $designation . ' ) ' ?></strong>
        </p>
        <div class="col-sm-8">
            <p class="m0">
                <strong><?= lang('experience') ?>: <?= $circular_details->experience ?></strong>

            </p>
            <p class="m0">
                <strong><?= lang('age') ?>: <?= $circular_details->age ?></strong>

            </p>
            <p class="m0">
                <strong><?= lang('salary_range') ?>: <?= $circular_details->salary_range ?></strong>

            </p>

            <p class="m0">
                <strong><?= lang('vacancy_no') ?>: <?= $circular_details->vacancy_no ?></strong>

            </p>
            <p class="m0">
                <strong><?= lang('employment_type') ?>
                    : <?= lang($circular_details->employment_type) ?></strong>

            </p>
            <p class="m0">
                <strong> <?= lang('posted_date') ?>
                    : <?= display_date($circular_details->posted_date) ?>
                </strong>
            </p>
            <p>

                <strong> <?= lang('last_date') ?>
                    : <?= display_date($circular_details->last_date) ?>
                </strong>
            </p>
            <?php $show_custom_fields = custom_form_label(14, $circular_details->job_circular_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <p>
                            <strong> <?= $c_label ?>
                                : <?= $v_fields ?>
                            </strong>
                        </p>
                    <?php }
                }
            }
            ?>

            <blockquote style="font-size: 12px"><?php echo $circular_details->description; ?></blockquote>
        </div>

        <div class="col-md-4">
            <div class="panel " style="border: none">
                <div class="panel-heading m0" style="border: none;background-color: #37474f;color: #fff">
                    <strong><?= lang('job_summery') ?></strong>
                </div>
                <div class="panel-body" style="background-color: #f5f5f5;">
                    <p class="m0">
                        <strong><?= lang('job_title') ?>: <?= $circular_details->job_title ?></strong>

                    </p>
                    <p class="m0">
                        <strong><?= lang('designation') ?>: <?= $designation ?></strong>

                    </p>
                    <p class="m0">
                        <strong><?= lang('experience') ?>: <?= $circular_details->experience ?></strong>

                    </p>
                    <p class="m0">
                        <strong><?= lang('age') ?>: <?= $circular_details->age ?></strong>

                    </p>
                    <p class="m0">
                        <strong><?= lang('salary_range') ?>: <?= $circular_details->salary_range ?></strong>

                    </p>
                    <p class="m0">
                        <strong><?= lang('vacancy_no') ?>: <?= $circular_details->vacancy_no ?></strong>
                    </p>
                    <p class="m0">
                        <strong><?= lang('employment_type') ?>
                            : <?= lang($circular_details->employment_type) ?></strong>

                    </p>
                    <p class="m0">
                        <strong> <?= lang('posted_date') ?>
                            : <?= display_date($circular_details->posted_date) ?>
                        </strong>
                    </p>
                    <p>

                        <strong> <?= lang('last_date') ?>
                            : <?= display_date($circular_details->last_date) ?>
                        </strong>
                    </p>

                </div>

            </div>

            <a href="<?= base_url() ?>frontend/apply_jobs/<?= $circular_details->job_circular_id ?>"
               class="btn btn-primary btn-block" data-toggle="modal"
               data-target="#myModal_lg"><?= lang('apply_now') ?></a>
        </div>
        <?php endif; ?>
    </div>
</div>
