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
        if ($v_goal_track->goal_type_id == 3) {
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
        if ($v_goal_track->goal_type_id == 4) {
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
    $this_7_days_deposit[$date] = $this->db->select_sum('amount')->where(array('type' => 'Expense', 'date >=' => $date, 'date <=' => $date))->get('tbl_transactions')->result();
}

$this_7_days_all = $this->db->where(array('goal_type_id' => 3, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

$this_7_days_bank = $this->db->where(array('goal_type_id' => 4, 'start_date >=' => $last_7_days, 'end_date <=' => $mdate))->get('tbl_goal_tracking')->result();

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
$created = can_action('31', 'created');
$edited = can_action('31', 'edited');
$deleted = can_action('31', 'deleted');
$expense_category = $this->db->get('tbl_expense_category')->result();
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
                            $income_category = $this->db->get('tbl_expense_category')->result();
                            if (count($income_category) > 0) { ?>
                                <?php foreach ($income_category as $v_category) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_category->expense_category_id ?>"
                                        search-type="by_category">
                                        <a href="#"><?php echo $v_category->expense_category; ?></a>
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
                                                                    data-toggle="tab"><?= lang('all_expense') ?></a>
                </li>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                    data-toggle="tab"><?= lang('new_expense') ?></a>
                </li>
                <li><a style="background-color: #1797be;color: #ffffff"
                       href="<?= base_url() ?>admin/transactions/import/Expense"><?= lang('import') . ' ' . lang('expense') ?></a>
                </li>
            </ul>
            <style type="text/css">
                .custom-bulk-button {
                    display: initial;
                }
            </style>
            <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <?php } else { ?>
                    <div class="panel panel-custom">
                        <header class="panel-heading ">
                            <div class="panel-title"><strong><?= lang('all_expense') ?></strong></div>
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
                                    <th><?= lang('account_name') ?></th>
                                    <th class="col-currency"><?= lang('amount') ?></th>
                                    <th><?= lang('status') ?></th>
                                    <?php $show_custom_fields = custom_form_table(2, null);
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
                                    <th><?= lang('action') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        list = base_url + "admin/transactions/expenseList";
                                        bulk_url = base_url + "admin/transactions/bulk_delete_expense";
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
                                            table_url(base_url + "admin/transactions/expenseList/" + filter_by + search_type);
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
                                  action="<?php echo base_url(); ?>admin/transactions/save_expense/<?php
                                  if (!empty($expense_info)) {
                                      echo $expense_info->transactions_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label
                                        class="col-lg-2 control-label"><?= lang('name') . '/' . lang('title') ?></label>
                                    <div class="col-lg-4">
                                        <input type="text" required
                                               placeholder="<?= lang('enter') . ' ' . lang('name') . '/' . lang('title') . ' ' . lang('for_personal') ?>"
                                               name="name" class="form-control" value="<?php
                                        if (!empty($expense_info->name)) {
                                            echo $expense_info->name;
                                        } ?>">
                                    </div>

                                    <label class="col-lg-2 control-label"><?= lang('date') ?></label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <input type="text" name="date" class="form-control datepicker"
                                                   value="<?php
                                                   if (!empty($expense_info->date)) {
                                                       echo $expense_info->date;
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
                                <?php $project_id = $this->uri->segment(5);
                                if (!empty($expense_info->project_id)) {
                                    $project_id = $expense_info->project_id;
                                }
                                $project = $this->db->where('project_id', $project_id)->get('tbl_project')->row();
                                if (!empty($project)) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label"><?= lang('project') ?></label>
                                        <div class="col-lg-5">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="project_id">
                                                <option
                                                    value="<?php echo $project_id; ?>"><?= $project->project_name ?></option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('account') ?> <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="account_id"
                                                    required <?php
                                            if (!empty($expense_info) && $expense_info->account_id != '0') {
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
                                                            if (!empty($expense_info)) {
                                                                echo $expense_info->account_id == $v_account->account_id ? 'selected' : '';
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

                                    <label class="col-lg-2 control-label"><?= lang('amount') ?> <span
                                                class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-4">
                                        <div class="input-group  ">
                                            <input class="form-control " data-parsley-type="number" type="text"
                                                   value="<?php
                                                   if (!empty($expense_info)) {
                                                       echo $expense_info->amount;
                                                   }
                                                   ?>" name="amount" required="" <?php
                                            if (!empty($expense_info)) {
                                                echo 'disabled';
                                            }
                                            ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label
                                            class="col-lg-2 control-label"><?= lang('deposit_category') ?> </label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select <?php
                                            if (!empty($project)) {
                                                echo 'required=""';
                                            }
                                            ?> class="form-control select_box" style="width: 100%"
                                               name="category_id">
                                                <option value="0"><?= lang('none') ?></option>
                                                <?php
                                                $category_info = $this->db->order_by('expense_category_id', 'DESC')->get('tbl_expense_category')->result();
                                                if (!empty($category_info)) {
                                                    foreach ($category_info as $v_category) {
                                                        ?>
                                                        <option value="<?= $v_category->expense_category_id ?>"
                                                            <?php
                                                            if (!empty($expense_info->category_id)) {
                                                                echo $expense_info->category_id == $v_category->expense_category_id ? 'selected' : '';
                                                            }
                                                            ?>
                                                        ><?= $v_category->expense_category ?></option>
                                                        <?php
                                                    }
                                                }
                                                $created = can_action('124', 'created');
                                                ?>
                                            </select>
                                            <?php if (!empty($created)) { ?>
                                                <div class="input-group-addon"
                                                     title="<?= lang('new') . ' ' . lang('deposit_category') ?>"
                                                     data-toggle="tooltip" data-placement="top">
                                                    <a data-toggle="modal" data-target="#myModal"
                                                       href="<?= base_url() ?>admin/transactions/categories/expense"><i
                                                                class="fa fa-plus"></i></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <label class="col-lg-2 control-label"><?= lang('paid_by') ?> </label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="paid_by">
                                                <?php $all_client = $this->db->get('tbl_client')->result();
                                                if (!empty($project)) {
                                                    $client_name = $this->db->where('client_id', $project->client_id)->get('tbl_client')->row();
                                                    ?>
                                                    <option
                                                            value="<?= $project->client_id ?>"><?= $client_name->name ?></option>
                                                <?php } else { ?>
                                                    <option value="0"><?= lang('select_payer') ?></option>
                                                    <?php if (!empty($all_client)) {
                                                        foreach ($all_client as $v_client) {
                                                            ?>
                                                            <option value="<?= $v_client->client_id ?>"
                                                                <?php
                                                                if (!empty($expense_info)) {
                                                                    echo $expense_info->paid_by == $v_client->client_id ? 'selected' : '';
                                                                }
                                                                ?>
                                                            ><?= ucfirst($v_client->name); ?></option>
                                                            <?php
                                                        }
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
                                    <label class="col-lg-2 control-label"><?= lang('payment_method') ?> </label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select class="form-control select_box" style="width: 100%"
                                                    name="payment_methods_id">
                                                <option value="0"><?= lang('select_payment_method') ?></option>
                                                <?php
                                                $payment_methods = $this->db->order_by('payment_methods_id', 'DESC')->get('tbl_payment_methods')->result();
                                                if (!empty($payment_methods)) {
                                                    foreach ($payment_methods as $p_method) {
                                                        ?>
                                                        <option
                                                                value="<?= $p_method->payment_methods_id ?>" <?php
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
                                    <label class="col-lg-2 control-label"><?= lang('reference') ?> </label>
                                    <div class="col-lg-4">
                                        <input class="form-control " type="text" value="<?php
                                        if (!empty($expense_info)) {
                                            echo $expense_info->reference;
                                        }
                                        ?>" name="reference">
                                        <span class="help-block"><?= lang('reference_example') ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($expense_info)) { ?>
                                    <input class="form-control " type="hidden"
                                           value="<?php echo $expense_info->amount; ?>"
                                           name="amount">
                                <?php } ?>


                                <div class="form-group" style="margin-bottom: 0px">
                                    <label class="col-lg-2 control-label"><?= lang('notes') ?> </label>
                                    <div class="col-lg-4">
                        <textarea name="notes" class="form-control"><?php
                            if (!empty($expense_info)) {
                                echo $expense_info->notes;
                            }
                            ?></textarea>
                                    </div>
                                    <label for="field-1"
                                           class="col-lg-2 control-label"><?= lang('attachment') ?></label>
                                    <div class="col-lg-4">
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
                                        if (!empty($expense_info->attachement)) {
                                            $uploaded_file = json_decode($expense_info->attachement);
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
                                <div class="form-group" <?php if (isset($expense_info) && !empty($expense_info->recurring_from)) { ?> data-toggle="tooltip" data-title="<?php echo lang('create_recurring_from_child_error_message', [lang('expense_lowercase'), lang('expense_lowercase'), lang('expense_lowercase')]); ?>"<?php } ?>>
                                    <label class=" col-lg-2 control-label"><?php echo lang('repeat_every'); ?></label>
                                    <div class="col-lg-4">
                                        <select name="repeat_every"
                                                id="repeat_every"
                                                class="selectpicker"
                                                data-width="100%"
                                                data-none-selected-text="<?php echo lang('none'); ?>"
                                            <?php if (isset($expense_info) && !empty($expense_info->recurring_from)) { ?> disabled <?php } ?>>
                                            <option value=""></option>
                                            <option value="1_week" <?php if (isset($expense_info) && $expense_info->repeat_every == 1 && $expense_info->recurring_type == 'week') {
                                                echo 'selected';
                                            } ?>><?php echo lang('week'); ?></option>
                                            <option value="2_week" <?php if (isset($expense_info) && $expense_info->repeat_every == 2 && $expense_info->recurring_type == 'week') {
                                                echo 'selected';
                                            } ?>>2 <?php echo lang('weeks'); ?></option>
                                            <option value="1_month" <?php if (isset($expense_info) && $expense_info->repeat_every == 1 && $expense_info->recurring_type == 'month') {
                                                echo 'selected';
                                            } ?>>1 <?php echo lang('month'); ?></option>
                                            <option value="2_month" <?php if (isset($expense_info) && $expense_info->repeat_every == 2 && $expense_info->recurring_type == 'month') {
                                                echo 'selected';
                                            } ?>>2 <?php echo lang('months'); ?></option>
                                            <option value="3_month" <?php if (isset($expense_info) && $expense_info->repeat_every == 3 && $expense_info->recurring_type == 'month') {
                                                echo 'selected';
                                            } ?>>3 <?php echo lang('months'); ?></option>
                                            <option value="6_month" <?php if (isset($expense_info) && $expense_info->repeat_every == 6 && $expense_info->recurring_type == 'month') {
                                                echo 'selected';
                                            } ?>>6 <?php echo lang('months'); ?></option>
                                            <option value="1_year" <?php if (isset($expense_info) && $expense_info->repeat_every == 1 && $expense_info->recurring_type == 'year') {
                                                echo 'selected';
                                            } ?>>1 <?php echo lang('year'); ?></option>
                                            <option value="custom" <?php if (isset($expense_info) && $expense_info->custom_recurring == 1) {
                                                echo 'selected';
                                            } ?>><?php echo lang('custom'); ?></option>
                                        </select>

                                        <div class="recurring_custom <?php if ((isset($expense_info) && $expense_info->custom_recurring != 1) || (!isset($expense_info))) {
                                            echo 'hide';
                                        } ?>">
                                            <div class="input-group">
                                                <?php $value = (isset($expense_info) && $expense_info->custom_recurring == 1 ? $expense_info->repeat_every : 1); ?>
                                                <input type="number" name="repeat_every_custom" class="form-control"
                                                       min="1"
                                                       value="<?= $value ?>">
                                                <div class="input-group-addon p0 b0">
                                                    <select name="repeat_type_custom" id="repeat_type_custom"
                                                            class="selectpicker" data-width="100%"
                                                            data-none-selected-text="<?php echo lang('none'); ?>">
                                                        <option value="day" <?php if (isset($expense_info) && $expense_info->custom_recurring == 1 && $expense_info->recurring_type == 'day') {
                                                            echo 'selected';
                                                        } ?>><?php echo lang('days'); ?></option>
                                                        <option value="week" <?php if (isset($expense_info) && $expense_info->custom_recurring == 1 && $expense_info->recurring_type == 'week') {
                                                            echo 'selected';
                                                        } ?>><?php echo lang('weeks'); ?></option>
                                                        <option value="month" <?php if (isset($expense_info) && $expense_info->custom_recurring == 1 && $expense_info->recurring_type == 'month') {
                                                            echo 'selected';
                                                        } ?>><?php echo lang('months'); ?></option>
                                                        <option value="year" <?php if (isset($expense_info) && $expense_info->custom_recurring == 1 && $expense_info->recurring_type == 'year') {
                                                            echo 'selected';
                                                        } ?>><?php echo lang('years'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cycles_wrapper"
                                         class="<?php if (!isset($expense_info) || (isset($expense_info) && $expense_info->recurring == 'No')) {
                                             echo ' hide';
                                         } ?>">
                                        <?php $value = (isset($expense_info) ? $expense_info->total_cycles : 0); ?>
                                        <div class="recurring-cycles">
                                            <label class="col-lg-2 control-label"
                                                   for="cycles"><?php echo lang('total_cycles'); ?>
                                            </label>
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <input type="number" class="form-control"<?php if ($value == 0) {
                                                        echo ' disabled';
                                                    } ?> name="total_cycles" id="cycles"
                                                           value="<?php echo $value; ?>" <?php if (isset($expense_info) && $expense_info->done_cycles > 0) {
                                                        echo 'min="' . ($expense_info->done_cycles) . '"';
                                                    } ?>>
                                                    <div class="input-group-addon">
                                                        <input data-toggle="tooltip"
                                                               title="<?php echo lang('cycles_infinity'); ?>"
                                                               type="checkbox"<?php if ($value == 0) {
                                                            echo ' checked';
                                                        } ?> id="unlimited_cycles">
                                                    </div>
                                                </div>
                                                <?php if (isset($expense_info) && $expense_info->done_cycles > 0) {
                                                    echo '<small>' . lang('total_cycles_passed', $expense_info->done_cycles) . '</small>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if (!empty($expense_info)) {
                                    $transactions_id = $expense_info->transactions_id;
                                } else {
                                    $transactions_id = null;
                                }
                                ?>
                                <?= custom_form_Fields(2, $transactions_id,true); ?>
                                <?php if (!empty($project_id)): ?>
                                    <div class="form-group mt-lg">
                                        <label for="field-1"
                                               class="col-sm-2 control-label"><?= lang('billable') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-8">
                                            <input data-toggle="toggle" name="billable" value="Yes" <?php
                                            if (!empty($expense_info) && $expense_info->billable == 'Yes') {
                                                echo 'checked';
                                            }
                                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                   data-onstyle="success" data-offstyle="danger" type="checkbox">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="field-1"
                                               class="col-sm-2 control-label"><?= lang('visible_to_client') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-8">
                                            <input data-toggle="toggle" name="client_visible" value="Yes" <?php
                                            if (!empty($expense_info) && $expense_info->client_visible == 'Yes') {
                                                echo 'checked';
                                            }
                                            ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                                   data-onstyle="success" data-offstyle="danger" type="checkbox">
                                        </div>
                                    </div>
                                <?php endif ?>

                                <input class="form-control " type="hidden" value="<?php
                                if (!empty($expense_info)) {
                                    echo $expense_info->account_id;
                                }
                                ?>" name="old_account_id">
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-2 control-label"><?= lang('permission') ?>
                                        <span
                                            class="required">*</span></label>
                                    <div class="col-sm-9">
                                        <div class="checkbox c-radio needsclick">
                                            <label class="needsclick">
                                                <input id="" <?php
                                                if (!empty($expense_info->permission) && $expense_info->permission == 'all') {
                                                    echo 'checked';
                                                } elseif (empty($expense_info)) {
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
                                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
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
                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                    echo 'show';
                                }
                                ?>" id="permission_user_1">
                                    <label for="field-1"
                                           class="col-sm-2 control-label"><?= lang('select') . ' ' . lang('users') ?>
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
                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);
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

                                                if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                    $get_permission = json_decode($expense_info->permission);

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

                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);

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

                                                            if (!empty($expense_info->permission) && $expense_info->permission != 'all') {
                                                                $get_permission = json_decode($expense_info->permission);
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
                                    if (!empty($expense_info)) { ?>
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
<script>
    $('#repeat_every').on('change', function () {
        if ($('input[name="billable"]').prop('checked') == true) {
            $('.billable_recurring_options').removeClass('hide');
        } else {
            $('.billable_recurring_options').addClass('hide');
        }
    });
    // hide invoice recurring options on page load
    $('#repeat_every').trigger('change');
</script>