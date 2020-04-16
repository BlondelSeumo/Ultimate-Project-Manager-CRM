<!DOCTYPE html>
<html>
<head>
    <title><?php
        if (!empty($title)) {
            echo $title;
        } else {
            config_item('company_name');
        }
        ?></title>
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        .table_tr1 {
            width: 100%;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr1 .th {
            border: 1px solid #aaaaaa;
            background-color: #dddddd;
            font-size: 12px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 th, .table_tr3 th, .table_tr1 .th, .table_tr3 td {
            padding: 3px 0px 3px 5px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr3 th {
            border-bottom: 1px solid #aaaaaa;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>

        }

        .table_tr3 td {
            border-bottom: 1px solid #dad3d3;
            font-size: 12px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr3 .td {
            font-size: 13px;
            background: #dee0e4;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .th1 {
            text-align: center;
            border: 1px solid #aaaaaa;

        }
    </style>
</head>
<body style="min-width: 100%; min-height: 100%; ; alignment-adjust: central;">
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
    <div style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
        <table style="width: 100%;">
            <tr style="font-size: 20px;  text-align: center">
                <td style="padding: 10px;">
                    <strong><?= lang('attendance_list') . ' ' . lang('of') . ' ' ?><?php echo $month; ?></strong>
                    <p><strong><?= lang('department') . ' : ' . $dept_name->deptname ?></strong></p>
                </td>
            </tr>
        </table>
    </div>
    <br/>

    <table class="table_tr1">
        <tr>
            <th style="width: 20%" class="th"><?= lang('name') ?></th>
            <?php foreach ($dateSl as $edate) : ?>
                <th class="th th1"><?php echo $edate ?></th>
            <?php endforeach; ?>
        </tr>
        <?php

        foreach ($attendance as $key => $v_employee) { ?>
            <tr>
                <td style="width: 20%;border: 1px solid #aaaaaa;"><?php echo $employee[$key]->fullname ?></td>
                <?php

                foreach ($v_employee as $v_result) {
                    ?>
                    <?php foreach ($v_result as $emp_attendance) { ?>
                        <td class="th1">
                            <?php
                            if ($emp_attendance->attendance_status == 1) {
                                echo '<span  style="padding:2px; 4px" class="label label-success std_p">' . lang('p') . '</span>';
                            }
                            if ($emp_attendance->attendance_status == '0') {
                                echo '<span style="padding:2px; 4px" class="label label-danger std_p">' . lang('a') . '</span>';
                            }
                            if ($emp_attendance->attendance_status == 'H') {
                                echo '<span style="padding:2px; 4px" class="label label-info std_p">' . lang('h') . '</span>';
                            }
                            if ($emp_attendance->attendance_status == 3) {
                                echo '<span style="padding:2px; 4px" class="label label-warning std_p">' . lang('l') . '</span>';
                            }
                            ?>
                        </td>
                    <?php }; ?>


                <?php }; ?>
            </tr>
        <?php }; ?>
    </table>
</div>
</body>
</html>