<?php
$chart_year = ($this->session->userdata('chart_year')) ? $this->session->userdata('chart_year') : date('Y');
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
$this->lang->load('calendar', config_item('language'));
?>

<section class="panel panel-custom">
    <header class="panel-heading">
        <div class="panel-title">
            <?php echo !empty($filterBy) ? ' ' . lang('sales_report') . ' -> ' . lang($filterBy) : lang('sales_report') ?>
            <div class="pull-right">
                <div class="btn-group ">
                    <button class="btn custom-bg btn-xs dropdown-toggle"
                            data-toggle="dropdown"
                            class="btn btn-<?= config_item('theme_color'); ?> btn-sm"><?= lang('report') ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/invoices"><?= lang('invoices_report') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/invoice_by_client"><?= lang('invoice_by_client') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/payments"><?= lang('payments_report') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/estimates"><?= lang('estimates_report') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/estimate_by_client"><?= lang('estimate_by_client') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/proposals"><?= lang('proposals_report') ?></a>
                        </li>
                        <li>
                            <a href="<?= base_url() ?>admin/report/sales_report/proposal_by_client"><?= lang('proposal_by_client') ?></a>
                        </li>

                    </ul>
                </div>
                <?php
                if (!empty($filterBy)) {
                    if (empty($status)) {
                        $status = 'all';
                    }
                    if (empty($range)) {
                        $range = array('0', '0');
                    }
                    ?>
                    <a data-toggle="tooltip" data-placement="top"
                       href="<?= base_url() ?>admin/report/sales_report_pdf/<?= $filterBy . '/' . $status . '/' . implode('/', $range) ?>"
                       title="<?= lang('pdf') ?>"
                       class="btn btn-xs btn-danger hidden-xs"><?= lang('pdf') ?>
                        <i class="fa fa-file-pdf-o"></i></a>
                <?php }
                if (!empty($filterBy)) {
                    $id = $filterBy;
                } else {
                    $id = 'sales_report';
                }
                ?>

                <a onclick="print_sales_report('<?= $id ?>')" href="#" data-toggle="tooltip" data-placement="top"
                   title=""
                   data-original-title="Print" class="mr-sm btn btn-xs btn-warning hidden-xs"><?= lang('print') ?>
                    <i class="fa fa-print"></i>
                </a>
            </div>

        </div>
    </header>
    <div id="<?= $id ?>">
        <div class="show_print">
            <div style="width: 100%; border-bottom: 2px solid black;">
                <table style="width: 100%; vertical-align: middle;">
                    <tr>
                        <td style="width: 50px; border: 0px;">
                            <img style="width: 50px;height: 50px;margin-bottom: 5px;"
                                 src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
                        </td>

                        <td style="border: 0px;">
                            <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
                        </td>

                    </tr>
                </table>
            </div>
            <br/>
        </div>
        <div class="panel-body table-responsive">
            <?php
            if (!empty($filterBy)) {
                if ($filterBy == 'invoices' || $filterBy == 'invoice_by_client') {
                    $this->load->view('admin/report/invoice_reports');
                } elseif ($filterBy == 'payments') {
                    $this->load->view('admin/report/payment_reports');
                } elseif ($filterBy == 'estimates' || $filterBy == 'estimate_by_client') {
                    $this->load->view('admin/report/estimates_reports');
                } else if ($filterBy == 'proposals' || $filterBy == 'proposal_by_client') {
                    $this->load->view('admin/report/proposals_reports');
                }
            } else {
                ?>
                <div class="row">
                    <div class="col-sm-3">
                        <section class="panel panel-info">
                            <div class="panel-body">
                                <div class="clear">
                            <span class="text-dark"><?= lang('total_sales') ?></a>
                                <small class="block text-danger pull-right ">
                                    <?= display_money($this->invoice_model->total_sales(), $cur->symbol) ?>

                                </small>
                                </div>
                            </div>
                        </section>
                        <section class="panel panel-info">
                            <div class="panel-body">
                                <div class="clear">
                            <span class="text-dark"><?= lang('paid_this_year') ?></a>
                                <small class="block text-danger pull-right">
                                    <strong>
                                        <?= display_money($this->invoice_model->paid_by_date(date('Y')), $cur->symbol) ?>
                                    </strong>
                                </small>
                                </div>
                            </div>
                        </section>
                        <section class="panel panel-info">
                            <div class="panel-body">
                                <div class="clear">
                            <span class="text-dark"><?= lang('paid_this_month') ?></a>
                                <small class="block text-danger pull-right ">
                                    <strong>
                                        <?= display_money($this->invoice_model->paid_by_date(date('Y'), date('m')), $cur->symbol) ?>
                                    </strong>
                                </small>
                                </div>
                            </div>
                        </section>
                        <section class="panel panel-info">
                            <div class="panel-body">
                                <div class="clear">
                            <span class="text-dark"><?= lang('paid') . ' ' . lang('last_month') ?></a>
                                <small class="block text-danger pull-right ">
                                    <?php
                                    $prevmonth = date('Y-m', strtotime("last month"));
                                    $lyear = date('Y', strtotime($prevmonth));
                                    $lmonth = date('m', strtotime($prevmonth));
                                    echo display_money($this->invoice_model->paid_by_date($lyear, $lmonth), $cur->symbol) ?>
                                </small>
                                </div>
                            </div>
                        </section>

                        <section class="panel panel-info">
                            <div class="panel-body">
                                <div class="clear">
                            <span class="text-dark"><?= lang('total') . ' ' . lang('payments') ?></a>
                                <small
                                    class="block text-danger pull-right"><?= count($this->db->get('tbl_payments')->result()) ?></small>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="col-md-9 b-top">
                        <!-- 1st Quarter -->
                        <div class="col-sm-6 col-xs-12">
                            <div class="widget">
                                <header class="widget-header">
                                    <h4 class="widget-title">1st <?= lang('six_of') ?>, <?= $chart_year ?></h4>
                                </header><!-- .widget-header -->
                                <hr class="widget-separator">
                                <div class="widget-body p-t-lg">
                                    <?php
                                    $total_jan = $this->invoice_model->paid_by_date($chart_year, '01');
                                    $total_feb = $this->invoice_model->paid_by_date($chart_year, '02');
                                    $total_mar = $this->invoice_model->paid_by_date($chart_year, '03');

                                    $total_apr = $this->invoice_model->paid_by_date($chart_year, '04');
                                    $total_may = $this->invoice_model->paid_by_date($chart_year, '05');
                                    $total_jun = $this->invoice_model->paid_by_date($chart_year, '06');
                                    $sum = array($total_jan, $total_feb, $total_mar, $total_apr, $total_may, $total_jun);
                                    ?>
                                    <div class="clearfix mb small text-muted"><?= lang('cal_january') ?>
                                        <div class="pull-right ">
                                            <?= display_money($total_jan, $cur->symbol); ?></div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_february') ?>
                                        <div class="pull-right ">
                                            <?= display_money($total_feb, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_march') ?>
                                        <div class="pull-right ">
                                            <?= display_money($total_mar, $cur->symbol); ?>
                                        </div>
                                    </div>
                                    <div class="clearfix mb small text-muted"><?= lang('cal_april') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_apr, $cur->symbol); ?></div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_may') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_may, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_june') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_jun, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                        <div class="pull-right"><strong>
                                                <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                        </div>
                                    </div>

                                </div><!-- .widget-body -->
                            </div><!-- .widget -->
                        </div>

                        <!-- 3rd Quarter -->

                        <div class="col-sm-6 col-xs-12">
                            <div class="widget">
                                <header class="widget-header">
                                    <h4 class="widget-title">2nd <?= lang('six_of') ?>, <?= $chart_year ?></h4>
                                </header><!-- .widget-header -->
                                <hr class="widget-separator">
                                <div class="widget-body p-t-lg">
                                    <?php
                                    $total_jul = $this->invoice_model->paid_by_date($chart_year, '07');
                                    $total_aug = $this->invoice_model->paid_by_date($chart_year, '08');
                                    $total_sep = $this->invoice_model->paid_by_date($chart_year, '09');
                                    $total_oct = $this->invoice_model->paid_by_date($chart_year, '10');
                                    $total_nov = $this->invoice_model->paid_by_date($chart_year, '11');
                                    $total_dec = $this->invoice_model->paid_by_date($chart_year, '12');
                                    $sum = array($total_jul, $total_aug, $total_sep, $total_oct, $total_nov, $total_dec);
                                    ?>
                                    <div class="clearfix mb small text-muted"><?= lang('cal_july') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_jul, $cur->symbol); ?></div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_august') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_aug, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_september') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_sep, $cur->symbol); ?>
                                        </div>
                                    </div>
                                    <div class="clearfix mb small text-muted"><?= lang('cal_october') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_oct, $cur->symbol); ?></div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_november') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_nov, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small text-muted"><?= lang('cal_december') ?>
                                        <div class="pull-right">
                                            <?= display_money($total_dec, $cur->symbol); ?>
                                        </div>
                                    </div>

                                    <div class="clearfix mb small bt pt-sm text-bold text-danger"><?= lang('total') ?>
                                        <div class="pull-right"><strong>
                                                <?= display_money(array_sum($sum), $cur->symbol); ?></strong>
                                        </div>
                                    </div>

                                </div><!-- .widget-body -->
                            </div><!-- .widget -->
                        </div>
                        <!-- End Quarters -->
                    </div>

                </div>
                <!-- End Row -->
            <?php } ?>
        </div>

</section>

<script type="text/javascript">
    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>