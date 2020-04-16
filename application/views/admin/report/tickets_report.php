<!-- START row-->

<div class="row">

    <div class="col-md-6">
        <div id="panelChart5" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('tickets') . ' ' . lang('report') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-pie flot-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div id="panelChart4" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('tickets') . ' ' . lang('report') . ' ' . date('Y') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-line flot-chart"></div>
            </div>
        </div>
    </div>
    <!-- END row-->

    <div class="col-lg-12">
        <div class="panel panel-custom">
            <div class="panel-heading"><?= lang('tickets_r_assignment') ?></div>
            <div class="panel-body">
                <div id="morris-bar"></div>
            </div>
        </div>
    </div>
</div>

<!-- END row-->

<?php
$answered = 0;
$closed = 0;
$open = 0;
$in_progress = 0;

$tickets_info = $this->report_model->get_permission('tbl_tickets');

if (!empty($tickets_info)):foreach ($tickets_info as $v_tickets):
    if ($v_tickets->status == 'answered') {
        $answered += count($v_tickets->status);
    }
    if ($v_tickets->status == 'closed') {
        $closed += count($v_tickets->status);
    }
    if ($v_tickets->status == 'open') {
        $open += count($v_tickets->status);
    }
    if ($v_tickets->status == 'in_progress') {
        $in_progress += count($v_tickets->status);
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
<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        var chartdata = [
                <?php if(!empty($user_tickets)):foreach($user_tickets as $user => $v_ticket_user):
                if($user != 'all'){
                if(!empty($assign_user)){foreach($assign_user as $v_user){
                if($v_user->user_id == $user){
                ?>
                {
                    y: "<?= $v_user->username?>",
                    <?php
                    $aanswered = 0;
                    $inparogress = 0;
                    $aopen = 0;
                    $aclosed = 0;
                    foreach ($v_ticket_user as $status => $value) {
                        if ($status == 'answered') {
                            $aanswered = count($value);
                        } elseif ($status == 'in_progress') {
                            $inparogress = count($value);
                        } elseif ($status == 'open') {
                            $aopen = count($value);
                        } elseif ($status == 'closed') {
                            $aclosed = count($value);
                        }
                    }
                    ?>
                    a:<?= $aanswered;?> ,
                    b: <?= $inparogress?>, c: <?= $aopen?>, d: <?= $aclosed?>},
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
        <?php
        if(!empty($value)){?>
        new Morris.Bar({
            element: 'morris-bar',
            data: chartdata,
            xkey: 'y',
            ykeys: ["a", "b", "c", 'd'],
            labels: ["<?= lang('answered')?>", "<?= lang('in_progress')?>", "<?= lang('open')?>", "<?= lang('closed')?>"],
            xLabelMargin: 2,
            barColors: ['#ff902b', '#5d9cec', '#27c24c', '#7266ba'],
            resize: true,
            parseTime: false,
        });
        <?php }?>

        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {

                var data = [{
                    "label": "<?= lang('answered')?>",
                    "color": "#ff902b",
                    "data": <?= $answered?>
                }, {
                    "label": "<?= lang('in_progress')?>",
                    "color": "#5d9cec",
                    "data": <?= $in_progress?>
                }, {
                    "label": "<?= lang('open')?>",
                    "color": "#23b7e5",
                    "data": <?= $open?>
                }, {
                    "label": "<?= lang('closed')?>",
                    "color": "#7266ba",
                    "data": <?= $closed?>
                }];

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
                    "label": "<?= lang('answered')?>",
                    "color": "#ff902b",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->status == 'answered')
                                    $y_not_started += count($s_report->status);
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
                                if ($s_report->status == 'in_progress')
                                    $y_not_started += count($s_report->status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]

                }, {
                    "label": "<?= lang('open')?>",
                    "color": "#23b7e5",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->status == 'open')
                                    $y_not_started += count($s_report->status);
                            }
                            echo $y_not_started; // view the total report in a  month
                            ?>],
                        <?php endforeach; ?>
                    ]
                }, {
                    "label": "<?= lang('closed')?>",
                    "color": "#7266ba",
                    "data": [
                        <?php foreach ($yearly_report as $name => $v_report):
                        $month_name = date('M', strtotime(date('Y') . '-' . $name)); // get full name of month by date query
                        ?>
                        ["<?= $month_name?>", <?php
                            $y_not_started = 0;
                            foreach ($v_report as $s_report) {
                                if ($s_report->status == 'closed')
                                    $y_not_started += count($s_report->status);
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