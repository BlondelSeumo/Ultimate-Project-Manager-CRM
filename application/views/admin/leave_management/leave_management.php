<?php include_once 'asset/admin-ajax.php';
$created = can_action('72', 'created');
$edited = can_action('72', 'edited');
$deleted = can_action('72', 'deleted');
$office_hours = config_item('office_hours');

?>
<?= message_box('success'); ?>
<?= message_box('error'); ?>
    <div class=" mt-lg">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="pending_approval <?= $active == 1 ? 'active' : '' ?>"><a href="#pending_approval"
                                                                                    data-toggle="tab"><?= lang('pending') . ' ' . lang('approval') ?></a>
                </li>

                <li class="my_leave <?= $active == 2 ? 'active' : '' ?>"><a href="#pending_approval"
                                                                            data-toggle="tab"><?= lang('my_leave') ?></a>
                </li>

                <?php if (!empty(admin_head())) { ?>
                    <li class="all_leave <?= $active == 3 ? 'active' : '' ?>"><a href="#pending_approval"
                                                                                 data-toggle="tab"><?= lang('all_leave') ?></a>
                    </li>
                <?php } ?>
                <li class="<?= $active == 4 ? 'active' : '' ?>"><a href="#leave_report"
                                                                   data-toggle="tab"><?= lang('leave_report') ?></a>
                </li>
                <li class="pull-right">
                    <a href="<?= base_url() ?>admin/leave_management/apply_leave"
                       class="bg-info"
                       data-toggle="modal" data-placement="top" data-target="#myModal_extra_lg">
                        <i class="fa fa-plus "></i> <?= lang('apply') . ' ' . lang('leave') ?>
                    </a>
                </li>
            </ul>
            <script type="text/javascript">
                $(document).ready(function () {
                    list = base_url + "admin/leave_management/pending_approvalList";
                    $('.pending_approval').on('click', function () {
                        table_url(base_url + "admin/leave_management/pending_approvalList");
                    });
                    $('.all_leave').on('click', function () {
                        table_url(base_url + "admin/leave_management/all_leaveList");
                    });
                    $('.my_leave').on('click', function () {
                        table_url(base_url + "admin/leave_management/my_leaveList");
                    });
                });
            </script>
            <div class="tab-content" style="border: 0;padding:0;">
                <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="pending_approval"
                     style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped pending_approval_" id="DataTables"
                                       cellspacing="0"
                                       width="100%">
                                    <thead>
                                    <tr>
                                        <th><?= lang('name') ?></th>
                                        <th><?= lang('leave_category') ?></th>
                                        <th><?= lang('date') ?></th>
                                        <th><?= lang('duration') ?></th>
                                        <th><?= lang('status') ?></th>
                                        <?php $show_custom_fields = custom_form_table(17, null);
                                        if (!empty($show_custom_fields)) {
                                            foreach ($show_custom_fields as $c_label => $v_fields) {
                                                if (!empty($c_label)) {
                                                    ?>
                                                    <th><?= $c_label ?> </th>
                                                <?php }
                                            }
                                        }
                                        ?>
                                        <?php if (!empty(admin_head())) { ?>
                                            <th class="col-sm-2"><?= lang('action') ?></th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="leave_report" style="position: relative;">
                    <div class="panel panel-custom">
                        <div class="panel-body">
                            <?php if ($this->session->userdata('user_type') == 1) { ?>
                                <div id="panelChart5">
                                    <div class="row panel-title pl-lg pb-sm"
                                         style="border-bottom: 1px solid #a0a6ad"><?= lang('all') . ' ' . lang('leave_report') ?></div>
                                    <div class="chart-pie flot-chart"></div>
                                </div>
                            <?php } ?>

                            <div id="panelChart5">
                                <div class="row panel-title pl-lg pb-sm"
                                     style="border-bottom: 1px solid #a0a6ad"><?= lang('my_leave') . ' ' . lang('report') ?></div>
                                <div class="chart-pie-my flot-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$my_atleast_one = get_row('tbl_leave_application', array('user_id' => my_id(), 'application_status' => 2));
$atleast_one = get_row('tbl_leave_application', array('application_status' => 2));

$all_category = $this->db->get('tbl_leave_category')->result();
$color = array('37bc9b', '7266ba', 'f05050', 'ff902b', '7266ba', 'f532e5', '5d9cec', '7cd600', '91ca00', 'ff7400', '1cc200', 'bb9000', '40c400');
if (!empty($all_category)) {
    ?>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.tooltip.min.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.resize.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.pie.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.time.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.categories.js"></script>
    <script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.spline.min.js"></script>
    <script type="text/javascript">

        // CHART PIE
        // -----------------------------------
        <?php
        if (!empty($atleast_one)) {
        if(!empty($leave_report)){
        ?>

        (function (window, document, $, undefined) {
            $(function () {
                var data = [
                    <?php
                    if(!empty($all_category)){
                    foreach ($all_category as $key => $v_category) {
                    if (!empty($leave_report['leave_taken'][$key])) {
                    $all_report = $leave_report['leave_taken'][$key];
                    ?>
                    {
                        "label": "<?= $v_category->leave_category . ' ( <small>' . lang('quota') . ': ' . $leave_report['leave_quota'][$key] . ' ' . lang('taken') . ': ' . $all_report . '</small>)'?>",
                        "color": "#<?=$color[$key] ?>",
                        "data": <?= $all_report ?>
                    },
                    <?php }
                    }
                    }?>
                ];
                var options = {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0,
                            label: {
                                show: true,
                                radius: 0.8,
                                formatter: function (label, series) {
                                    return '<div class="flot-pie-label">' +
                                            //label + ' : ' +
                                        Math.round(series.percent) +
                                        '%</div>';
                                },
                                background: {
                                    opacity: 0.8,
                                    color: '#222'
                                }
                            }
                        }
                    }
                };

                var chart = $('.chart-pie');
                if (chart.length)
                    $.plot(chart, data, options);

            });

        })(window, document, window.jQuery);
        <?php }
        }
        ?>

        <?php

        if(!empty($my_atleast_one) ){?>
        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {
                var data = [
                    <?php
                    if(!empty($all_category)){
                    foreach ($all_category as $key => $v_category) {
                    if (!empty($my_leave_report['leave_taken'][$key]) && $my_leave_report['leave_taken'][$key] != 0) {
                    $result = $my_leave_report['leave_taken'][$key];
                    ?>
                    {
                        "label": "<?= $v_category->leave_category . ' ( <small>' . lang('quota') . ': ' . $my_leave_report['leave_quota'][$key] . ' ' . lang('taken') . ': ' . $result . '</small>)'?>",
                        "color": "#<?=$color[$key] ?>",
                        "data": <?= $result ?>
                    },
                    <?php }
                    }
                    }?>
                ];

                var options = {
                    series: {
                        pie: {
                            show: true,
                            innerRadius: 0,
                            label: {
                                show: true,
                                radius: 0.8,
                                formatter: function (label, series) {
                                    return '<div class="flot-pie-label">' +
                                            //label + ' : ' +
                                        Math.round(series.percent) +
                                        '%</div>';
                                },
                                background: {
                                    opacity: 0.8,
                                    color: '#222'
                                }
                            }
                        }
                    }
                };
                var chart = $('.chart-pie-my');
                if (chart.length)
                    $.plot(chart, data, options);
            });
        })(window, document, window.jQuery);

        <?php }?>
    </script>
<?php } ?>