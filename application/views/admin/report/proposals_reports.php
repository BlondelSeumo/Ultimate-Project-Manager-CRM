<!-- Include Required Prerequisites -->
<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<?php
$cur = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
if (!empty($range[0])) {
    $start_date = date('F d, Y', strtotime($range[0]));
    $end_date = date('F d, Y', strtotime($range[1]));
}
$status = (isset($status)) ? $status : 'all';
?>


<div class="">
    <div class="hidden-print">
        <div class="criteria-band">
            <address class="row">
                <?php echo form_open(base_url() . 'admin/report/sales_report/' . $filterBy);
                if ($filterBy == 'proposal_by_client') {
                    ?>
                    <div class="col-md-3">
                        <label><?= lang('select_client') ?></label>
                        <select class="form-control" name="status">
                            <?php
                            $all_client = get_result('tbl_client');
                            if (!empty($all_client)) {
                                foreach ($all_client as $v_client) {
                                    ?>
                                    <option value="<?= $v_client->client_id ?>"
                                        <?php
                                        if (!empty($status)) {
                                            echo $status == $v_client->client_id ? 'selected' : null;
                                        }
                                        ?>
                                    ><?= ucfirst($v_client->name) ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                <?php } else { ?>
                    <div class="col-md-2">
                        <label><?= lang('status') ?></label>
                        <select class="form-control" name="status">
                            <option
                                value="all" <?= ($status == 'all') ? 'selected="selected"' : ''; ?>><?= lang('all') ?></option>
                            <option
                                value="draft" <?= ($status == 'draft') ? 'selected="selected"' : ''; ?>><?= lang('draft') ?></option>
                            <option
                                value="open" <?= ($status == 'open') ? 'selected="selected"' : ''; ?>><?= lang('open') ?></option>
                            <option
                                value="cancelled" <?= ($status == 'cancelled') ? 'selected="selected"' : ''; ?>><?= lang('cancelled') ?></option>
                            <option
                                value="expired" <?= ($status == 'expired') ? 'selected="selected"' : ''; ?>><?= lang('expired') ?></option>
                            <option
                                value="declined" <?= ($status == 'declined') ? 'selected="selected"' : ''; ?>><?= lang('declined') ?></option>
                            <option
                                value="accepted" <?= ($status == 'accepted') ? 'selected="selected"' : ''; ?>><?= lang('accepted') ?></option>
                        </select>
                    </div>
                <?php } ?>

                <div class="col-md-4">
                    <label><?= lang('date_range') ?></label>
                    <input type="text" name="range" id="reportrange"
                           class="pull-right form-control">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <b class="caret"></b>
                </div>


                <div class="col-md-2">
                    <label style="visibility: hidden"><?= lang('run_report') ?></label>
                    <button class="btn btn-purple" type="submit">
                        <?= lang('run_report') ?>
                    </button>
                </div>
            </address>
        </div>
        </form>
    </div>


    <div class="rep-container">
        <div class="page-header text-center">
            <h3 class="reports-headerspacing"><?= lang($filterBy) ?></h3>
            <?php if (!empty($start_date)) { ?>
                <h5><span><?= lang('FROM') ?></span>&nbsp;<?= $start_date ?>
                    &nbsp;<span><?= lang('TO') ?></span>&nbsp;<?= $end_date ?></h5>
            <?php } ?>
        </div>

        <div class="fill-container">
            <table class="table zi-table table-hover norow-action small">
                <thead>
                <tr>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('proposal') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('proposal') . ' ' . lang('date') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "><?= lang('expire') . ' ' . lang('date') ?></div>
                    </th>
                    <th class="text-left">
                        <div class="pull-left "> <?= lang('module') ?>#</div>
                        <!---->
        </div>
        </th>
        <th class="text-right">
            <div class=" "> <?= lang('amount') ?></div>
        </th>
        <th class="text-right">
            <div class=" "> <?= lang('tax') . ' ' . lang('amount') ?></div>
        </th>
        <th class="text-right">
            <div class=" "> <?= lang('discount') ?></div>
        </th>
        </tr>
        </thead>

        <tbody>

        <?php
        $discount_total = 0;
        $proposal_total = 0;
        $total_tax = 0;
        if (!empty($all_proposals)) {
            foreach ($all_proposals as $v_proposals) {
                $proposal_total += $this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id);
                $total_tax += $this->proposal_model->get_proposal_tax_amount($v_proposals->proposals_id);
                $discount_total += $this->proposal_model->get_proposal_discount($v_proposals->proposals_id);
                ?>
                <tr>
                    <td>
                        <a class="text-info hidden-print"
                           href="<?= base_url() ?>admin/proposals/index/proposals_details/<?= $v_proposals->proposals_id ?>"><?= $v_proposals->reference_no ?></a>
                        <span class="show_print"><?= $v_proposals->reference_no ?></span>
                    </td>
                    <td><?= display_date($v_proposals->proposal_date); ?></td>
                    <td><?= display_date($v_proposals->due_date); ?></td>
                    <td>
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

                    </td>
                    <td class="text-right">
                        <?= display_money($this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id), $cur->symbol); ?></td>

                    <td class="text-right">
                        <?= display_money($this->proposal_model->get_proposal_tax_amount($v_proposals->proposals_id), $cur->symbol); ?></td>

                    <td class="text-right">
                        <?php echo display_money($this->proposal_model->get_proposal_discount($v_proposals->proposals_id), $cur->symbol); ?></td>
                </tr>
            <?php } ?>

            <tr class="hover-muted bt">
                <td colspan="4"><?= lang('total') ?></td>
                <td class="text-right"><?= display_money($proposal_total, $cur->symbol) ?></td>
                <td class="text-right"><?= display_money($total_tax, $cur->symbol) ?></td>
                <td class="text-right"><?= display_money($discount_total, $cur->symbol) ?></td>
            </tr>
        <?php } ?>

        <!----></tbody>
        </table>
    </div>


</div>


<script type="text/javascript">
    $('#reportrange').daterangepicker({
        autoUpdateInput: <?= !empty($start_date) ? 'true' : 'false'?>,
        locale: {
            format: 'MMMM D, YYYY'
        },
        <?php if(!empty($start_date)){?>
        startDate: '<?=$start_date?>',
        endDate: '<?=$end_date?>',
        <?php }?>
        "opens": "right",
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
    });

    $('#reportrange').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
</script>
