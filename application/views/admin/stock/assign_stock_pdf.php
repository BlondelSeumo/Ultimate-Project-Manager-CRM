<!DOCTYPE html>
<html>
<head>
    <title>Assign Stock List</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }?>
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
        <?php if(!empty($RTL)){?> text-align: left;<?php }else{?>text-align: right;<?php }?>

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
                <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <div style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
        <table style="width: 100%;">
            <tr style="font-size: 20px;  text-align: center">
                <td style="padding: 10px;"><?= lang('assign_stock_list_for') . ' -' ?><strong><?php
                        if (!empty($employee)) {
                            echo $employee->fullname . ' (' . $employee->employment_id . ')';
                        }
                        ?></strong></td>
            </tr>
        </table>
    </div>
    <br/>
    <?php if (!empty($assign_list)): foreach ($assign_list as $sub_category => $v_assign_list) : ?>
        <?php if (!empty($v_assign_list)): ?>
            <div style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
                <table style="width: 100%;">
                    <tr style="font-size: 20px;">
                        <td style="padding: 5px;"><?php echo $sub_category ?></td>
                    </tr>
                </table>
            </div>
            <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
                <tr class="table_tr1">
                    <td style="border: 1px solid black;"><?= lang('sl') ?></td>
                    <td style="border: 1px solid black;"><?= lang('item_name') ?></td>
                    <td style="border: 1px solid black;"><?= lang('assign_quantity') ?></td>
                    <td style="border: 1px solid black;"><?= lang('assign_date') ?></td>
                </tr>

                <?php foreach ($v_assign_list as $key => $v_assign_stock) : ?>
                    <tr class="table_tr2">
                        <td><?php echo $key + 1 ?></td>
                        <td><?php echo $v_assign_stock->item_name ?></td>
                        <td><?php echo $v_assign_stock->assign_inventory ?></td>
                        <td><?= strftime(config_item('date_format'), strtotime($v_assign_stock->assign_date)); ?></td>
                    </tr>
                    <br/>
                    <?php
                endforeach;
                ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php else : ?>
        <div class="panel-body">
            <strong><?= lang('nothing_to_display') ?></strong>
        </div>
    <?php endif; ?>
</div>
</body>
</html>