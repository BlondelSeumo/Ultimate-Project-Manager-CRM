<div id="printReport">
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
    <div class="panel panel-custom">
        <!-- Default panel contents -->
        <div class="panel-heading">
            <div class="panel-title">
                <strong><?= lang('transfer_report') ?></strong>

                <div class="pull-right hidden-print">
                    <div class="btn-group filtered">
                        <button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <?= lang('search_by') ?><span class="caret"></span></button>
                        <ul class="dropdown-menu group animated zoomIn"
                            style="width:300px;">
                            <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                            <li class="divider"></li>

                            <li class="dropdown-submenu pull-left  " id="from_account">
                                <a href="#" tabindex="-1"><?php echo lang('from_account'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left from_account"
                                    style="">
                                    <?php
                                    $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                    if (!empty($account_info)) {
                                        foreach ($account_info as $v_account) {
                                            ?>
                                            <li class="filter_by" id="<?= $v_account->account_id ?>" search-type="from_account">
                                                <a href="#"><?php echo $v_account->account_name; ?></a>
                                            </li>
                                        <?php }
                                    }
                                    ?>
                                </ul>
                            </li>
                            <div class="clearfix"></div>
                            <li class="dropdown-submenu pull-left " id="to_account">
                                <a href="#" tabindex="-1"><?php echo lang('to_account'); ?></a>
                                <ul class="dropdown-menu dropdown-menu-left to_account"
                                    style="">
                                    <?php
                                    $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                                    if (!empty($account_info)) {
                                        foreach ($account_info as $v_account) {
                                            ?>
                                            <li class="filter_by" id="<?= $v_account->account_id ?>" search-type="to_account">
                                                <a href="#"><?php echo $v_account->account_name; ?></a>
                                            </li>
                                        <?php }
                                    }
                                    ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <a href="<?php echo base_url() ?>admin/transactions/transfer_report_pdf"
                       class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="top"
                       title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                    <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger"
                       data-toggle="tooltip" data-placement="top"
                       title="<?= lang('print') ?>"><?= lang('print') ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="width: 15%"><?= lang('date') ?></th>
                        <th style="width: 15%"><?= lang('from_account') ?></th>
                        <th style="width: 15%"><?= lang('to_account') ?></th>
                        <th><?= lang('type') ?></th>
                        <th><?= lang('amount') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/transactions/transfer_reportList";
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
                                var search_type = $(this).attr('search-type');
                                if (filter_by) {
                                    filter_by = filter_by;
                                } else {
                                    filter_by = '';
                                }
                                if (search_type) {
                                    search_type = '/' + search_type;
                                } else {
                                    search_type = '';
                                }
                                table_url(base_url + "admin/transactions/transfer_reportList/" + filter_by + search_type);
                            });
                        });
                    </script>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">

    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
