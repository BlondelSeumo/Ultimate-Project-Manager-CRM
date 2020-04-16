<div class="row">
    <div class="col-sm-3">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <?= lang('all_payments') ?>
            </div>

            <div class="panel-body">
                <section class="scrollable  ">
                    <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                         data-size="5px" data-color="#333333">
                        <ul class="nav"><?php
                            if (!empty($all_payments_info)) {

                                foreach ($all_payments_info as $v_payments_info) {

                                    $client_info = $this->invoice_model->check_by(array('client_id' => $v_payments_info->paid_by), 'tbl_client');
                                    if (!empty($client_info)) {
                                        $c_name = $client_info->name;
                                        $currency = $this->invoice_model->client_currency_symbol($client_info->client_id);
                                    } else {
                                        $c_name = '-';
                                        $currency = $this->invoice_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                                    }
                                    ?>
                                    <li class="<?= ($v_payments_info->payments_id == $this->uri->segment(4) ? 'active' : '') ?>">
                                        <a href="<?= base_url() ?>client/invoice/manage_invoice/payments_details/<?= $v_payments_info->payments_id ?>">
                                            <?= $c_name ?>
                                            <div class="pull-right">
                                                <?= display_money($v_payments_info->amount, $currency->symbol); ?>
                                            </div>
                                            <br>
                                            <small class="block small text-info"><?= $v_payments_info->trans_id ?>
                                                | <?= strftime(config_item('date_format'), strtotime($v_payments_info->created_date)).' '.display_time($v_payments_info->created_date); ?> </small>

                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>

                    </div>
                </section>
            </div>
        </div>
    </div>
<?php
$img = base_url() . config_item('invoice_logo');
if (!file_exists($img)) {
    $img = base_url() . 'uploads/default_logo.png';
}

?>
    <section class="col-sm-9">
        <div class="row">

            <section class="panel panel-custom">
                <div class="panel-body">

                    <div class="details-page" style="margin:45px 25px 25px 8px">
                        <div class="details-container clearfix" style="margin-bottom:20px">
                            <div style="font-size:10pt;">

                                <div style="padding:35px;">
                                    <div style="padding-bottom:35px;border-bottom:1px solid #eee;width:100%;">
                                        <div>
                                            <div style="font-weight: bold;">
                                                <div class="pull-left">
                                                    <img
                                                        style="width: 60px;width: 60px;margin-top: -10px;margin-right: 10px;"
                                                        src="<?= $img ?>">
                                                </div>
                                                <div class="pull-left">
                                                    <?= config_item('company_name') ?>
                                                    <p style="color:#999"><?= $this->config->item('company_address') ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                    <div style="padding:35px 0 50px;text-align:center">
                                        <span
                                            style="text-transform: uppercase; border-bottom:1px solid #eee;font-size:13pt;"><?= lang('payments_sent') ?></span>
                                    </div>
                                    <?php
                                    $user_id = $this->session->userdata('user_id');
                                    $client_id = $this->session->userdata('client_id');
                                    $client_outstanding = $this->invoice_model->client_outstanding($user_id);

                                    $client_payments = $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('paid_by' => $client_id));

                                    $client_payable = $client_payments + $client_outstanding;

                                    $client_currency = $this->invoice_model->client_currency_symbol($client_id);
                                    if (!empty($client_currency)) {
                                        $cur = $client_currency->symbol;
                                    } else {
                                        $currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
                                        $cur = $currency->symbol;
                                    }
                                    ?>
                                    <div style="width: 70%;float: left;">
                                        <div style="width: 100%;padding: 11px 0;">
                                            <div
                                                style="color:#999;width:35%;float:left;"><?= lang('invoice_amount') ?></div>
                                            <div
                                                style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;"><?= display_money($client_payable, $cur); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <div style="width: 100%;padding: 10px 0;">
                                            <div
                                                style="color:#999;width:35%;float:left;"><?= lang('payments_sent') ?></div>
                                            <div
                                                style="width:65%;border-bottom:1px solid #eee;float:right;foat:right;min-height:22px"><?= display_money($client_payments, $cur); ?></div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                    <div style="text-align:center;color:white;float:right;background:#FC8174;width: 25%;
                                         padding: 20px 5px;">
                                        <span> <?= lang('amount_received') ?></span><br>
                                        <span
                                            style="font-size:16pt;"><?= display_money($client_payments, $cur); ?></span>
                                    </div>
                                    <div style="clear:both;"></div>

                                    <div style="padding-top:10px">
                                        <div style="width:75%;border-bottom:1px solid #eee;float:right"><strong>

                                                <?php
                                                $payments_info = $this->db->where('paid_by', $client_id)->get('tbl_payments');

                                                if ($payments_info->num_rows() > 0) {
                                                    $last_row = $payments_info->last_row('array');
                                                    ?>
                                                    <a href="<?= base_url() ?>client/invoice/manage_invoice/invoice_details/<?= $last_row['invoices_id'] ?>">

                                                        <?php
                                                        $reference_no = $this->invoice_model->get_any_field('tbl_invoices', array('invoices_id' => $last_row['invoices_id']), 'reference_no');
                                                        echo $reference_no;
                                                        ?>
                                                    </a>
                                                    <?php
                                                } else {
                                                    echo 'NULL';
                                                }
                                                ?></strong></div>
                                        <div style="color:#999;width:25%"><?= lang('recent_invoice') ?></div>
                                    </div>
                                    <div style="padding-top:25px">
                                        <div
                                            style="width:75%;border-bottom:1px solid #eee;float:right"><?= display_money($client_outstanding, $cur); ?></div>
                                        <div style="color:#999;width:25%"><?= lang('outstanding') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <!-- End Payment -->
            </section>
        </div>
    </section>
</div>
<!-- end -->