<!DOCTYPE html>
<html>
<head>
    <title><?= lang('overtime_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
        .list {
            position: relative;
            display: block;
            padding: 5px;
            margin-bottom: -1px;
        }

        .xright {
            float: right;
            margin-left: 20px;
        }

        blockquote {
            padding: 10.5px 21px;
            margin: 0 0 21px;
            font-size: 17.5px;
            border-left: 5px solid #edf1f2;
        }

        .table_tr1 td {
            padding: 7px 0px 7px 8px;
            font-weight: bold;
        }

        .table_tr2 td {
            padding: 7px 0px 7px 8px;
            border: 1px solid black;
        }

        .total_amount td {
            padding: 7px 8px 7px 0px;
            border: 1px solid black;
            font-size: 15px;
        }
    </style>
</head>
<body style="min-width: 100%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 60px; border: 0px;">
                <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                     src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
            </td>
            <td style="border: 0px;">
                <h4 style="margin: 0;padding: 0"><?= config_item('company_name') ?></h4>
                <?= lang('address') . ': ' . config_item('company_address') ?>
                <?= lang('city') . ': ' . config_item('company_city') ?>,
                <?= lang('country') . ': ' . config_item('company_country') . '-' . config_item('company_zip_code') ?>
                ,<?= lang('phone') ?> : <?= config_item('company_phone') ?>
            </td>
        </tr>
    </table>
</div>
<br/>
<table style="width: 100%;">
    <tr style="width: 32%;background-color: rgba(84, 73, 73, 0.12);">
        <td style="background-color: rgba(84, 73, 73, 0.12);">
            <table style="width: 100%;background-color: rgba(84, 73, 73, 0.12)">
                <tr style="font-size: 20px;  text-align: center;background-color: rgba(84, 73, 73, 0.12)">
                    <td style="padding: 10px;background-color: rgba(84, 73, 73, 0.12)"><?= lang('quotation_details'); ?></td>
                </tr>
            </table>
            <ul class="list">
                <?php
                $client_info = $this->quotations_model->check_by(array('client_id' => $quotations_info->client_id), 'tbl_client');
                $user_info = $this->quotations_model->check_by(array('user_id' => $quotations_info->user_id), 'tbl_users');
                $reviewer_info = $this->quotations_model->check_by(array('user_id' => $quotations_info->reviewer_id), 'tbl_users');

                if (!empty($user_info)) {
                        if ($user_info->role_id == 1) {
                            $user = lang('admin');
                            $label = 'danger';
                        } elseif ($user_info->role_id == 2) {
                            $user = lang('client');
                            $label = 'success';
                        } else {
                            $user = lang('staff');
                            $label = 'primary';
                        }
                    } else {
                        $user = null;
                        $label = null;
                    }

                if (!empty($reviewer_info)) {

                    if ($reviewer_info->role_id == 1) {
                        $r_user = lang('admin');
                        $r_label = 'danger';
                    } elseif ($reviewer_info->role_id == 2) {
                        $r_user = lang('client');
                        $r_label = 'success';
                    } else {
                        $r_user = lang('staff');
                        $r_label = 'primary';
                    }
                } else {
                    $user = null;
                    $r_label = null;
                }
                $currency = $this->quotations_model->client_currency_symbol($quotations_info->client_id);
 if (empty($currency)) {
                        $currency = $this->quotations_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    }
                if (!empty($client_info)) {
                    if ($client_info->client_status == 1) {
                        $client_status = '' . lang('person') . '';
                    } else {
                        $client_status = '' . lang('company') . '';
                    }
                } else {
                    $client_status = '';
                }
                ?>
                <li class="list"><?= lang('client') ?><span
                        class="xright"><?= $quotations_info->name . ' ' . $client_status; ?></span>
                </li>
                <li class="list"><?= lang('email') ?><span
                        class="xright"><?= $quotations_info->email ?></span></li>
                <li class="list"><?= lang('mobile') ?><span
                        class="xright"><?= $quotations_info->mobile; ?></span></li>
                <li class="list"><?= lang('date') ?><span
                        class="xright"><?= strftime(config_item('date_format'), strtotime($quotations_info->quotations_date)) ?></span>
                </li>
                <li class="list"><?= lang('status') ?><span class="xright"><?php
                        if ($quotations_info->quotations_status == 'completed') {
                            echo '<span class="label label-success">' . lang('completed') . '</span>';
                        } else {
                            echo '<span class="label label-danger">' . lang('pending') . '</span>';
                        };
                        ?></span></li>

                <li class="list"><?= lang('generated_by') ?><span
                        class="xright"><?= (!empty($user_info->username) ? $user_info->username : '-')  . ' <small class="label label-' . $label . '">(' . $user . ')</small>'; ?></span>
                </li>
                <?php if (!empty($reviewer_info)): ?>
                    <li class="list"><?= lang('reviewer') ?><span
                            class="xright"><?= $reviewer_info->username . ' <small class="label label-' . $r_label . '"> (' . $r_user . ')</small>'; ?></span>
                    </li>
                    <li class="list"><?= lang('reviewed_date') ?><span
                            class="xright"><?= strftime(config_item('date_format'), strtotime($quotations_info->reviewed_date)) ?></span>
                    </li>
                <?php endif; ?>
                <li class="list"> <?= lang('amount') ?>
                    <span class="xright">
                        <?php
                        if ($quotations_info->quotations_amount) {
                            echo display_money($quotations_info->quotations_amount, $currency->symbol);
                        }
                        ?>
                            </span>

                </li>
                <?= lang('comments') ?>
                <blockquote
                    style="font-size: 12px;word-wrap: break-word "
                    class="mt-lg"><?php if (!empty($quotations_info->notes)) echo strip_html_tags($quotations_info->notes,true); ?></blockquote>

            </ul>
        </td>
    </tr>
    <tr style="width: 67%;">
        <td style="background-color: rgb(224, 224, 224); padding: 1px 0px 5px 15px;float: right">
            <table style="width: 100%;">
                <tr style="font-size: 20px;  text-align: center">
                    <td style="padding: 10px;"><?= lang('quotaion_form_response'); ?></td>
                </tr>
            </table>
            <?php if (!empty($quotation_details)):foreach ($quotation_details as $v_q_details): ?>
                <label class="control-label"> <strong><?= $v_q_details->quotation_form_data ?></strong></label>
                <?php
                if (@unserialize($v_q_details->quotation_data)) {
                    $multiple_data = unserialize($v_q_details->quotation_data);
                    foreach ($multiple_data as $key => $value) {
                        ?>
                        <p style="word-wrap: break-word"><span><?= $key + 1 . '.' . $value ?></span></p>
                        <?php
                    }
                } else {
                    ?>
                    <p style="word-wrap: break-word"><span><?= $v_q_details->quotation_data ?></span></p>
                <?php } ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </td>
    </tr>
</table>