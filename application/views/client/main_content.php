<link href="<?php echo base_url() ?>assets/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet" type="text/css">
<style type="text/css">
    .datepicker {
        z-index: 1151 !important;
    }

    .easypiechart {
        margin: 0px auto;
    }
</style>
<?php echo message_box('success'); ?>
<?php
$user_id = $this->session->userdata('user_id');

$client_id = $this->session->userdata('client_id');

$client_outstanding = $this->invoice_model->client_outstanding($client_id);

$client_payments = $this->invoice_model->get_sum('tbl_payments', 'amount', $array = array('paid_by' => $client_id));

$client_payable = $client_payments + $client_outstanding;

$client_currency = $this->invoice_model->client_currency_symbol($client_id);
if (!empty($client_currency)) {
    $cur = $client_currency->symbol;
} else {
    $currency = $this->db->where(array('code' => config_item('default_currency')))->get('tbl_currencies')->row();
    $cur = $currency->symbol;
}
if ($client_payable > 0 AND $client_payments > 0) {
    $perc_paid = round(($client_payments / $client_payable) * 100, 1);
    if ($perc_paid > 100) {
        $perc_paid = '100';
    }
} else {
    $perc_paid = 0;
}
$all_report = $this->db->where('report', 2)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();
$all_order_data = $this->db->where('report', 3)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();;
?>
<?php if ($client_outstanding > 0) { ?>

    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-warning"></i>
        <?= lang('your_balance_due') ?>
        <?= display_money($client_outstanding, $cur) ?>
        </strong>
    </div>
<?php } ?>
<div class="row">
    <?php if (!empty($all_report)) {
        foreach ($all_report as $v_report) {
            if ($v_report->name == 'paid_amount' && $v_report->status == 1) { ?>
                <div class="<?= $v_report->col ?>">
                    <div class="panel widget mb0 b0">
                        <div class="row-table row-flush">
                            <div class="col-xs-4 bg-info text-center">
                                <em class="fa fa-money fa-2x"></em>
                            </div>
                            <div class="col-xs-8">
                                <div class="text-center">
                                    <h4 class="mb-sm"><?php
                                        if (!empty($client_payments)) {
                                            echo display_money($client_payments, $cur);
                                        } else {
                                            echo '0.00';
                                        }
                                        ?></h4>
                                    <p class="mb0 text-muted"><?= lang('paid_amount') ?></p>
                                    <a href="<?= base_url() ?>client/invoice/all_payments"
                                       class="small-box-footer"><?= lang('more_info') ?> <i
                                            class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($v_report->name == 'due_amount' && $v_report->status == 1) {
                ?>
                <div class="<?= $v_report->col ?>">
                    <div class="panel widget mb0 b0">
                        <div class="row-table row-flush">
                            <div class="col-xs-4 bg-danger text-center">
                                <em class="fa fa-usd fa-2x"></em>
                            </div>
                            <div class="col-xs-8">
                                <div class="text-center">
                                    <h4 class="mb-sm"><?php
                                        if ($client_outstanding > 0) {
                                            echo display_money($client_outstanding, $cur);
                                        } else {
                                            echo '0.00';
                                        }
                                        ?></h4>
                                    <p class="mb0 text-muted"><?= lang('due_amount') ?></p>
                                    <a href="<?= base_url() ?>client/invoice/manage_invoice"
                                       class="small-box-footer"><?= lang('more_info') ?>
                                        <i
                                            class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($v_report->name == 'invoice_amount' && $v_report->status == 1) {
                ?>
                <div class="<?= $v_report->col ?>">
                    <div class="panel widget mb0 b0">
                        <div class="row-table row-flush">
                            <div class="col-xs-4 bg-inverse text-center">
                                <em class="fa fa-usd fa-2x"></em>
                            </div>
                            <div class="col-xs-8">
                                <div class="text-center">
                                    <h4 class="mb-sm">
                                        <?php
                                        if ($client_payable > 0) {
                                            echo display_money($client_payments, $cur);
                                        } else {
                                            echo '0.00';
                                        }
                                        ?></h4>
                                    <p class="mb0 text-muted"><?= lang('invoice_amount') ?></p>
                                    <a href="<?= base_url() ?>client/invoice/manage_invoice"
                                       class="small-box-footer"><?= lang('more_info') ?>
                                        <i
                                            class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($v_report->name == 'paid_percentage' && $v_report->status == 1) {
                ?>
                <div class="<?= $v_report->col ?>">
                    <div class="panel widget mb0 b0">
                        <div class="row-table row-flush">
                            <div class="col-xs-4 bg-purple text-center">
                                <em class="fa fa-usd fa-2x"></em>
                            </div>
                            <div class="col-xs-8">
                                <div class="text-center">
                                    <h4 class="mb-sm">
                                        <?= $perc_paid ?>%</h4>
                                    <p class="mb0 text-muted"><?= lang('paid') . ' ' . lang('percentage') ?></p>
                                    <a href="<?= base_url() ?>client/invoice/all_payments"
                                       class="small-box-footer"><?= lang('more_info') ?>
                                        <i
                                            class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
        }
    }
    ?>
</div>
<div class="row mt-lg">
    <?php if (!empty($all_order_data)) {
        foreach ($all_order_data as $v_order) {
            ?>
            <?php if ($v_order->name == 'recently_paid_invoices' && $v_order->status == 1) { ?>
                <div class="<?= $v_order->col ?>">
                    <section class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <header class="panel-heading">
                            <h3 class="panel-title"><?= lang('recently_paid_invoices') ?></h3>
                        </header>
                        <div class="panel-body inv-slim-scroll">
                            <div class="list-group bg-white">
                                <?php
                                $recently_paid = $this->db
                                    ->order_by('created_date', 'desc')
                                    ->get('tbl_payments', 5)
                                    ->result();
                                $total_amount = 0;
                                if (!empty($recently_paid)) {
                                    foreach ($recently_paid as $key => $v_paid) {
                                        $invoices_info = $this->db->where(array('invoices_id' => $v_paid->invoices_id))->get('tbl_invoices')->row();
                                        if ($invoices_info->client_id == $client_id) {
                                            if (!empty($v_paid->payment_method)) {
                                                $payment_method = $this->db->where(array('payment_methods_id' => $v_paid->payment_method))->get('tbl_payment_methods')->row();
                                            }

                                            if ($v_paid->payment_method == '1') {
                                                $label = 'success';
                                            } elseif ($v_paid->payment_method == '2') {
                                                $label = 'danger';
                                            } else {
                                                $label = 'dark';
                                            }
                                            $total_amount += $v_paid->amount;
                                            ?>
                                            <a href="<?= base_url() ?>client/invoice/manage_invoice/invoice_details/<?= $v_paid->invoices_id ?>"
                                               class="list-group-item">
                                                <?= $invoices_info->reference_no ?> -
                                                <small
                                                    class="text-muted"><?= display_money($v_paid->amount, $cur); ?>
                                                    <span
                                                        class="label label-<?= $label ?> pull-right"><?= !empty($payment_method->method_name) ? $payment_method->method_name : '-'; ?></span>
                                                </small>
                                            </a>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <small><?= lang('total_receipts') ?>: <strong>
                                    <?php
                                    echo display_money($total_amount, $cur);
                                    ?>
                                </strong></small>
                        </div>
                    </section>

                </div>
            <?php }
            if ($v_order->name == 'payments' && $v_order->status == 1) {
                ?>
                <div class="<?= $v_order->col ?>">
                    <section class="panel panel-custom mb-lg" style="height: 418px;">
                        <header class="panel-heading">
                            <h3 class="panel-title"><?= lang('payments') ?></h3>
                        </header>
                        <div class="panel-body text-center">
                            <h4>
                                <small> <?= lang('paid_amount') ?> :</small>
                                <?= display_money($this->invoice_model->get_sum('tbl_payments', 'amount', array('paid_by' => $client_id)), $cur); ?>
                            </h4>
                            <small class="text-muted text-center block">
                                <?= lang('outstanding') ?>
                                : <?= display_money($client_outstanding, $cur); ?>
                            </small>
                            <div class="text-center block mt">
                                <div style="display: inline-block">
                                    <div id="easypie3" data-percent="<?= $perc_paid ?>" class="easypie-chart">
                                        <span class="h2"><?= $perc_paid ?>%</span>
                                        <div class="easypie-text"><?= lang('paid') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <small><?= lang('invoice_amount') ?>:
                                <strong><?= display_money($client_payable, $cur); ?></strong>
                            </small>
                        </div>
                    </section>

                </div>
            <?php }
            if ($v_order->name == 'recent_invoice' && $v_order->status == 1) {
                ?>
                <div class="<?= $v_order->col ?>">

                    <section class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?= lang('recent') . ' ' . lang('invoices') ?>
                            </div>
                        </div>
                        <div class="">
                            <table class="table table-striped" id="">
                                <thead>
                                <tr>
                                    <th><?= lang('reference_no') ?></th>
                                    <th><?= lang('date_issued') ?></th>
                                    <th><?= lang('due_date') ?> </th>
                                    <th class="col-currency"><?= lang('amount') ?> </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $client_invoices = $this->db->where('client_id', $client_id)->limit(5)->get('tbl_invoices')->result();
                                $total_invoice = 0;
                                if (!empty($client_invoices)) {
                                    foreach ($client_invoices as $key => $invoice) {
                                        $total_invoice += $this->invoice_model->invoice_payable($invoice->invoices_id);
                                        ?>
                                        <tr>
                                            <td><a class="text-info"
                                                   href="<?= base_url() ?>client/invoice/manage_invoice/invoice_details/<?= $invoice->invoices_id ?>"><?= $invoice->reference_no ?></a>
                                            </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($invoice->date_saved)); ?> </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($invoice->due_date)); ?> </td>
                                            <td>
                                                <?= display_money($this->invoice_model->invoice_payable($invoice->invoices_id), $cur); ?>

                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <strong><?= lang('invoice') . ' ' . lang('amount') ?>:</strong> <strong
                                class="label label-success">
                                <?= display_money($total_invoice, $cur); ?>
                            </strong>
                        </div>
                    </section>
                </div>
            <?php }
            if ($v_order->name == 'recent_projects' && $v_order->status == 1) {
                ?>
                <div class="<?= $v_order->col ?>">
                    <div class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?= lang('recent') . ' ' . lang('project') ?>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped " id="">
                                    <thead>
                                    <tr>
                                        <th><?= lang('project_name') ?></th>
                                        <th><?= lang('end_date') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <th class="col-options no-sort"><?= lang('action') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $all_project = $this->db->where('client_id', $client_id)->limit(5)->get('tbl_project')->result();
                                    if (!empty($all_project)):foreach ($all_project as $v_project):
                                        $progress = $this->items_model->get_project_progress($v_project->project_id);
                                        ?>
                                        <tr>
                                            <td><a class="text-info"
                                                   href="<?= base_url() ?>client/projects/project_details/<?= $v_project->project_id ?>"><?= $v_project->project_name ?></a>
                                                <?php if (strtotime(date('Y-m-d')) > strtotime($v_project->end_date) && $progress < 100) { ?>
                                                    <span
                                                        class="label label-danger pull-right"><?= lang('overdue') ?></span>
                                                <?php } ?>

                                                <div class="progress progress-xs progress-striped active">
                                                    <div
                                                        class="progress-bar progress-bar-<?php echo ($progress >= 100) ? 'success' : 'primary'; ?>"
                                                        data-toggle="tooltip"
                                                        data-original-title="<?= $progress ?>%"
                                                        style="width: <?= $progress; ?>%"></div>
                                                </div>

                                            </td>
                                            <td><?= strftime(config_item('date_format'), strtotime($v_project->end_date)) ?></td>

                                            <td><?php
                                                if (!empty($v_project->project_status)) {
                                                    if ($v_project->project_status == 'completed') {
                                                        $status = "<span class='label label-success'>" . lang($v_project->project_status) . "</span>";
                                                    } elseif ($v_project->project_status == 'in_progress') {
                                                        $status = "<span class='label label-primary'>" . lang($v_project->project_status) . "</span>";
                                                    } elseif ($v_project->project_status == 'cancel') {
                                                        $status = "<span class='label label-danger'>" . lang($v_project->project_status) . "</span>";
                                                    } else {
                                                        $status = "<span class='label label-warning'>" . lang($v_project->project_status) . "</span>";
                                                    }
                                                    echo $status;
                                                }
                                                ?>      </td>
                                            <td>
                                                <?= btn_view('client/projects/project_details/' . $v_project->project_id) ?>
                                            </td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    endif;
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            <?php }
            ?>

            <?php if ($v_order->name == 'recent_emails' && $v_order->status == 1) { ?>
                <div class="<?= $v_order->col ?> ">
                    <div class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('recent_mail') ?>
                                <span class="pull-right text-white">
                            <a href="<?php echo base_url() ?>client/mailbox" class=" view-all-front">View All</a></span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <form method="post" action="<?php echo base_url() ?>client/mailbox/delete_mail/inbox">
                                <!-- Main content -->
                                <section class="content">
                                    <div class="box box-primary">
                                        <div class="box-body no-padding">
                                            <div class="mailbox-controls">

                                                <!-- Check all button -->
                                                <div class="mail_checkbox">
                                                    <input type="checkbox" id="parent_present">
                                                </div>
                                                <div class="btn-group">
                                                    <button class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i>
                                                    </button>
                                                </div><!-- /.btn-group -->
                                                <a href="#" onClick="history.go(0)" class="btn btn-default btn-sm"><i
                                                        class="fa fa-refresh"></i></a>
                                                <a href="<?php echo base_url() ?>client/mailbox/index/compose"
                                                   class="btn btn-danger">Compose +</a>
                                            </div>
                                            <br/>
                                            <div class="table-responsive mailbox-messages slim-scroll">
                                                <table class="table table-hover table-striped">
                                                    <tbody>

                                                    <?php
                                                    $get_inbox_message = $this->db
                                                        ->where(array('deleted' => 'no', 'to' => $this->session->userdata('email')))
                                                        ->order_by('message_time', 'desc')
                                                        ->get('tbl_inbox', 10)
                                                        ->result();
                                                    if (!empty($get_inbox_message)):foreach ($get_inbox_message as $v_inbox_msg):
                                                        ?>
                                                        <tr>
                                                            <td><input class="child_present" type="checkbox"
                                                                       name="selected_id[]"
                                                                       value="<?php echo $v_inbox_msg->inbox_id; ?>"/>
                                                            </td>

                                                            <td class="mailbox-star">
                                                                <?php if ($v_inbox_msg->favourites == 1) { ?>
                                                                    <a href="<?php echo base_url() ?>client/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/0"><i
                                                                            class="fa fa-star text-yellow"></i></a>
                                                                <?php } else { ?>
                                                                    <a href="<?php echo base_url() ?>client/mailbox/index/added_favourites/<?php echo $v_inbox_msg->inbox_id ?>/1"><i
                                                                            class="fa fa-star-o text-yellow"></i></a>
                                                                <?php } ?>
                                                            </td>
                                                            <td class="mailbox-name"><a
                                                                    href="<?php echo base_url() ?>client/mailbox/index/read_inbox_mail/<?php echo $v_inbox_msg->inbox_id ?>"><?php
                                                                    $string = (strlen($v_inbox_msg->to) > 13) ? strip_html_tags(mb_substr($v_inbox_msg->to, 0, 13)) . '...' : $v_inbox_msg->to;
                                                                    if ($v_inbox_msg->view_status == 1) {
                                                                        echo '<span style="color:#000">' . $string . '</span>';
                                                                    } else {
                                                                        echo '<b style="color:#000;font-size:13px;">' . $string . '</b>';
                                                                    }
                                                                    ?></a></td>
                                                            <td class="mailbox-subject" style="font-size:13px"><b
                                                                    class="pull-left"><?php
                                                                    $subject = (strlen($v_inbox_msg->subject) > 20) ? strip_html_tags(mb_substr($v_inbox_msg->subject, 0, 20)) . '...' : $v_inbox_msg->subject;
                                                                    echo $subject;
                                                                    ?> - &nbsp;</b> <span class="pull-left "> <?php
                                                                    $body = (strlen($v_inbox_msg->message_body) > 40) ? strip_html_tags(mb_substr($v_inbox_msg->message_body, 0, 40)) . '...' : $v_inbox_msg->message_body;
                                                                    echo $body;
                                                                    ?></span></td>
                                                            <td style="font-size:13px">
                                                                <?= time_ago($v_inbox_msg->message_time); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td><strong>There is no email to display</strong></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    </tbody>
                                                </table><!-- /.table -->
                                            </div><!-- /.mail-box-messages -->
                                        </div><!-- /.box-body -->
                                    </div><!-- /. box -->
                                </section><!-- /.content -->
                            </form>
                        </div>
                    </div>
                </div>
            <?php }
            if ($v_order->name == 'recent_activities' && $v_order->status == 1) {
                ?>
                <div class="<?= $v_order->col ?> ">
                    <div class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('recent_activities') ?></h3>
                        </div>
                        <div class="panel-body">
                            <section class="comment-list block">
                                <section>
                                    <?php
                                    $activities = $this->db
                                        ->where('user', $user_id)
                                        ->order_by('activity_date', 'desc')
                                        ->get('tbl_activities', 50)
                                        ->result();
                                    if (!empty($activities)) {
                                        foreach ($activities as $v_activities) {
                                            $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                            ?>
                                            <article id="comment-id-1" class="comment-item" style="font-size: 11px;">
                                                <div class="pull-left recect_task  ">
                                                    <a class="pull-left recect_task  ">
                                                        <?php if (!empty($profile_info)) {
                                                            ?>
                                                            <img style="width: 30px;margin-left: 18px;
                                                             height: 29px;
                                                             border: 1px solid #aaa;"
                                                                 src="<?= base_url() . $profile_info->avatar ?>"
                                                                 class="img-circle">
                                                        <?php } ?>
                                                    </a>
                                                </div>
                                                <section class="comment-body m-b-lg">
                                                    <header class=" ">
                                                        <strong>
                                                            <?= $profile_info->fullname ?></strong>
                                                    <span data-toggle="tooltip" data-placement="top"
                                                          title="<?= display_datetime($v_activities->activity_date) ?>"
                                                          class="text-muted text-xs"> <?php
                                                        echo time_ago($v_activities->activity_date);
                                                        ?>
                                                    </span>
                                                    </header>
                                                    <div>
                                                        <?php echo sprintf(lang($v_activities->activity) . ' <strong style="color:#000"> <em>' . $v_activities->value1 . '</em>' . '<em>' . $v_activities->value2 . '</em></strong>'); ?>
                                                    </div>
                                                    <hr/>
                                                </section>
                                            </article>


                                            <?php
                                        }
                                    }
                                    ?>
                                </section>
                            </section>
                        </div>
                    </div>
                </div>
            <?php }
            if ($v_order->name == 'announcements' && $v_order->status == 1) {
                ?>
                <div class="<?= $v_order->col ?>">
                    <div class="panel panel-custom mb-lg" style="height: 418px;overflow-y: scroll">
                        <header class="panel-heading mb0">
                            <h3 class="panel-title"><?= lang('announcements') ?></h3>
                        </header>
                        <?php $all_announcements = get_order_by('tbl_announcements', array('all_client' => 1), 'announcements_id', null, '10');
                        if (!empty($all_announcements)):foreach ($all_announcements as $v_announcements):?>
                            <div class="notice-calendar-list panel-body">
                                <div class="notice-calendar">
                                    <span
                                        class="month"><?php echo date('M', strtotime($v_announcements->created_date)) ?></span>
                                    <span
                                        class="date"><?php echo date('d', strtotime($v_announcements->created_date)) ?></span>
                                </div>

                                <div class="notice-calendar-heading">
                                    <h5 class="notice-calendar-heading-title">
                                        <a href="<?php echo base_url() ?>client/dashboard/announcements_details/<?php echo $v_announcements->announcements_id; ?>"
                                           title="View" data-toggle="modal"
                                           data-target="#myModal_lg"><?php echo $v_announcements->title ?></a>
                                    </h5>
                                    <div class="notice-calendar-date">
                                        <?php
                                        echo strip_html_tags(mb_substr($v_announcements->description, 0, 200)) . '...';
                                        ?>
                                    </div>
                                </div>
                                <div style="margin-top: 5px; padding-top: 5px; padding-bottom: 10px;">
                                        <span style="font-size: 10px;" class="pull-right">
                                            <strong>
                                                <a href="<?php echo base_url() ?>client/dashboard/announcements_details/<?php echo $v_announcements->announcements_id; ?>"
                                                   title="View" data-toggle="modal"
                                                   data-target="#myModal_lg"><?= lang('view_details') ?></a></strong>
                                        </span>
                                </div>
                            </div>
                            <?php

                        endforeach; ?>
                        <?php endif; ?>
                    </div><!-- ./box-body -->
                </div>
            <?php }
            ?>
        <?php }
    } ?>
</div>
