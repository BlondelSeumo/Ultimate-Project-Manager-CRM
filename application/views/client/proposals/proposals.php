<?= message_box('success'); ?>
<?= message_box('error'); ?>
<?php
$expired = 0;
$total_sent = 0;
$total_declined = 0;
$total_accepted = 0;
$total_expired = 0;
$sent = 0;
$declined = 0;
$accepted = 0;
$pending = 0;
$cancelled = 0;
$all_proposals = get_result('tbl_proposals', array('status !=' => 'draft', 'module' => 'client', 'module_id' => $this->session->userdata('client_id')));

if (!empty($all_proposals)) {
    $all_proposals = array_reverse($all_proposals);
    foreach ($all_proposals as $v_invoice) {
        if (strtotime($v_invoice->due_date) < strtotime(date('Y-m-d')) && $v_invoice->status == ('pending')) {
            $total_expired += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
            $expired += count($v_invoice->proposals_id);;
        }
        if ($v_invoice->status == ('sent')) {
            $sent += count($v_invoice->proposals_id);
            $total_sent += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
        }
        if ($v_invoice->status == ('declined')) {
            $declined += count($v_invoice->proposals_id);
            $total_declined += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
        }
        if ($v_invoice->status == ('accepted')) {
            $accepted += count($v_invoice->proposals_id);
            $total_accepted += $this->proposal_model->proposal_calculation('total', $v_invoice->proposals_id);
        }
        if ($v_invoice->status == ('pending')) {
            $pending += count($v_invoice->proposals_id);
        }
        if ($v_invoice->status == ('cancelled')) {
            $cancelled += count($v_invoice->proposals_id);
        }
    }
}
?>
<?php if (!empty($all_proposals)) { ?>
    <div class="row">
        <div class="col-lg-3 pr-lg">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a class="text-primary filter_by" style="font-size: 15px"
                               search-type="<?= lang('sent') ?>" id="sent"
                               href="#"><?= lang('sent') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $sent ?>
                            / <?= count($all_proposals) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-aqua " data-toggle="tooltip"
                             data-original-title="<?= ($sent / count($all_proposals)) * 100 ?>%"
                             style="width: <?= ($sent / count($all_proposals)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a class="text-danger filter_by" style="font-size: 15px"
                               search-type="<?= lang('expired') ?>" id="expired"
                               href="#"><?= lang('expired') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $expired ?>
                            / <?= count($all_proposals) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= ($expired / count($all_proposals)) * 100 ?>%"
                             style="width: <?= ($expired / count($all_proposals)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a class="text-warning filter_by" style="font-size: 15px"
                               search-type="<?= lang('declined') ?>" id="declined"
                               href="#"><?= lang('declined') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $declined ?>
                            / <?= count($all_proposals) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-primary " data-toggle="tooltip"
                             data-original-title="<?= ($declined / count($all_proposals)) * 100 ?>%"
                             style="width: <?= ($declined / count($all_proposals)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
        <div class="col-lg-3">
            <!-- START widget-->
            <div class="panel widget">
                <div class="pl-sm pr-sm pb-sm">
                    <strong><a class="text-success filter_by" style="font-size: 15px"
                               search-type="<?= lang('accepted') ?>" id="accepted"
                               href="#"><?= lang('accepted') ?></a>
                        <small class="pull-right " style="padding-top: 2px"> <?= $accepted ?>
                            / <?= count($all_proposals) ?></small>
                    </strong>
                    <div class="progress progress-striped progress-xs mb-sm">
                        <div class="progress-bar progress-bar-warning " data-toggle="tooltip"
                             data-original-title="<?= ($accepted / count($all_proposals)) * 100 ?>%"
                             style="width: <?= ($accepted / count($all_proposals)) * 100 ?>%"></div>
                    </div>
                </div>
            </div>
            <!-- END widget-->
        </div>
    </div>
<?php } ?>
<div class="panel panel-custom">
    <header class="panel-heading ">
        <div class="panel-title"><strong><?= lang('all_proposals') ?></strong>
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
                        id="last_month">
                        <a href="#"><?= lang('last_month') ?></a>
                    </li>
                    <li class="filter_by"
                        id="this_months">
                        <a href="#"><?= lang('this_months') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0"
               width="100%">
            <thead>
            <tr>
                <th><?= lang('proposal') ?> #</th>
                <th><?= lang('proposal_date') ?></th>
                <th><?= lang('expire_date') ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('status') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                $(document).ready(function () {
                    list = base_url + "client/proposals/proposalsList";
                    $('.filtered > .dropdown-toggle').on('click', function () {
                        if ($('.group').css('display') == 'block') {
                            $('.group').css('display', 'none');
                        } else {
                            $('.group').css('display', 'block')
                        }
                    });
                    $('.filter_by').on('click', function () {
                        $('.filter_by').removeClass('active');
                        $(this).addClass('active');
                        var filter_by = $(this).attr('id');
                        if (filter_by) {
                            filter_by = filter_by;
                        } else {
                            filter_by = '';
                        }
                        table_url(base_url + "client/proposals/proposalsList/" + filter_by);
                    });
                });
            </script>
            </tbody>
        </table>
    </div>
</div>

