<!DOCTYPE html>
<html>
<head>
    <title><?= lang('income_report') ?></title>
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
<br/>

<h5><strong><?= lang('income_summary') ?></strong></h5>
<hr>
<p><?= lang('total_income') ?>: <?php
    $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
    $mdate = date('Y-m-d');
    //first day of month
    $first_day_month = date('Y-m-01');
    //first day of Weeks
    $this_week_start = date('Y-m-d', strtotime('previous sunday'));
    // 30 days before
    $before_30_days = date('Y-m-d', strtotime('today - 30 days'));

    $total_income = $this->db->select_sum('credit')->get('tbl_transactions')->row();
    $this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
    $this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
    $this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
    echo display_money($total_income->credit, $curency->symbol);
    ?></p>
<p><?= lang('total_income_this_month') ?>
    : <?= display_money($this_month->credit, $curency->symbol) ?></p>
<p><?= lang('total_income_this_week') ?>
    : <?= display_money($this_week->credit, $curency->symbol) ?></p>
<p><?= lang('total_income_last_30') ?>
    : <?= display_money($this_30_days->credit, $curency->symbol) ?></p>

<br/>
<h4><?= lang('last_deposit_income') ?></h4>
<hr>
<div style="width: 100%;">
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr>
            <th><?= lang('date') ?></th>
            <th><?= lang('account') ?></th>
            <th><?= lang('deposit_category') ?></th>
            <th><?= lang('paid_by') ?></th>
            <th><?= lang('description') ?></th>
            <th><?= lang('amount') ?></th>
            <th><?= lang('credit') ?></th>
            <th><?= lang('balance') ?></th>
        </tr>
        <?php
        $total_amount = 0;
        $total_credit = 0;
        $total_balance = 0;
        $all_deposit_info = $this->db->where(array('type' => 'Income'))->limit(20)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();

        if (!empty($all_deposit_info)):foreach ($all_deposit_info as $v_deposit) :
            $account_info = $this->report_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');
            $client_info = $this->report_model->check_by(array('client_id' => $v_deposit->paid_by), 'tbl_client');
            $category_info = $this->report_model->check_by(array('income_category_id' => $v_deposit->category_id), 'tbl_income_category');
            if (!empty($client_info)) {
                $client_name = $client_info->name;
            } else {
                $client_name = '-';
            }
            ?>
            <tr>
                <td style="width: 15%"><?= strftime(config_item('date_format'), strtotime($v_deposit->date)); ?></td>
                <td style="width: 15%"><?= !empty($account_info->account_name) ? $account_info->account_name : '-' ?></td>
                <td><?php
                    if (!empty($category_info)) {
                        echo $category_info->income_category;
                    } else {
                        echo '-';
                    }
                    ?></td>
                <td><?= $client_name ?></td>
                <td><?= strip_html_tags($v_deposit->notes,true) ?></td>
                <td><?= display_money($v_deposit->amount, $curency->symbol) ?></td>
                <td><?= display_money($v_deposit->debit, $curency->symbol) ?></td>
                <td><?= display_money($v_deposit->total_balance, $curency->symbol) ?></td>

            </tr>
            <?php
            $total_amount += $v_deposit->amount;
            $total_credit += $v_deposit->credit;
            $total_balance += $v_deposit->total_balance;
            ?>
            <?php
        endforeach;
            ?>
            <tr class="custom-color-with-td">
                <td style="text-align: right;" colspan="5"><strong><?= lang('total') ?>:</strong></td>
                <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
                <td><strong><?= display_money($total_credit, $curency->symbol) ?></strong></td>
                <td><strong><?= display_money($total_balance, $curency->symbol) ?></strong></td>
            </tr>
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
