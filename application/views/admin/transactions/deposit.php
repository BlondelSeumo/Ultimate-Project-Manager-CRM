<?= message_box('success'); ?>
<?= message_box('error'); ?>

<?php
$mdate = date('Y-m-d');
$last_7_days = date('Y-m-d', strtotime('today - 7 days'));
$all_goal_tracking = $this->transactions_model->get_permission('tbl_goal_tracking');

$all_goal = 0;
$bank_goal = 0;
$complete_achivement = 0;
if (!empty($all_goal_tracking)) {
    foreach ($all_goal_tracking as $v_goal_track) {
        $goal_achieve = $this->transactions_model->get_progress($v_goal_track, true);
        if ($v_goal_track->goal_type_id == 1) {
            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }
            $all_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }
        if ($v_goal_track->goal_type_id == 2) {
            if ($v_goal_track->end_date <= $mdate) { // check today is last date or not

                if ($v_goal_track->email_send == 'no') {// check mail are send or not
                    if ($v_goal_track->achievement <= $goal_achieve['achievement']) {
                        if ($v_goal_track->notify_goal_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_achieve', $v_goal_track);
                        }
                    } else {
                        if ($v_goal_track->notify_goal_not_achive == 'on') {// check is notify is checked or not check
                            $this->transactions_model->send_goal_mail('goal_not_achieve', $v_goal_track);
                        }
                    }
                }
            }

            $bank_goal += $v_goal_track->achievement;
            $complete_achivement += $goal_achieve['achievement'];
        }

    }
}
// 30 days before

for ($iDay = 7; $iDay >= 0; $iDay--) {
    $date = date('Y-m-d', strtotime('today - ' . $iDay . 'days'));
    $this_7_days_deposit[$date] = $this->db->select_sum('amount')->where(array('type' => 'Income', 'date >=' => $date, 'date <=' => $date))->get('tbl_transactions')->result();
}

$this_7_days_all = $this->db->where(array('goal_type_id' => 1, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$this_7_days_bank = $this->db->where(array('goal_type_id' => 2, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

if (!empty($this_7_days_all)) {
    $this_7_days_all = $this_7_days_all;
} else {
    $this_7_days_all = array();
}
if (!empty($this_7_days_bank)) {
    $this_7_days_bank = $this_7_days_bank;
} else {
    $this_7_days_bank = array();
}


$terget_achievement = array_merge($this_7_days_all, $this_7_days_bank);
$total_terget = 0;
if (!empty($terget_achievement)) {
    foreach ($terget_achievement as $v_terget) {
        $total_terget += $v_terget->achievement;
    }
}
$tolal_goal = $all_goal + $bank_goal;
$curency = $this->transactions_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');

if ($this->session->userdata('user_type') == 1) {
    $margin = 'margin-bottom:30px';
    ?>
    <div class="col-sm-12 bg-white p0" style="<?= $margin ?>">
        <div class="col-md-4">
            <div class="row row-table pv-lg">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= display_money($tolal_goal, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('achievement') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 ">
                    <p class="m0 lead"><?= display_money($total_terget, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('last_weeks') . ' ' . lang('created') ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead"><?= display_money($complete_achivement, $curency->symbol) ?></p>
                    <p class="m0">
                        <small><?= lang('completed') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <div class="col-xs-6 pt">
                    <div data-sparkline="" data-bar-color="#23b7e5" data-height="60" data-bar-width="8"
                         data-bar-spacing="6" data-chart-range-min="0" values="<?php
                    if (!empty($this_7_days_deposit)) {
                        foreach ($this_7_days_deposit as $v_last_deposit) {
                            echo $v_last_deposit[0]->amount . ',';
                        }
                    }
                    ?>">
                    </div>
                    <p class="m0">
                        <small>
                            <?php
                            if (!empty($this_7_days_deposit)) {
                                foreach ($this_7_days_deposit as $date => $v_last_deposit) {
                                    echo date('d', strtotime($date)) . ' ';
                                }
                            }
                            ?>
                        </small>
                    </p>

                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="row row-table ">
                <div class="col-xs-6">
                    <p class="m0 lead">
                        <?php
                        if ($tolal_goal < $complete_achivement) {
                            $pending_goal = 0;
                        } else {
                            $pending_goal = $tolal_goal - $complete_achivement;
                        } ?>
                        <?= display_money($pending_goal, $curency->symbol) ?>
                    </p>
                    <p class="m0">
                        <small><?= lang('pending') . ' ' . lang('achievements') ?></small>
                    </p>
                </div>
                <?php
                if (!empty($tolal_goal)) {
                    if ($tolal_goal <= $complete_achivement) {
                        $total_progress = 100;
                    } else {
                        $progress = ($complete_achivement / $tolal_goal) * 100;
                        $total_progress = round($progress);
                    }
                } else {
                    $total_progress = 0;
                }
                ?>
                <div class="col-xs-6 text-center pt">
                    <div class="inline ">
                        <div class="easypiechart text-success"
                             data-percent="<?= $total_progress ?>"
                             data-line-width="5" data-track-Color="#f0f0f0"
                             data-bar-color="#<?php
                             if ($total_progress == 100) {
                                 echo '8ec165';
                             } elseif ($total_progress >= 40 && $total_progress <= 50) {
                                 echo '5d9cec';
                             } elseif ($total_progress >= 51 && $total_progress <= 99) {
                                 echo '7266ba';
                             } else {
                                 echo 'fb6b5b';
                             }
                             ?>" data-rotate="270" data-scale-Color="false"
                             data-size="50"
                             data-animate="2000">
                                                        <span class="small "><?= $total_progress ?>
                                                            %</span>
                            <span class="easypie-text"><strong><?= lang('done') ?></strong></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php }
$created = can_action('30', 'created');
$edited = can_action('30', 'edited');
$deleted = can_action('30', 'deleted');
$income_category = $this->db->get('tbl_income_category')->result();
$id = $this->uri->segment(5);
if (!empty($created) || !empty($edited)){
?>
<div class="row">
    <div class="col-sm-12">
        <?php $is_department_head = is_department_head();
        if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
            <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                 data-title="<?php echo lang('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu group animated zoomIn"
                    style="width:300px;">
                    <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                    <li class="divider"></li>

                    <li class="dropdown-submenu pull-left  " id="from_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('account'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_account"
                            style="">
                            <?php
                            $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                            if (!empty($account_info)) {
                                foreach ($account_info as $v_account) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_account->account_id ?>" search-type="by_account">
                                        <a href="#"><?php echo $v_account->account_name; ?></a>
                                    </li>
                                <?php }
                            }
                            ?>
                        </ul>
                    </li>
                    <div class="clearfix"></div>
                    <li class="dropdown-submenu pull-left " id="to_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('categories'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left to_account"
                            style="">
                            <?php
                            $income_category = $this->db->get('tbl_income_category')->result();
                            if (count($income_category) > 0) { ?>
                                <?php foreach ($income_category as $v_category) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_category->income_category_id ?>"
                                        search-type="by_category">
                                        <a href="#"><?php echo $v_category->income_category; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php } ?>
        <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs">
                <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                    data-toggle="tab"><?= lang('all_deposit') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                    data-toggle="tab"><?= lang('new_deposit') ?></a>
                </li>
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/transactions/import/Income"><?= lang('import') . ' ' . lang('deposit') ?></a>
                </li>
            </ul>
            <style type="text/css">
                .custom-bulk-button {
                    display: initial;
                }
            </style>
            <div class="tab-content no-padding  bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_deposit') ?></strong></div>
                        </header>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-striped DataTables bulk_table" id="DataTables" cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <?php if (!empty($deleted)) { ?>
                                        <th data-orderable="false">
                                            <div class="checkbox c-checkbox">
                                                <label class="needsclick">
                                                    <input id="select_all" type="checkbox">
                                                    <span class="fa fa-check"></span></label>
                                            </div>
                                        </th>
                                    <?php } ?>
                                    <th><?= lang('name') . '/' . lang('title') ?></th>
                                    <th><?= lang('date') ?></th>
                                    <th><?= lang('account') ?></th>
                                    <th><?= lang('paid_by') ?></th>
                                    <th><?= lang('amount') ?></th>
                                    <th><?= lang('balance') ?></th>
                                    <?php $show_custom_fields = custom_form_table(1, null);
                                    if (!empty($show_custom_fields)) {
                                        foreach ($show_custom_fields as $c_label => $v_fields) {
                                            if (!empty($c_label)) {
                                                ?>
                                                <th><?= $c_label ?> </th>
                                            <?php }
                                        }
                                    }
                                    ?>
                                    <th><?= lang('attachment') ?></th>
                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                        <th><?= lang('action') ?></th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        list = base_url + "admin/transactions/depositList";
                                        bulk_url = base_url + "admin/transactions/bulk_delete_deposit";
                                        $('.filtered > .dropdown-toggle').on('click', function () {
                                            if ($('.group').css('display') == 'block') {
                                                $('.group').css('display', 'none');
                                            } else {
                                                $('.group').css('display', 'block')
                                            }
                                        });
                                        $('.all_filter').on('click', function () {
                                            $('.to_account').removeAttr("style");
                                            $('.from_account').removeAttr("style");
                                        });
                                        $('.from_account li').on('click', function () {
                                            if ($('.to_account').css('display') == 'block') {
                                                $('.to_account').removeAttr("style");
                                                $('.from_account').css('display', 'block');
                                            } else {
                                                $('.from_account').css('display', 'block')
                                            }
                                        });

                                        $('.to_account li').on('click', function () {
                                            if ($('.from_account').css('display') == 'block') {
                                                $('.from_account').removeAttr("style");
                                                $('.to_account').css('display', 'block');
                                            } else {
                                                $('.to_account').css('display', 'block');
                                            }
                                        });
                                        $('.filter_by').on('click', function () {
                                            $('.filter_by').removeClass('active');
                                            $('.group').css('display', 'block');
                                            $(this).addClass('active');
                                            var filter_by = $(this).attr('id');
                                            if (filter_by) {
                                                filter_by = filter_by;
                                            } else {
                                                filter_by = '';
                                            }
                                            var search_type = $(this).attr('search-type');
                                            if (search_type) {
                                                search_type = '/' + search_type;
                                            } else {
                                                search_type = '';
                                            }
                                            table_url(base_url + "admin/transactions/depositList/" + filter_by + search_type);
                                        });
                                    });
                                </script>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if (!empty($created) || !empty($edited)) { ?>
                        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data"
                                  id="form"
                                  action="<?php echo base_url(); ?>admin/transactions/save_deposit/<?php
                                  if (!empty($deposit_info)) {
                                      echo $deposit_info->transactions_id;
                                  }
                                  ?>" method="post" class="form-horizontal ">
                                <div class="form-group">
                                    <label
                                        class="col-lg-3 control-label"><?= lang('name') . '/' . lang('title') ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" required
                                               placeholder="<?= lang('enter') . ' ' . lang('name') . '/' . lang('title') . ' ' . lang('for_personal') ?>"
                                               name="name" class="form-control" value="<?php
                                        if (!empty($deposit_info->name)) {
                                            echo strip_html_tags($deposit_info->name);
                                        } ?>">
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label class="col-lg-3 control-label mt-lg"><?= lang('account') ?> <span


                                            class="text-danger">*</span> </label>
                                    <div class="col-lg-5 mt-lg">
                                        <div class="input-group">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="account_id"
                                                    required <?php
                                            if (!empty($deposit_info) && $deposit_info->account_id != '0') {
                                                echo 'disabled';
                                            }
                                            ?>>
                                                <?php
                                                $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                                if (!empty($account_info)) {
                                                    foreach ($account_info as $v_account) {
                                                        ?>
                                                        <option value="<?= $v_account->account_id ?>"
                                                            <?php
                                                            if (!empty($deposit_info->account_id)) {
                                                                echo $deposit_info->account_id == $v_account->account_id ? 'selected' : '';
                                                            }
                                                            ?>
                                                        ><?= $v_account->account_name ?></option>
                                                        <?php
                                                    }
                                                }
                                                $acreated = can_action('36', 'created');
                                                ?>
                                            </select>
                                            <?php if (!empty($acreated)) { ?>
                                                <div class="input-group-addon"
                                                     title="<?= lang('new') . ' ' . lang('account') ?>"
                                                     data-toggle="tooltip" data-placement="top">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/account/new_account"><i
                                                            class="fa fa-plus"></i></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date') ?></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" name="date" class="form-control datepicker" value="<?php
                                            if (!empty($deposit_info->date)) {
                                                echo $deposit_info->date;
                                            } else {
                                                echo date('Y-m-d');
                                            }
                                            ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('notes') ?> </label>
                                    <div class="col-lg-5">
                        <textarea name="notes" class="form-control"><?php
                            if (!empty($deposit_info)) {
                                echo $deposit_info->notes;
                            }
                            ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('amount') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-5">
                                        <div class="input-group  ">
                                            <input class="form-control " data-parsley-type="number" type="text"
                                                   value="<?php
                                                   if (!empty($deposit_info)) {
                                                       echo $deposit_info->amount;
                                                   }
                                                   ?>" name="amount" required="" <?php
                                            if (!empty($deposit_info)) {
                                                echo 'disabled';
                                            }
                                            ?>>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($deposit_info)) { ?>
                                    <input class="form-control " type="hidden"
                                           value="<?php echo $deposit_info->amount; ?>"
                                           name="amount">
                                    <input class="form-control " type="hidden" value="<?php
                                    if (!empty($deposit_info)) {
                                        echo $deposit_info->account_id;
                                    }
                                    ?>" name="old_account_id">
                                <?php } ?>
                                <div class="more_option">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('deposit_category') ?> </label>
                                        <div class="col-lg-5">
                                            <div class="input-group">
                                                <select class="form-control select_box" style="width: 100%"
                                                        name="category_id">
                                                    <option value="0"><?= lang('none') ?></option>
                                                    <?php
                                                    $category_info = $this->db->order_by('income_category_id', 'DESC')->get('tbl_income_category')->result();
                                                    if (!empty($category_info)) {
                                                        foreach ($category_info as $v_category) {
                                                            ?>
                                                            <option value="<?= $v_category->income_category_id ?>"
                                                                <?php
                                                                if (!empty($deposit_info->category_id)) {
                                                                    echo $deposit_info->category_id == $v_category->income_category_id ? 'selected' : '';
                                                                }
                                                                ?>
                                                            ><?= $v_category->income_category ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    $created = can_action('125', 'created');
                                                    ?>
                                                </select>
                                                <?php if (!empty($created)) { ?>
                                                    <div class="input-group-addon"
                                                         title="<?= lang('new') . ' ' . lang('deposit_category') ?>"
                                                         data-toggle="tooltip" data-placement="top">
                                                        <a data-toggle="modal" data-target="#myModal"
                                                           href="<?= base_url() ?>admin/transactions/categories/income"><i
                                                                class="fa fa-plus"></i></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('paid_by') ?> </label>
                                        <div class="col-lg-5">
                                            <div class="input-group">
                                                <select class="form-control select_box" style="width: 100%"
                                                        name="paid_by">
                                                    <option value="0"><?= lang('select_payer') ?></option>
                                                    <?php
                                                    $all_client = $this->db->get('tbl_client')->result();
                                                    if (!empty($all_client)) {
                                                        foreach ($all_client as $v_client) {
                                                            ?>
                                                            <option value="<?= $v_client->client_id ?>"
                                                                <?php
                                                                if (!empty($deposit_info)) {
                                                                    echo $deposit_info->paid_by == $v_client->client_id ? 'selected' : '';
                                                                }
                                                                ?>
                                                            ><?= ucfirst($v_client->name) ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    $acreated = can_action('4', 'created');
                                                    ?>
                                                </select>
                                                <?php if (!empty($acreated)) { ?>
                                                    <div class="input-group-addon"
                                                         title="<?= lang('new') . ' ' . lang('paid_by') ?>"
                                                         data-toggle="tooltip" data-placement="top">
                                                        <a data-toggle="modal" data-target="#myModal"
                                                           href="<?= base_url() ?>admin/client/new_client"><i
                                                                    class="fa fa-plus"></i></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('payment_method') ?> </label>
                                        <div class="col-lg-5">
                                            <div class="input-group">
                                                <select class="form-control select_box" style="width: 100%"
                                                        name="payment_methods_id">
                                                    <option value="0"><?= lang('select_payment_method') ?></option>
                                                    <?php
                                                    $payment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                                    if (!empty($payment_methods)) {
                                                        foreach ($payment_methods as $p_method) {
                                                            ?>
                                                            <option value="<?= $p_method->payment_methods_id ?>" <?php
                                                            if (!empty($expense_info)) {
                                                                echo $expense_info->payment_methods_id == $p_method->payment_methods_id ? 'selected' : '';
                                                            }
                                                            ?>><?= $p_method->method_name ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <div class="input-group-addon"
                                                     title="<?= lang('new') . ' ' . lang('payment_method') ?>"
                                                     data-toggle="tooltip" data-placement="top">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/settings/inline_payment_method"><i
                                                            class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label"><?= lang('reference') ?> </label>
                                        <div class="col-lg-5">

                                            <input class="form-control " type="text" value="<?php
                                            if (!empty($deposit_info)) {
                                                echo $deposit_info->reference;
                                            }
                                            ?>" name="reference">

                                            <input class="form-control " type="hidden" value="<?php
                                            if (!empty($deposit_info)) {
                                                echo $deposit_info->account_id;
                                            }
                                            ?>" name="old_account_id">

                                            <span class="help-block"><?= lang('reference_example') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 0px">
                                    <label for="field-1"
                                           class="col-sm-3 control-label"><?= lang('attachment') ?></label>

                                    <div class="col-sm-5">
                                        <div id="comments_file-dropzone" class="dropzone mb15">

                                        </div>
                                        <div id="comments_file-dropzone-scrollbar">
                                            <div id="comments_file-previews">
                                                <div id="file-upload-row" class="mt pull-left">

                                                    <div class="preview box-content pr-lg" style="width:100px;">
                                                    <span data-dz-remove class="pull-right" style="cursor: pointer">
                                    <i class="fa fa-times"></i>
                                </span>
                                                        <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                        <input class="file-count-field" type="hidden" name="files[]"
                                                               value=""/>
                                                        <div
                                                            class="mb progress progress-striped upload-progress-sm active mt-sm"
                                                            role="progressbar" aria-valuemin="0" aria-valuemax="100"
                                                            aria-valuenow="0">
                                                            <div class="progress-bar progress-bar-success"
                                                                 style="width:0%;"
                                                                 data-dz-uploadprogress></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($deposit_info->attachement)) {
                                            $uploaded_file = json_decode($deposit_info->attachement);
                                        }
                                        if (!empty($uploaded_file)) {
                                            foreach ($uploaded_file as $v_files_image) { ?>
                                                <div class="pull-left mt pr-lg mb" style="width:100px;">
                                                        <span data-dz-remove class="pull-right existing_image"
                                                              style="cursor: pointer"><i
                                                                class="fa fa-times"></i></span>
                                                    <?php if ($v_files_image->is_image == 1) { ?>
                                                        <img data-dz-thumbnail
                                                             src="<?php echo base_url() . $v_files_image->path ?>"
                                                             class="upload-thumbnail-sm"/>
                                                    <?php } else { ?>
                                                        <span data-toggle="tooltip" data-placement="top"
                                                              title="<?= $v_files_image->fileName ?>"
                                                              class="mailbox-attachment-icon"><i
                                                                class="fa fa-file-text-o"></i></span>
                                                    <?php } ?>

                                                    <input type="hidden" name="path[]"
                                                           value="<?php echo $v_files_image->path ?>">
                                                    <input type="hidden" name="fileName[]"
                                                           value="<?php echo $v_files_image->fileName ?>">
                                                    <input type="hidden" name="fullPath[]"
                                                           value="<?php echo $v_files_image->fullPath ?>">
                                                    <input type="hidden" name="size[]"
                                                           value="<?php echo $v_files_image->size ?>">
                                                    <input type="hidden" name="is_image[]"
                                                           value="<?php echo $v_files_image->is_image ?>">
                                                </div>
                                            <?php }; ?>
                                        <?php }; ?>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $(".existing_image").click(function () {
                                                    $(this).parent().remove();
                                                });

                                                fileSerial = 0;
                                                // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                                var previewNode = document.querySelector("#file-upload-row");
                                                previewNode.id = "";
                                                var previewTemplate = previewNode.parentNode.innerHTML;
                                                previewNode.parentNode.removeChild(previewNode);
                                                Dropzone.autoDiscover = false;
                                                var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                                    url: "<?= base_url()?>admin/global_controller/upload_file",
                                                    thumbnailWidth: 80,
                                                    thumbnailHeight: 80,
                                                    parallelUploads: 20,
                                                    previewTemplate: previewTemplate,
                                                    dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                                    autoQueue: true,
                                                    previewsContainer: "#comments_file-previews",
                                                    clickable: true,
                                                    accept: function (file, done) {
                                                        if (file.name.length > 200) {
                                                            done("Filename is too long.");
                                                            $(file.previewTemplate).find(".description-field").remove();
                                                        }
                                                        //validate the file
                                                        $.ajax({
                                                            url: "<?= base_url()?>admin/global_controller/validate_project_file",
                                                            data: {file_name: file.name, file_size: file.size},
                                                            cache: false,
                                                            type: 'POST',
                                                            dataType: "json",
                                                            success: function (response) {
                                                                if (response.success) {
                                                                    fileSerial++;
                                                                    $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                                    $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                                                        <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                                    $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                                    done();
                                                                } else {
                                                                    $(file.previewTemplate).find("input").remove();
                                                                    done(response.message);
                                                                }
                                                            }
                                                        });
                                                    },
                                                    processing: function () {
                                                        $("#file-save-button").prop("disabled", true);
                                                    },
                                                    queuecomplete: function () {
                                                        $("#file-save-button").prop("disabled", false);
                                                    },
                                                    fallback: function () {
                                                        //add custom fallback;
                                                        $("body").addClass("dropzone-disabled");
                                                        $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                                        $("#comments_file-dropzone").hide();

                                                        $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                                        $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                                            var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                                + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                                + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                                + "</div>";
                                                            $("#comments_file-previews").prepend(newFileRow);
                                                        });
                                                        $("#add-more-file-button").trigger("click");
                                                        $("#comments_file-previews").on("click", ".remove-file", function () {
                                                            $(this).closest(".file-row").remove();
                                                        });
                                                    },
                                                    success: function (file) {
                                                        setTimeout(function () {
                                                            $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                                        }, 1000);
                                                    }
                                                });

                                            })
                                        </script>
                                    </div>
                                </div>

                                <?php
                                if (!empty($deposit_info)) {
                                    $transactions_id = $deposit_info->transactions_id;
                                } else {
                                    $transactions_id = null;
                                }
                                ?>
                                <?= custom_form_Fields(1, $transactions_id); ?>

                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('permission') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="checkbox c-radio needsclick">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if (!empty($deposit_info->permission) && $deposit_info->permission == 'all') {
                                                    echo 'checked';
                                                } elseif (empty($deposit_info)) {
                                                    echo 'checked';
                                                }
                                                ?> type="radio" name="permission" value="everyone">
                                                <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                                <i title="<?= lang('permission_for_all') ?>"
                                                   class="fa fa-question-circle" data-toggle="tooltip"
                                                   data-placement="top"></i>
                                            </label>
                                        </div>
                                        <div class="checkbox c-radio needsclick">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                    echo 'checked';
                                                }
                                                ?> type="radio" name="permission" value="custom_permission"
                                                >
                                                <span class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                <i
                                                    title="<?= lang('permission_for_customization') ?>"
                                                    class="fa fa-question-circle" data-toggle="tooltip"
                                                    data-placement="top"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group <?php
                                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                    echo 'show';
                                }
                                ?>" id="permission_user_1">
                                    <label for="field-1"
                                           class="col-sm-3 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <?php
                                        if (!empty($permission_user)) {
                                            foreach ($permission_user as $key => $v_user) {

                                                if ($v_user->role_id == 1) {
                                                    $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                                } else {
                                                    $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                                }

                                                ?>
                                                <div class="checkbox c-checkbox needsclick">
                                                    <label class="needsclick">
                                                        <input type="checkbox"
                                                            <?php
                                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                                $get_permission = json_decode($deposit_info->permission);
                                                                foreach ($get_permission as $user_id => $v_permission) {
                                                                    if ($user_id == $v_user->user_id) {
                                                                        echo 'checked';
                                                                    }
                                                                }

                                                            }
                                                            ?>
                                                               value="<?= $v_user->user_id ?>"
                                                               name="assigned_to[]"
                                                               class="needsclick">
                                                        <span
                                                            class="fa fa-check"></span><?= $v_user->username . ' ' . $role ?>
                                                    </label>

                                                </div>
                                                <div class="action_1 p
                                                <?php

                                                if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                    $get_permission = json_decode($deposit_info->permission);

                                                    foreach ($get_permission as $user_id => $v_permission) {
                                                        if ($user_id == $v_user->user_id) {
                                                            echo 'show';
                                                        }
                                                    }

                                                }
                                                ?>
                                                " id="action_1<?= $v_user->user_id ?>">
                                                    <label class="checkbox-inline c-checkbox">
                                                        <input id="<?= $v_user->user_id ?>" checked type="checkbox"
                                                               name="action_1<?= $v_user->user_id ?>[]"
                                                               disabled
                                                               value="view">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                                    </label>
                                                    <label class="checkbox-inline c-checkbox">
                                                        <input id="<?= $v_user->user_id ?>"
                                                            <?php

                                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                                $get_permission = json_decode($deposit_info->permission);

                                                                foreach ($get_permission as $user_id => $v_permission) {
                                                                    if ($user_id == $v_user->user_id) {
                                                                        if (in_array('edit', $v_permission)) {
                                                                            echo 'checked';
                                                                        };

                                                                    }
                                                                }

                                                            }
                                                            ?>
                                                               type="checkbox"
                                                               value="edit" name="action_<?= $v_user->user_id ?>[]">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                                    </label>
                                                    <label class="checkbox-inline c-checkbox">
                                                        <input id="<?= $v_user->user_id ?>"
                                                            <?php

                                                            if (!empty($deposit_info->permission) && $deposit_info->permission != 'all') {
                                                                $get_permission = json_decode($deposit_info->permission);
                                                                foreach ($get_permission as $user_id => $v_permission) {
                                                                    if ($user_id == $v_user->user_id) {
                                                                        if (in_array('delete', $v_permission)) {
                                                                            echo 'checked';
                                                                        };
                                                                    }
                                                                }

                                                            }
                                                            ?>
                                                               name="action_<?= $v_user->user_id ?>[]"
                                                               type="checkbox"
                                                               value="delete">
                                                        <span
                                                            class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                                    </label>
                                                    <input id="<?= $v_user->user_id ?>" type="hidden"
                                                           name="action_<?= $v_user->user_id ?>[]" value="view">

                                                </div>


                                                <?php
                                            }
                                        }
                                        ?>


                                    </div>
                                </div>
                                <div class="btn-bottom-toolbar text-right">
                                    <?php
                                    if (!empty($deposit_info)) { ?>
                                        <button type="submit" id="file-save-button"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        <button type="button" onclick="goBack()"
                                                class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                    <?php } else {
                                        ?>
                                        <button type="submit" id="file-save-button"
                                                class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                    <?php }
                                    ?>
                                </div>
                            </form>
                        </div>
                    <?php }else{ ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('attachment') ?></label>\n\
        <div class="col-sm-4">\n\
        <div class="fileinput fileinput-new" data-provides="fileinput">\n\
<span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="attachement[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
<a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
        $('a.RCF').click(function () {
            $(this).parent().parent().remove();
        });
    });
</script>
