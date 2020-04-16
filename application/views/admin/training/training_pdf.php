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
<div style="padding: 5px 0; width: 100%;">
    <div>
        <table style="width: 100%; border-radius: 3px;">
            <tr>
                <td style="width: 150px;">
                    <table style="border: 1px solid grey;">
                        <tr>
                            <td style="background-color: lightgray; border-radius: 2px;">
                                <?php if ($training_info->avatar): ?>
                                    <img src="<?php echo base_url() . $training_info->avatar; ?>"
                                         style="width: 138px; height: 144px; border-radius: 3px;">
                                <?php else: ?>
                                    <img alt="Employee_Image">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table style="width: 500px; margin-left: 10px; margin-bottom: 10px; font-size: 13px;">
                        <tr>
                            <td style="width: 30%;"><h2><?php echo "$training_info->fullname"; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('emp_id') ?> : </strong></td>
                            <td style="width: 70%"><?php echo "$training_info->employment_id "; ?></td>
                        </tr>
                        <?php
                        $design_info = $this->db->where('designations_id', $training_info->designations_id)->get('tbl_designations')->row();
                        $dept_info = $this->db->where('departments_id', $design_info->departments_id)->get('tbl_departments')->row();

                        ?>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('departments') ?> : </strong></td>
                            <td style="width: 70%"><?php echo "$dept_info->deptname"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('designation') ?> :</strong></td>
                            <td style="width: 70%"><?php echo "$design_info->designations"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;"><strong><?= lang('joining_date') ?>: </strong></td>
                            <td style="width: 70%"><?= strftime(config_item('date_format'), strtotime($training_info->joining_date)) ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<div style="width: 100%;">
    <div>
        <div style="width: 100%; background: #E3E3E3;padding: 1px 0px 1px 10px; color: black; vertical-align: middle; ">
            <p style="margin-left: 10px; font-size: 15px; font-weight: lighter;">
                <strong><?= lang('training_details') ?></strong></p>
        </div>

        <table style="width: 100%; font-size: 13px;margin-top: 20px">
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('course_training') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo $training_info->training_name;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('vendor') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    echo $training_info->vendor_name;
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('start_date') ?> :</strong></td>

                <td style="">&nbsp; <?php
                    echo strftime(config_item('date_format'), strtotime($training_info->start_date));
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('finish_date') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    echo strftime(config_item('date_format'), strtotime($training_info->finish_date));
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('training_cost') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                    echo display_money($training_info->training_cost, $curency->symbol);
                    ?></td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('status') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    if ($training_info->status == '0') {
                        echo '<span class="label label-warning">' . lang('pending') . ' </span>';
                    } elseif ($training_info->status == '1') {
                        echo '<span class="label label-info">' . lang('started') . '</span>';
                    } elseif ($training_info->status == '2') {
                        echo '<span class="label label-success"> ' . lang('completed') . ' </span>';
                    } else {
                        echo '<span class="label label-danger"> ' . lang('terminated ') . '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: right"><strong><?= lang('performance') ?> :</strong>
                </td>

                <td style="">&nbsp; <?php
                    if ($training_info->performance == '0') {
                        echo '<span class="label label-warning">' . lang('not_concluded') . ' </span>';
                    } elseif ($training_info->performance == '1') {
                        echo '<span class="label label-info">' . lang('satisfactory') . '</span>';
                    } elseif ($training_info->performance == '2') {
                        echo '<span class="label label-primary"> ' . lang('average') . ' </span>';
                    } elseif ($training_info->performance == '3') {
                        echo '<span class="label label-danger"> ' . lang('poor') . ' </span>';
                    } else {
                        echo '<span class="label label-success"> ' . lang('excellent ') . '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php echo strip_html_tags($training_info->remarks,true) ?></td>
            </tr>

        </table>

    </div>
</div><!-- ***************** Salary Details  Ends *********************-->

</body>
</html>