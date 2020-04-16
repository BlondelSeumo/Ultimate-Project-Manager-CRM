<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('all_income') ?>
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
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th><?= lang('date') ?></th>
                    <th><?= lang('account_name') ?></th>
                    <th class="col-date"><?= lang('notes') ?></th>
                    <th class="col-currency"><?= lang('amount') ?></th>
                    <th class="col-currency"><?= lang('credit') ?></th>
                    <th class="col-currency"><?= lang('debit') ?></th>
                    <th class="col-currency"><?= lang('balance') ?></th>
                    <th class="col-options no-sort"><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/report/incomeList";
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
                            table_url(base_url + "admin/report/incomeList/" + filter_by + search_type);
                        });
                    });
                </script>

                <?php
                $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                $total_amount = 0;
                $total_credit = 0;
                $total_debit = 0;
                $total_balance = 0;
                $all_expense_info = $this->db->where(array('type' => 'Income'))->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                foreach ($all_expense_info as $v_income) :
                    $account_info = $this->report_model->check_by(array('account_id' => $v_income->account_id), 'tbl_accounts');

                    $total_amount += $v_income->amount;
                    $total_credit += $v_income->credit;
                    $total_debit += $v_income->debit;
                    $total_balance += $v_income->total_balance;
                    ?>
                    <?php
                endforeach;
                ?>

                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <strong style="width: 25%"><?= lang('balance') ?>:<span
                class="label label-info"><?= display_money($total_credit - $total_debit, $curency->symbol) ?></span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('total_amount') ?>:<span
                class="label label-success">
                <?= display_money($total_amount, $curency->symbol) ?>
            </span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('credit') ?>:<span
                class="label label-primary">
                <?= display_money($total_credit, $curency->symbol) ?>
            </span></span>
        </strong>
        <strong class="col-sm-3"><?= lang('debit') ?>:<span
                class="label label-danger">
                <?= display_money($total_debit, $curency->symbol) ?>
                </span></span>
        </strong>

    </div>
</div>