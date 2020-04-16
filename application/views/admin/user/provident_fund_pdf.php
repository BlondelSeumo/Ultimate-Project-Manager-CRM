<!DOCTYPE html>
<html>
<head>
    <title>Provident Fund</title>
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
        .table_tr1 th{
            background-color: rgb(224, 224, 224);
            height: 40px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
            border: 1px solid black;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 td {
            padding: 7px 0px 7px 8px;
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
                <td style="padding: 10px;"><?= lang('provident_found_report') . ' ' . lang('for') . ' ' . $user_info->fullname ?><?php echo ' ' . $monthyaer ?></td>
            </tr>
        </table>
    </div>
    <br/>
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr class="table_tr1">
            <th><?= lang('payment_month') ?></th>
            <th><?= lang('payment_date') ?></th>
            <th><?= lang('amount') ?></th>
        </tr>
        <?php
        $total_amount = 0;
        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
        ?>
        <?php if (!empty($provident_fund_info)) {
            foreach ($provident_fund_info as $key => $v_provident_fund) {
                $month_name = date('F', strtotime($monthyaer . '-' . $key)); // get full name of month by date query

                $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
                if (!empty($v_provident_fund)) {
                    foreach ($v_provident_fund as $provident_fund) { ?>
                        <tr>
                            <td><?php echo $month_name ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($provident_fund->paid_date)) ?></td>
                            <td><?php echo display_money($provident_fund->salary_payment_deduction_value, $curency->symbol);
                                $total_amount += $provident_fund->salary_payment_deduction_value;
                                ?></td>

                        </tr>
                        <?php
                        $key++;
                    };
                    $total_amount = $total_amount;
                };

                ?>


            <?php } ?>
            <tr class="total_amount">
                <td colspan="2" style="text-align: right;">
                    <strong><?= lang('total') . ' ' . lang('provident_fund') ?>
                        : </strong></td>
                <td colspan="3" style="padding-left: 8px;"><strong><?php
                        echo display_money($total_amount, $curency->symbol);
                        ?></strong></td>
            </tr>
        <?php }; ?>
    </table>
</div>
</body>
</html>