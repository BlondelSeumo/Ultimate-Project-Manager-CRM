<!DOCTYPE html>
<html>
<head>
    <title><?= lang('transactions_report') ?></title>
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

<h5><strong><?= lang('income_expense') ?></strong></h5>
<?php
$curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
$mdate = date('Y-m-d');
//first day of month
$first_day_month = date('Y-m-01');
//first day of Weeks
$this_week_start = date('Y-m-d', strtotime('previous sunday'));
// 30 days before
$before_30_days = date('Y-m-d', strtotime('today - 30 days'));

$total_income = $this->db->select_sum('credit')->get('tbl_transactions')->row();
$total_expense = $this->db->select_sum('debit')->get('tbl_transactions')->row();

$income_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
$income_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
$income_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();

$expense_this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
$expense_this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
$expense_this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('debit')->get('tbl_transactions')->row();
?>
<hr>
<p><?= lang('total_income') ?>
    : <?= display_money($total_income->credit, $curency->symbol) ?></p>
<p><?= lang('total_expense') ?>
    : <?= display_money($total_expense->debit, $curency->symbol) ?></p>
<hr>
<p><strong><?= lang('Income') ?>
        - <?= lang('Expense') ?> </strong>: <?= display_money($total_income->credit - $total_expense->debit, $curency->symbol); ?>
</p>
<hr>
<p><?= lang('total_income_this_month') ?>
    : <?= display_money($income_this_month->credit, $curency->symbol) ?>
</p>
<p><?= lang('total_expense_this_month') ?>
    : <?= display_money($expense_this_month->debit, $curency->symbol) ?>
</p>
<p>
    <strong><?= lang('total') ?></strong>:
    <?= display_money($income_this_month->credit - $expense_this_month->debit, $curency->symbol) ?>
</p>
<hr>
<p><?= lang('total_income_this_week') ?>
    :<?= display_money($income_this_week->credit, $curency->symbol) ?></p>
<p><?= lang('total_expense_this_week') ?>
    : <?= display_money($expense_this_week->debit, $curency->symbol) ?></p>
<p>
    <strong><?= lang('total') ?></strong>: <?= display_money($income_this_week->credit - $expense_this_week->debit, $curency->symbol) ?>
</p>
<hr>
<p><?= lang('total_income_last_30') ?>
    : <?= display_money($income_this_30_days->credit, $curency->symbol) ?></p>
<p><?= lang('total_expense_last_30') ?>
    : <?= display_money($expense_this_30_days->debit, $curency->symbol) ?></p>
<p>
    <strong><?= lang('total') ?></strong>: <?= display_money($income_this_30_days->credit - $expense_this_30_days->debit, $curency->symbol) ?>
</p>

</body>
</html>
