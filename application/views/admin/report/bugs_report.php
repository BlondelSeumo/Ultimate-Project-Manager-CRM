<!-- START row-->
<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<div class="row">

    <div class="col-md-6">
        <div id="panelChart5" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('bugs') . ' ' . lang('report') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-pie flot-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div id="panelChart4" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('bugs') . ' ' . lang('report') . ' ' . date('Y') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-line flot-chart"></div>
            </div>
        </div>
    </div>
    <!-- END row-->

    <div class="col-lg-12">
        <div class="panel panel-custom">
            <div class="panel-heading"><?= lang('bugs_r_assignment') ?></div>
            <div class="panel-body">
                <div id="morris-bar"></div>
            </div>
        </div>
    </div>
</div>

<!-- END row-->

<?php
$unconfirmed = 0;
$in_progress = 0;
$confirmed = 0;
$resolved = 0;
$verified = 0;

$bugs_info =$this->report_model->get_permission('tbl_bug');

if (!empty($bugs_info)):foreach ($bugs_info as $v_bugs):
    if ($v_bugs->bug_status == 'unconfirmed') {
        $unconfirmed += count($v_bugs->bug_status);
    }
    if ($v_bugs->bug_status == 'in_progress') {
        $in_progress += count($v_bugs->bug_status);
    }
    if ($v_bugs->bug_status == 'confirmed') {
        $confirmed += count($v_bugs->bug_status);
    }
    if ($v_bugs->bug_status == 'resolved') {
        $resolved += count($v_bugs->bug_status);
    }
    if ($v_bugs->bug_status == 'verified') {
        $verified += count($v_bugs->bug_status);
    }
endforeach;
endif;
?>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.tooltip.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.resize.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.pie.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.time.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.categories.js"></script>
<script src="<?= base_url() ?>assets/plugins/Flot/jquery.flot.spline.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var chartdata = [
                <?php if(!empty($user_bugs)):foreach($user_bugs as $user => $v_bugs_user):
                if($user != 'all'){
                if(!empty($assign_user)){foreach($assign_user as $v_user){
                if($v_user->user_id == $user){
                ?>
                {
                    y: "<?= $v_user->username?>",
                    <?php
                    $aunconfirmed = 0;
                    $inparogress = 0;
                    $averified = 0;
                    $aresolved = 0;
                    foreach ($v_bugs_user as $status => $value) {
                        if ($status == 'unconfirmed') {
                            $aunconfirmed = count($value);
                        } elseif ($status == 'in_progress') {
                            $inparogress = count($value);
                        } elseif ($status == 'verified') {
                            $averified = count($value);
                        } elseif ($status == 'resolved') {
                            $aresolved = count($value);
                        }
                    }
                    ?>
                    a:<?= $aunconfirmed;?> ,

                    b: <?= $inparogress?>, c: <?= $averified?>, d: <?= $aresolved?>},
                <?php
                }
                }
                };
                }
                endforeach;
                endif;

                ?>
            ]
            ;
        new Morris.Bar({
            element: 'morris-bar',
            data: chartdata,
            xkey: 'y',
            ykeys: ["a", "b", "c", 'd'],
            labels: ["<?= lang('unconfirmed')?>", "<?= lang('in_progress')?>", "<?= lang('verified')?>", "<?= lang('resolved')?>"],
            xLabelMargin: 2,
            barColors: ['#ff902b', '#5d9cec', '#27c24c', '#7266ba'],
            resize: true,
            parseTime: false,
        });

        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {

                var data = [{
                    "label": "<?= lang('unconfirmed')?>",
                    "color": "#ff902b",
                    "data": <?= $unconfirmed?>
                }, {
                    "label": "<?= lang('in_progress')?>",
                    "color": "#5d9cec",
                    "data": <?= $in_progress?>
                }, {
                    "label": "<?= lang('confirmed')?>",
                    "color": "#23b7e5",
                    "data": <?= $confirmed?>
                }, {
                    "label": "<?= lang('resolved')?>",
                    "color": "#7266ba",
                    "data": <?= $resolved?>
                }, {
                    "label": "<?= lang('verified')?>",
                    "color": "#27c24c",
                    "data": <?= $verified?>
                },];

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
        // CHART BAR STACKED
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {

                var data = [{
                    "label": "<?= lang('unconfirmed')?>",
                    "color": "#ff902b",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->bug_status == 'unconfirmed')
                                    $y_not_started += count($s_report->bug_status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]
                }, {
                    "label": "<?= lang('in_progress')?>",
                    "color": "#5d9cec",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->bug_status == 'in_progress')
                                    $y_not_started += count($s_report->bug_status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]

                }, {
                    "label": "<?= lang('confirmed')?>",
                    "color": "#23b7e5",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->bug_status == 'confirmed')
                                    $y_not_started += count($s_report->bug_status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]
                }, {
                    "label": "<?= lang('resolved')?>",
                    "color": "#7266ba",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->bug_status == 'resolved')
                                    $y_not_started += count($s_report->bug_status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]
                }, {
                    "label": "<?= lang('verified')?>",
                    "color": "#27c24c",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->bug_status == 'verified')
                                    $y_not_started += count($s_report->bug_status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]
                }];

                var options = {
                    series: {
                        lines: {
                            show: true,
                            fill: 0.01
                        },
                        points: {
                            show: true,
                            radius: 4
                        }
                    },
                    grid: {
                        borderColor: '#eee',
                        borderWidth: 1,
                        hoverable: true,
                        backgroundColor: '#fcfcfc'
                    },
                    tooltip: true,
                    tooltipOpts: {
                        content: function (label, x, y) {
                            return x + ' : ' + y;
                        }
                    },
                    xaxis: {
                        tickColor: '#eee',
                        mode: 'categories'
                    },
                    yaxis: {
                        // position: 'right' or 'left'
                        tickColor: '#eee'
                    },
                    shadowSize: 0
                };

                var chart = $('.chart-line');
                if (chart.length)
                    $.plot(chart, data, options);

            });

        })(window, document, window.jQuery);

    });

</script>