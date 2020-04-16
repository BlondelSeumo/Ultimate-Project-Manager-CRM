<!DOCTYPE html>
<html>
<head>
    <title><?= lang('report_by_month') ?></title>
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
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
            background-color: rgb(224, 224, 224);
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 td {
            padding: 5px;
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
            <td>
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
                <td style="padding: 10px;"><?= lang('report_by_month_for') ?><?php echo $monthyaer ?></td>
            </tr>
        </table>
    </div>
    <br/>
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr class="table_tr1">
            <th style="width: 15%"><?= lang('date') ?></th>
            <th style="width: 15%"><?= lang('account') ?></th>
            <th><?= lang('type') ?></th>
            <th><?= lang('notes') ?></th>
            <th><?= lang('amount') ?></th>
            <th><?= lang('credit') ?></th>
            <th><?= lang('debit') ?></th>
            <th><?= lang('balance') ?></th>
        </tr>
        <?php
        $total_amount = 0;
        $total_debit = 0;
        $total_credit = 0;
        $total_balance = 0;
        $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
        if (!empty($report_list)): foreach ($report_list as $v_month) :
            $account_info = $this->report_model->check_by(array('account_id' => $v_month->account_id), 'tbl_accounts');
            ?>
            <tr class="table_tr2">
                <td><?= strftime(config_item('date_format'), strtotime($v_month->date)); ?></td>
                <td><?= $account_info->account_name ?></td>
                <td><?= lang($v_month->type) ?> </td>
                <td><?= strip_html_tags($v_month->notes,true) ?></td>
                <td><?= display_money($v_month->amount, $curency->symbol) ?></td>
                <td><?= display_money($v_month->credit, $curency->symbol) ?></td>
                <td><?= display_money($v_month->debit, $curency->symbol) ?></td>
                <td><?= display_money($v_month->total_balance, $curency->symbol) ?></td>
            </tr>
            <?php
            $total_amount += $v_month->amount;
            $total_debit += $v_month->debit;
            $total_credit += $v_month->credit;
            $total_balance += $v_month->total_balance;
            ?>
        <?php endforeach; ?>
            <tr class="table_tr2">
                <td style="text-align: right;" colspan="4"><strong><?= lang('total') ?>:</strong></td>
                <td>
                    <strong><?= display_money($total_amount, $curency->symbol) ?></strong>
                </td>
                <td>
                    <strong><?= display_money($total_credit, $curency->symbol) ?></strong>
                </td>
                <td>
                    <strong><?= display_money($total_debit, $curency->symbol) ?></strong>
                </td>
                <td>
                    <strong><?= display_money($total_credit - $total_debit, $curency->symbol) ?></strong>
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>