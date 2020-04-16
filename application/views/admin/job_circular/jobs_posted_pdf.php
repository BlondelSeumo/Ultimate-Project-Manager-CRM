<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body style="width: 100%;">
<br/>
<?php
$img = ROOTPATH . '/' . config_item('company_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('company_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= $img ?>" alt="" class="img-circle"/>
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <div>
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('view_circular_details') ?></strong></p>
        </div>

        <table style="width: 100%; font-size: 13px;margin-top: 20px">
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('job_title') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $job_posted->job_title;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('designation') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    if (!empty($job_posted->designations_id)) {
                        $design_info = $this->db->where('designations_id', $job_posted->designations_id)->get('tbl_designations')->row();
                        if (!empty($design_info)) {
                            $designation = $design_info->designations;
                        } else {
                            $designation = '-';
                        }
                    } else {
                        $designation = '-';
                    }
                    echo $designation;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('experience') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $job_posted->experience;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('age') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $job_posted->age;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('salary_range') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $job_posted->salary_range;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('vacancy_no') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $job_posted->vacancy_no;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('posted_date') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    echo strftime(config_item('date_format'), strtotime($job_posted->posted_date));
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('last_date_to_apply') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo strftime(config_item('date_format'), strtotime($job_posted->last_date));
                    ?></td>
            </tr>
            <?php $show_custom_fields = custom_form_label(14, $job_posted->job_circular_id);

            if (!empty($show_custom_fields)) {
                foreach ($show_custom_fields as $c_label => $v_fields) {
                    if (!empty($v_fields)) {
                        ?>
                        <tr>
                            <td style="width: 30%;text-align: right"><strong><?= $c_label ?> :</strong>
                            </td>

                            <td style="">&nbsp; <?= $v_fields ?></td>
                        </tr>
                    <?php }
                }
            }
            ?>
            <tr>
                <span style="word-wrap: break-word;"><?php echo strip_html_tags($job_posted->description,true); ?></span>
            </tr>

        </table>

    </div>
</div><!-- ***************** Salary Details  Ends *********************-->

</body>
</html>