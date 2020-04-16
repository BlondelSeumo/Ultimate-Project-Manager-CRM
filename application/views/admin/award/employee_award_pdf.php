<!DOCTYPE html>
<html>
<head>
    <title><?= lang('all_award') ?></title>
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
            background-color: rgb(224, 224, 224);
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
            font-size: 14px;
            border: 1px solid black;
        <?php if(!empty($RTL)){?> text-align: right;<?php }?>
        }

        .table_tr2 td {
            padding: 7px 0px 7px 8px;
            border: 1px solid black;
            font-size: 12px;
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
                <td style="padding: 10px;"><?= lang('employee_award_list') ?></td>
            </tr>
        </table>
    </div>
    <br/>
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <tr class="table_tr1">
            <td><?= lang('emp_id') ?></td>
            <td><?= lang('name') ?></td>
            <td><?= lang('award_name') ?></td>
            <td><?= lang('gift') ?></td>
            <td><?= lang('amount') ?></td>
            <td><?= lang('month') ?></td>
            <td><?= lang('award_date') ?></td>
        </tr>
        <?php
        $curency = $this->db->where('code', config_item('default_currency'))->get('tbl_currencies')->row();
        if (!empty($employee_award_info)):foreach ($employee_award_info as $v_award_info):
            $emp_info = $this->db->where('user_id', $v_award_info->user_id)->get('tbl_account_details')->row()
            ?>
            <tr class="table_tr2">
                <td><?php echo $emp_info->employment_id ?></td>
                <td><?php echo $emp_info->fullname ?></td>
                <td><?php echo $v_award_info->award_name; ?></td>
                <td><?php echo $v_award_info->gift_item; ?></td>
                <td><?php echo display_money($v_award_info->award_amount, $curency->symbol); ?></td>
                <td><?= strftime(date('M Y'), strtotime($v_award_info->award_date)) ?></td>
                <td><?= strftime(config_item('date_format'), strtotime($v_award_info->given_date)) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
</body>
</html>