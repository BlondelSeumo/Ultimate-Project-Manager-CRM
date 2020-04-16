<?php
$client_id = $this->session->userdata('client_id');
$client_outstanding = $this->invoice_model->client_outstanding($client_id);
$currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
?>
<div class="row">
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0"><?php
                    if ($client_outstanding > 0) {
                        echo display_money($client_outstanding, $currency->symbol);
                    } else {
                        echo '0.00';
                    }
                    ?></h3>
                <p class="text-warning m0"><?= lang('total') . ' ' . lang('outstanding') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
    </div>
    <!-- END widget-->
    <?php
    $past_overdue = 0;
    $all_paid_amount = 0;
    $not_paid = 0;
    $fully_paid = 0;
    $draft = 0;
    $partially_paid = 0;
    $overdue = 0;
    $all_invoices = $this->db->where(array('client_id' => $client_id, 'status !=' => 'draft'))->get('tbl_invoices')->result();

    if (!empty($all_invoices)) {
        $all_invoices = array_reverse($all_invoices);
        foreach ($all_invoices as $v_invoice) {
            $payment_status = $this->invoice_model->get_payment_status($v_invoice->invoices_id);
            if (strtotime($v_invoice->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                $past_overdue += $this->invoice_model->calculate_to('invoice_due', $v_invoice->invoices_id);
            }
            $all_paid_amount += $this->invoice_model->calculate_to('paid_amount', $v_invoice->invoices_id);

            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('not_paid')) {
                $not_paid += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('fully_paid')) {
                $fully_paid += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('draft')) {
                $draft += count($v_invoice->invoices_id);
            }
            if ($this->invoice_model->get_payment_status($v_invoice->invoices_id) == lang('partially_paid')) {
                $partially_paid += count($v_invoice->invoices_id);
            }
            if (strtotime($v_invoice->due_date) < strtotime(date('Y-m-d')) && $payment_status != lang('fully_paid')) {
                $overdue += count($v_invoice->invoices_id);
            }
        }
    }
    ?>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0 "><?= display_money($all_paid_amount + $client_outstanding, $currency->symbol) ?></h3>
                <p class="text-primary m0"><?= lang('total') . ' ' . lang('invoice_amount') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0"><?= display_money($past_overdue, $currency->symbol) ?></h3>
                <p class="text-danger m0"><?= lang('past') . ' ' . lang('overdue') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
    <div class="col-lg-3">
        <!-- START widget-->
        <div class="panel widget">
            <div class="panel-body pl-sm pr-sm pt-sm pb0 text-center">
                <h3 class="mt0 mb0 "><?= display_money($all_paid_amount, $currency->symbol) ?></h3>
                <p class="text-success m0"><?= lang('paid') . ' ' . lang('invoice') ?></p>
            </div>
        </div>
        <!-- END widget-->
    </div>
</div>
<?php if (!empty($all_invoices)) { ?>
    <div class="row">
        <div class="col-lg-3 pl-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="filter_by" search-type="<?= lang('not_paid') ?>"
                               id="not_paid"
                               href="#"><?= lang('unpaid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $not_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-danger " data-toggle="tooltip"
                             data-original-title="<?= round(($not_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($not_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>

        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="filter_by" search-type="<?= lang('paid') ?>" id="paid"
                               href="#"><?= lang('paid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $fully_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-success " data-toggle="tooltip"
                             data-original-title="<?= round(($fully_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($fully_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="filter_by" search-type="<?= lang('partially_paid') ?>"
                               id="partially_paid"
                               href="#"><?= lang('partially_paid') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $partially_paid ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= round(($partially_paid / count($all_invoices)) * 100) ?>%"
                             style="width: <?= ($partially_paid / count($all_invoices)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a style="font-size: 15px" class="filter_by" search-type="<?= lang('overdue') ?>"
                               id="overdue"
                               href="#"><?= lang('overdue') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $overdue ?>
                            / <?= count($all_invoices) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= round(($overdue / count($all_invoices)) * 100) ?>%"
                             style="width: <?= round(($overdue / count($all_invoices)) * 100) ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php } ?>
<?= message_box('success'); ?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <?= lang('all_invoices') ?>
            <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                 data-title="<?php echo lang('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu group animated zoomIn"
                    style="width:300px;">
                    <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                    <li class="filter_by"
                        id="cancelled">
                        <a href="#"><?= lang('cancelled') ?></a>
                    </li>
                    <li class="filter_by"
                        id="recurring">
                        <a href="#"><?= lang('recurring') ?></a>
                    </li>
                    <li class="filter_by"
                        id="last_month">
                        <a href="#"><?= lang('last_month') ?></a>
                    </li>
                    <li class="filter_by"
                        id="this_months">
                        <a href="#"><?= lang('this_months') ?></a>
                    </li>
                </ul>
            </div>
            <a class="btn btn-success btn-xs pull-right mr"
               href="<?= base_url('client/invoice/refund_itemslist') ?>"><?= lang('request') . ' ' . lang('refund_items') ?></a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('invoice') ?></th>
                <th class="col-date"><?= lang('due_date') ?></th>
                <th class="col-currency"><?= lang('amount') ?></th>
                <th class="col-currency"><?= lang('due_amount') ?></th>
                <th><?= lang('status') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                $(document).ready(function () {
                    list = base_url + "client/invoice/invoiceList";
                    $('.filtered > .dropdown-toggle').on('click', function () {
                        if ($('.group').css('display') == 'block') {
                            $('.group').css('display', 'none');
                        } else {
                            $('.group').css('display', 'block')
                        }
                    });
                    $('.filter_by').on('click', function () {
                        $('.filter_by').removeClass('active');
                        $('#showed_result').html($(this).attr('search-type'));
                        $(this).addClass('active');
                        var filter_by = $(this).attr('id');
                        if (filter_by) {
                            filter_by = filter_by;
                        } else {
                            filter_by = '';
                        }
                        table_url(base_url + "client/invoice/invoiceList/" + filter_by);
                    });
                });
            </script>
            </tbody>
        </table>
    </div>
</div>