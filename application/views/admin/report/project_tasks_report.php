<!-- START row-->
<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<div class="row">
    <div class="col-md-6">
        <div id="panelChart5" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('task') . ' ' . lang('report') ?></div>
            </div>
            <div class="panel-body">
                <div class="chart-pie flot-chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">

        <div id="panelChart4" class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('total') . ' ' . lang('task') . ' ' . lang('time_spent') ?></div>
            </div>
            <div class="panel-body">
                <div class="form-group col-sm-12">
                    <?php
                    $tasks_info = $this->report_model->get_permission('tbl_task');
                    $task_time = 0;
                    if (!empty($tasks_info)) {
                        foreach ($tasks_info as $v_tasks) {
                            if (!empty($v_tasks->project_id)) {
                                $task_time += $this->report_model->task_spent_time_by_id($v_tasks->task_id);

                            }
                        }
                    }
                    echo $this->report_model->get_time_spent_result($task_time)

                    ?>

                </div>
            </div>
        </div>
    </div>
    <!-- END row-->

    <div class="col-lg-12">
        <div class="panel panel-custom">
            <div class="panel-heading"><?= lang('tasks_r_assignment') ?></div>
            <div class="panel-body">
                <div id="morris-bar"></div>
            </div>
        </div>
    </div>
</div>

<!-- END row-->

<?php
$not_started = 0;
$in_progress = 0;
$completed = 0;
$deferred = 0;
$waiting_for_someone = 0;

if (!empty($all_project)):
    foreach ($all_project as $v_project) :
        $tasks_info = $this->db->where('project_id', $v_project->project_id)->get('tbl_task')->result();
        if (!empty($tasks_info)):foreach ($tasks_info as $v_tasks):
            if ($v_tasks->task_status == 'not_started') {
                $not_started += count($v_tasks->task_status);
            }
            if ($v_tasks->task_status == 'in_progress') {
                $in_progress += count($v_tasks->task_status);
            }
            if ($v_tasks->task_status == 'completed') {
                $completed += count($v_tasks->task_status);
            }
            if ($v_tasks->task_status == 'deferred') {
                $deferred += count($v_tasks->task_status);
            }
            if ($v_tasks->task_status == 'waiting_for_someone') {
                $waiting_for_someone += count($v_tasks->task_status);
            }
        endforeach;
        endif;
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
                <?php if(!empty($user_tasks)):foreach($user_tasks as $user => $v_task_user):
                if($user != 'all'){
                if(!empty($assign_user)){foreach($assign_user as $v_user){
                if($v_user->user_id == $user){
                ?>
                {
                    y: "<?= $v_user->username?>",
                    <?php
                    $inparogress = 0;
                    $notstarted = 0;
                    $deferred = 0;
                    foreach ($v_task_user as $status => $value) {
                        if ($status == 'not_started') {
                            $notstarted = count($value);
                        } elseif ($status == 'in_progress') {
                            $inparogress = count($value);
                        } elseif ($status == 'deferred') {
                            $deferred = count($value);
                        }
                    }
                    ?>
                    a:<?= $notstarted;?> ,

                    b: <?= $inparogress?>, c: <?= $deferred?>},
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
            labels: ["<?= lang('not_started')?>", "<?= lang('in_progress')?>", "<?= lang('deferred')?>"],
            xLabelMargin: 2,
            barColors: ['#23b7e5', '#ff902b', '#f05050'],
            resize: true,
            parseTime: false,
        });

        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {

                var data = [{
                    "label": "<?= lang('not_started')?>",
                    "color": "#23b7e5",
                    "data": <?= $not_started?>
                }, {
                    "label": "<?= lang('in_progress')?>",
                    "color": "#ff902b",
                    "data": <?= $in_progress?>
                }, {
                    "label": "<?= lang('completed')?>",
                    "color": "#27c24c",
                    "data": <?= $completed?>
                }, {
                    "label": "<?= lang('deferred')?>",
                    "color": "#f05050",
                    "data": <?= $deferred?>
                }, {
                    "label": "<?= lang('waiting_for_someone')?>",
                    "color": "#ff902b",
                    "data": <?= $waiting_for_someone?>
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

    });

</script>