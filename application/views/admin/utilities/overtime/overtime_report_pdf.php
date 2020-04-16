<!DOCTYPE html>
<html>
<head>
    <title><?= lang('overtime_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <style type="text/css">
        .table_tr1 {
            background-color: rgb(224, 224, 224);
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 td {
            padding: 7px 0px 7px 8px;
            border: 1px solid black;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .total_amount {
            background-color: rgb(224, 224, 224);
            font-weight: bold;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>

        }

        .total_amount td {
            padding: 7px 8px 7px 0px;
            border: 1px solid black;
            font-size: 15px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }
    </style>
</head>
<body style="min-width: 100%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
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
                <td style="padding: 10px;"><?= lang('overtime_report') ?><?php echo $monthyaer ?></td>
            </tr>
        </table>
    </div>
    <br/>
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr class="table_tr1">
            <td style="border: 1px solid black;"><?= lang('sl') ?></td>
            <td style="border: 1px solid black;"><?= lang('name') ?></td>
            <td style="border: 1px solid black;"><?= lang('overtime_date') ?></td>
            <td style="border: 1px solid black;"><?= lang('overtime_hour') ?></td>
        </tr>
        <?php
        $key = 1;
        $hh = 0;
        $mm = 0;
        ?>
        <?php
        if (!empty($overtime_info)):
            foreach ($overtime_info as $v_overtime) :
                ?>
                <tr class="table_tr2">
                    <td><?php echo $key ?></td>
                    <td><?php echo $v_overtime->fullname ?></td>
                    <td><?php echo strftime(config_item('date_format'), strtotime($v_overtime->overtime_date)); ?></td>
                    <td><?php echo display_time($v_overtime->overtime_hours); ?></td>
                    <?php $hh += date('h', strtotime($v_overtime->overtime_hours)); ?>
                    <?php $mm += date('i', strtotime($v_overtime->overtime_hours)); ?>

                </tr>
                <?php
                $key++;
            endforeach;
            ?>
            <tr class="total_amount">
                <td colspan="3" style="text-align: right"><span><?= lang('total_overtime_hour') ?>:</span></td>
                <td colspan="1" style="padding-left: 8px;"><?php
                    if ($hh >= 1 && $hh <= 9 || $mm >= 1 && $mm <= 9) { // if i<=9 concate with Mysql.becuase on Mysql query fast in two digit like 01.
                        $total_hh = '0' . $hh;
                        $total_mm = '0' . $mm;
                    } else {
                        $total_hh = $hh;
                        $total_mm = $mm;
                    }
                    if ($total_mm > 59) {
                        $total_hh += intval($total_mm / 60);
                        $total_mm = intval($total_mm % 60);
                    }
                    echo $total_hh . " : " . $total_mm . " m";
                    ?></td>
            </tr>
        <?php else : ?>
            <tr>
                <td style="border: 1px solid black;" colspan="7">
                    <strong><?= lang('nothing_to_display') ?></strong>
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>