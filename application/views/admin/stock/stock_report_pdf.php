<!DOCTYPE html>
<html>
<head>
    <title><?= lang('assign_stock') . ' ' . lang('report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    } ?>
    <style>
        th {
            padding: 10px 0px 5px 5px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?> font-size: 13px;
            border: 1px solid black;
        }

        td {
            padding: 5px 0px 0px 5px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?> border: 1px solid black;
            font-size: 13px;
        }

        .col-md-6 {
            width: 100%;
        }

        .col-md-12 {
            width: 100%;
        }

        .custom-bg {
            background: #f0f0f0;
        }

        .p {
            padding: 10px !important;
        }

        .text-center {
            text-align: center;
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<?php
$img = ROOTPATH . '/' . config_item('company_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('company_logo');
}
if (!file_exists($img)) {
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
    <table style="width: 100%; background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;">
        <tr style="font-size: 20px;  text-align: center">
            <?php if (!empty($stock_id)) { ?>
                <td style="padding: 10px;border: 0;text-align: center"><?= lang('items_name') ?>
                    <strong><?php
                        $items_name = $this->db->where('stock_id', $stock_id)->get('tbl_stock')->row();
                        echo $items_name->item_name; ?>
                    </strong>
                </td>
            <?php } else { ?>
                <td style="padding: 10px;border: 0;text-align: center"><?= lang('stock') . ' ' . lang('report_from') ?>
                    <strong>
                        <?= strftime(config_item('date_format'), strtotime($start_date)); ?></strong>
                </td>
                <td style="padding: 10px;border: 0"><?= lang('stock') . ' ' . lang('report_to') ?><strong>
                        <?= strftime(config_item('date_format'), strtotime($end_date)); ?></strong>
                </td>
                <?php
            } ?>
        </tr>
    </table>
</div>
<?php
if (!empty($assign_stock) && !empty($purchase_stock)) {
    $col = 'col-md-12';
} else {
    $col = 'col-md-12';
}
if (!empty($assign_stock) || !empty($purchase_stock)) { ?>
    <?php if (!empty($purchase_stock)) { ?>
        <div class="<?= $col ?>">
        <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
            <div class="custom-bg p text-center">
                <strong><?= lang('stock_list') ?></strong>
            </div>
            <?php foreach ($purchase_stock as $item_name => $v_purchase_stoc) { ?>
                <tr style="width: 100%; background-color: rgb(224, 224, 224);margin-top: 15px;padding-bottom: 5px">
                    <td colspan="4"><strong><?php echo $item_name; ?></strong></td>
                </tr>
                <tr>
                    <th><?= lang('sl') ?></th>
                    <th><?= lang('item_name') ?></th>
                    <th><?= lang('inventory') ?></th>
                    <th><?= lang('buying_date') ?></th>
                </tr>
                <?php
                $total_inventory = 0;
                if (!empty($v_purchase_stoc)) {
                    foreach ($v_purchase_stoc as $p_sl => $v_stock) {
                        ?>
                        <tr style="width: 100%;">
                            <td><?php echo $p_sl + 1 ?></td>
                            <td><?php echo $v_stock->item_name ?></td>
                            <td><?php echo $v_stock->inventory; ?> </td>
                            <td><?= strftime(config_item('date_format'), strtotime($v_stock->purchase_date)); ?></td>
                            <?php
                            $total_inventory += $v_stock->inventory;
                            ?>
                        </tr>
                    <?php }
                ; ?>
                    <tr>
                        <th colspan="2" style="text-align: right;"><strong
                                style="margin-right: 5px"> <?= lang('total') . ' ' . lang('assigned') . ': ' ?> </strong>
                        </th>
                        <td><?= $total_inventory - $v_stock->total_stock ?></td>
                        <td>
                        <span
                            style="padding-right: 20px;text-align: right;display: inline-block;overflow: hidden; "> <?= lang('available_stock') ?>
                            : <strong> <?php echo $v_stock->total_stock; ?> </strong></span>
                        </td>
                    </tr>
                <?php }; ?>
            <?php }; ?>
        </table>
    <?php } else { ?>
        <tr>
            <td colspan="3">
                <strong><?= lang('nothing_to_display') ?></strong>
            </td>
        </tr>
    <?php } ?>
    </div>
    <?php if (!empty($assign_stock)) { ?>
        <div class="<?= $col ?>">
        <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
            <div class="custom-bg p text-center">
                <strong><?= lang('assign_stock_list') ?></strong>
            </div>
            <?php foreach ($assign_stock as $item_name => $v_assign_report) { ?>
                <tr style="width: 100%; background-color: rgb(224, 224, 224);margin-top: 15px;padding-bottom: 5px">
                    <td colspan="4"><strong><?php echo $item_name; ?></strong></td>
                </tr>
                <tr>
                    <th><?= lang('sl') ?></th>
                    <th><?= lang('assigned_user') ?></th>
                    <th><?= lang('assign_date') ?></th>
                    <th><?= lang('assign_quantity') ?></th>
                </tr>
                <?php
                $total_assign_inventory = 0;
                if (!empty($v_assign_report)) {
                    foreach ($v_assign_report as $ssl => $v_report) {
                        ?>

                        <tr style="width: 100%;">
                            <td><?php echo $ssl + 1 ?></td>
                            <td><?php echo $v_report->fullname ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($v_report->assign_date)); ?></td>
                            <td><?php echo $v_report->assign_inventory; ?> </td>
                            <?php
                            $total_assign_inventory += $v_report->assign_inventory;
                            ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th style="text-align: right;" colspan="3"><strong
                                style="margin-right: 5px"> <?= lang('total') ?> <?php echo $v_report->item_name ?>
                                : </strong></th>
                        <td><span><?php
                                echo $total_assign_inventory;
                                ?></span>
                        <span
                            style="padding-right: 20px;text-align: right;display: inline-block;overflow: hidden; "> <?= lang('available_stock') ?>
                            : <strong> <?php echo $v_report->total_stock; ?> </strong></span>
                        </td>
                    </tr>
                <?php }; ?>
            <?php }; ?>
        </table>
    <?php } else { ?>
        <tr>
            <td colspan="4">
                <strong><?= lang('nothing_to_display') ?></strong>
            </td>
        </tr>
    <?php } ?>
    </div>
<?php }; ?>
</div>
</body>
</html>
