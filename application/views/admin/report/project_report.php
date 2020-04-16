<!-- START row-->
<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<div class="row">

    <div class="col-md-6">
        <div id="panelChart5" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('project') . ' ' . lang('report') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-pie flot-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div id="panelChart4" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('total') . ' ' . lang('project') . ' ' . lang('time_spent') ?></div>
            </div>
            <div class="panel-body">
                <?php
                $project_info = $this->report_model->get_permission('tbl_project');
                $project_time = 0;
                if (!empty($project_info)) {
                    foreach ($project_info as $v_projects) {
                        $project_time += $this->report_model->task_spent_time_by_id($v_projects->project_id, true);
                    }
                }
                echo $this->report_model->get_time_spent_result($project_time)

                ?>
            </div>
        </div>
    </div>
    <!-- END row-->

    <div class="col-lg-12">
        <div class="panel panel-custom">
            <div class="panel-heading"><?= lang('project_assignment') ?></div>
            <div class="panel-body">
                <div id="morris-bar"></div>
            </div>
        </div>
    </div>
</div>

<!-- END row-->

<?php
$started = 0;
$in_progress = 0;
$cancel = 0;
$completed = 0;
if (!empty($all_project)):
    foreach ($all_project as $v_project) :
        if ($v_project->project_status == 'started') {
            $started += count($v_project->project_status);
        }
        if ($v_project->project_status == 'in_progress') {
            $in_progress += count($v_project->project_status);
        }
        if ($v_project->project_status == 'completed') {
            $completed += count($v_project->project_status);
        }
        if ($v_project->project_status == 'cancel') {
            $cancel += count($v_project->project_status);
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
                <?php if(!empty($user_project)):foreach($user_project as $user => $v_project_user):
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
                    foreach ($v_project_user as $status => $value) {
                        if ($status == 'started') {
                            $aunconfirmed = count($value);
                        } elseif ($status == 'in_progress') {
                            $inparogress = count($value);
                        } elseif ($status == 'cancel') {
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
            ykeys: ["a", "b", "c"],
            labels: ["<?= lang('started')?>", "<?= lang('in_progress')?>", "<?= lang('cancel')?>"],
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
                    "label": "<?= lang('started')?>",
                    "color": "#ff902b",
                    "data": <?= $started?>
                }, {
                    "label": "<?= lang('in_progress')?>",
                    "color": "#5d9cec",
                    "data": <?= $in_progress?>
                }, {
                    "label": "<?= lang('completed')?>",
                    "color": "#23b7e5",
                    "data": <?= $completed?>
                }, {
                    "label": "<?= lang('cancel')?>",
                    "color": "#7266ba",
                    "data": <?= $cancel?>
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


    });

</script>