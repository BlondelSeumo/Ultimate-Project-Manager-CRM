<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="row">
    <?php
    $all_job_circular = $this->db->where('status', 'published')->order_by('posted_date', 'DESC')->get('tbl_job_circular')->result();

    if (!empty($all_job_circular)):foreach ($all_job_circular as $v_job_circular):

        $last_date = $v_job_circular->last_date;
        $current_time = date('Y-m-d');
        if ($current_time > $last_date) {
            $ribon = 'danger';
            $text = lang('expired');
        } elseif ($current_time == $last_date) {
            $ribon = 'info';
            $text = lang('last_date');
        } else {
            $lastdate = date('Y-m-d', strtotime($v_job_circular->last_date));
            $today = date('Y-m-d');
            $datetime1 = new DateTime($today);
            $datetime2 = new DateTime($lastdate);
            $interval = $datetime1->diff($datetime2);

            $ribon = 'success';
            $text = $interval->format('%R%a') . lang('days');
        }

        $design_info = $this->db->where('designations_id', $v_job_circular->designations_id)->get('tbl_designations')->row();
        if (!empty($design_info->designations)) {
            $designation = $design_info->designations;
        } else {
            $designation = '-';
        }
        ?>
        <div class="col-lg-4">
            <!-- START widget-->
            <div class="panel widget">
                <div class="row row-table row-flush">

                    <div class="panel-body">
                        <div class="invoice-ribbon">
                            <div class="ribbon-inner label-<?= $ribon ?>"><?= $text ?></div>
                        </div>
                        <p>
                            <a href="<?= base_url() ?>frontend/circular_details/<?= $v_job_circular->job_circular_id ?>">
                                <strong
                                    style="font-size: 17px;: "><?= $v_job_circular->job_title . ' ( ' . $designation . ' ) ' ?></strong>
                            </a>
                        </p>
                        <hr class=" mt0 row"/>
                        <p class="m0">
                            <strong><?= lang('experience') ?>: <?= $v_job_circular->experience ?></strong>
                            <strong class="pull-right"><?= lang('age') ?>
                                : <?= $v_job_circular->age ?></strong>
                        </p>
                        <p class="m0">
                            <strong><?= lang('vacancy_no') ?>: <?= $v_job_circular->vacancy_no ?></strong>
                            <strong class="pull-right"><?= lang('employment_type') ?>
                                : <?= lang($v_job_circular->employment_type) ?></strong>
                        </p>
                        <p>
                            <strong> <?= lang('posted_date') ?>
                                : <?= strftime(config_item('date_format'), strtotime($v_job_circular->posted_date)) ?>
                            </strong>
                            <strong class="pull-right"> <?= lang('last_date') ?>
                                : <?= strftime(config_item('date_format'), strtotime($v_job_circular->last_date)) ?>
                            </strong>
                        </p>
                        <p>

                            <?php
                            $max_len = 600; // Only show 300 characters //
                            $string = $v_job_circular->description;
                            echo strip_html_tags(strlen($string) > $max_len ? mb_substr($string, 0, $max_len) . ' <strong> .....</strong><a href="' . base_url() . 'frontend/circular_details/' . $v_job_circular->job_circular_id . '">' . lang('more') . '</a>' : $string,'<strong><a>');
                            ?>
                        <p>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    <?php endforeach; ?>
    <?php else: ?>
        <div class="col-lg-4">
            <!-- START widget-->
            <div class="panel widget">
                <div class="row row-table row-flush">

                    <div class="panel-body">
                        <?= lang('nothing_to_display') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
