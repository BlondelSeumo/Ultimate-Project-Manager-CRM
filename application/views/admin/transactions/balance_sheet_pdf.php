<!DOCTYPE html>
<html>
<head>
    <title><?= lang('balance_sheet') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }?>
    <style>
        th {
            padding: 10px 0px 5px 5px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
            font-size: 13px;
            border: 1px solid black;
        }

        td {
            padding: 5px 0px 0px 5px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
            border: 1px solid black;
            font-size: 13px;
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
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr>
            <th><?= lang('account') ?></th>
            <th><?= lang('balance') ?></th>
        </tr>
        <?php
        $curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        $total_amount = 0;
        $all_account = $this->db->get('tbl_accounts')->result();
        if ($all_account):
            foreach ($all_account as $v_account):
                ?>

                <tr style="width: 100%;">
                    <td class="vertical-td"><?php
                        if (!empty($v_account->account_name)) {
                            echo $v_account->account_name;
                        } else {
                            echo '-';
                        }
                        ?></td>
                    <td><?= display_money($v_account->balance, $curency->symbol) ?></td>
                </tr>

                <?php
                $total_amount += $v_account->balance;
                ?>
            <?php endforeach; ?>
            <tr class="custom-color-with-td">
                <th style="text-align: right;" colspan="1"><strong><?= lang('total') ?>:</strong></th>
                <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
            <tr>
        <?php else: ?>
            <tr>
                <td colspan="7">
                    <strong>There is no Report to display</strong>
                </td>
            </tr>
        <?php endif; ?>
    </table>

</div>
</body>
</html>
