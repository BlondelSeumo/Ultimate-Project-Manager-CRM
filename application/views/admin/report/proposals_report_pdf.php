<!DOCTYPE html>
<html>
<head>
    <title><?= lang('proposals_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }
    ?>
    <style>
        th {
            padding: 10px 0px 5px 5px;
            font-size: 13px;;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        td {
            padding: 5px 0px 0px 5px;
            font-size: 13px;
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }else{?> text-align: left;
        <?php }?>
        }

        .notes {
            color: #777;
            min-height: 20px;
            padding: 19px;
            margin-bottom: 20px;
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
        <?php if(!empty($RTL)){?> text-align: right;
        <?php }?>
        }
    </style>

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">

<br/>
<?php
$img = ROOTPATH . '/' . config_item('invoice_logo');
$a = file_exists($img);
if (empty($a)) {
    $img = base_url() . config_item('invoice_logo');
}
if(!file_exists($img)){
    $img = ROOTPATH . '/' . 'uploads/default_logo.png';
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>

            <td style="width: 35px; border: 0px;padding-bottom: 10px;">
                <img style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                     src="<?= $img ?>">
            </td>
            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 22px lighter;"><?= config_item('company_name') ?></p>
                <p style="color:#999;"><?= $this->config->item('company_address') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="padding:35px 0 50px;text-align:center"><span
        style="text-transform: uppercase; border-bottom:1px solid #eee;font-size:13pt;"><?= lang('proposals_report') ?></span>
    <?php
    if (!empty($range[0])) {
        $start_date = display_date($range[0]);
        $end_date = display_date($range[1]);
    }
    if (!empty($start_date)) { ?>
        <span style="margin-top: 10px;display: block;font-size: 15px;text-align: center"><?= lang('FROM') ?>
            &nbsp;<?= $start_date ?>
            &nbsp;<?= lang('TO') ?>&nbsp;<?= $end_date ?>
            </span>
    <?php } ?>
</div>

<table style="width:100%;margin-bottom:35px;table-layout:fixed;" cellpadding="0"
       cellspacing="0" border="0">
    <thead>
    <tr style="height:40px;background:#d6d6d6">
        <td style="padding:5px 10px 5px 10px;word-wrap: break-word;">
            <?= lang('estimate') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('proposal') . ' ' . lang('date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('expire') . ' ' . lang('date') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;"
            align="right">
            <?= lang('module') ?>#
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('tax') . ' ' . lang('amount') ?>
        </td>
        <td style="padding:5px 10px 5px 5px;word-wrap: break-word;text-align:right"
            align="right">
            <?= lang('discount') ?>
        </td>
    </tr>
    </thead>
    <tbody>
    <?php
    $cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
    $discount_total = 0;
    $proposal_total = 0;
    $total_tax = 0;
    if (!empty($all_proposals)) {
        foreach ($all_proposals as $v_proposals) {
            $proposal_total += $this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id);
            $total_tax += $this->proposal_model->get_proposal_tax_amount($v_proposals->proposals_id);
            $discount_total += $this->proposal_model->get_proposal_discount($v_proposals->proposals_id);
            ?>
            <tr style="border-bottom:1px solid #ededed">
                <td style="padding: 10px 0px 10px 10px;"
                    valign="top"><?= $v_proposals->reference_no ?></td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top">
                    <?= display_date($v_proposals->proposal_date) ?>
                </td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top">
                    <?= display_date($v_proposals->due_date) ?>
                </td>
                <td style="padding: 10px 10px 5px 10px;word-wrap: break-word;"
                    valign="top"><span>
                        <?php
                        if ($v_proposals->module == 'client') {
                            $client_info = $this->proposal_model->check_by(array('client_id' => $v_proposals->module_id), 'tbl_client');
                            if (!empty($client_info)) {
                                $client_name = $client_info->name;
                                $currency = $this->proposal_model->client_currency_symbol($v_proposals->module_id);
                            } else {
                                $client_name = '-';
                                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                            }
                        } else if ($v_proposals->module == 'leads') {
                            $client_info = $this->proposal_model->check_by(array('leads_id' => $v_proposals->module_id), 'tbl_leads');
                            if (!empty($client_info)) {
                                $client_name = $client_info->lead_name;
                            } else {
                                $client_name = '-';
                            }
                            $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        } else {
                            $client_name = '-';
                            $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                        }
                        echo $client_name;
                        ?>
                    </span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= display_money($this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id), $cur->symbol); ?></span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span> <?= display_money($this->proposal_model->get_proposal_tax_amount($v_proposals->proposals_id), $cur->symbol); ?></span>
                </td>
                <td style="text-align:right;padding: 10px 10px 10px 5px;word-wrap: break-word;"
                    valign="top">
                    <span><?= display_money($this->proposal_model->get_proposal_discount($v_proposals->proposals_id), $cur->symbol); ?></span>
                </td>
            </tr>
        <?php } ?>
        <tr style="height:50px;background:#d6d6d6">
            <td style="height:50px;padding-left: 15px" colspan="4"><?= lang('total') ?></td>
            <td style="text-align: right;height:50px"><?= display_money($proposal_total, $cur->symbol) ?></td>
            <td style="text-align: right;height:50px"><?= display_money($total_tax, $cur->symbol) ?></td>
            <td style="text-align: right;height:50px;padding-right: 15px"><?= display_money($discount_total, $cur->symbol) ?></td>
        </tr>

    <?php } ?>
    </tbody>
</table>
</body>
</html>
