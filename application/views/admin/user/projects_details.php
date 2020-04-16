<div id="panelChart4" class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('total') . ' ' . lang('project') . ' ' . lang('time_spent') ?></div>
    </div>
    <div class="panel-body">
        <?php
        $project_info = $this->user_model->my_permission('tbl_project', $profile_info->user_id);
        $project_time = 0;
        $project_time = $this->user_model->my_spent_time($profile_info->user_id, true);
        if (!empty($project_info)) {
            foreach ($project_info as $v_projects) {
            }
        }
        echo $this->user_model->get_time_spent_result($project_time)

        ?>
    </div>
</div>
<div id="panelChart5" class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('project') . ' ' . lang('report') ?></div>
    </div>
    <div class="panel-body">
        <div class="project-chart-pie flot-chart"></div>
    </div>
</div>            

<?php

$started = 0;
$in_progress = 0;
$cancel = 0;
$completed = 0;
if (!empty($project_info)):
    foreach ($project_info as $v_project) :
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
<?php if (!empty($started) || !empty($in_progress) || !empty($completed) || !empty($cancel)) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
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

                    var chart = $('.project-chart-pie');
                    if (chart.length)
                        $.plot(chart, data, options);

                });

            })(window, document, window.jQuery);
            // CHART BAR STACKED
            // -----------------------------------


        });

    </script>
<?php } ?>