<div class="panel panel-custom">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('leave') . ' ' . lang('details_of') . ' ' . $profile_info->fullname ?></strong>
        </div>
    </div>
    <table class="table">
        <tbody>
        <?php
        $total_taken = 0;
        $total_quota = 0;
        $leave_report = leave_report($profile_info->user_id);

        if (!empty($leave_report['leave_category'])) {
            foreach ($leave_report['leave_category'] as $lkey => $v_l_report) {
                $total_quota += $leave_report['leave_quota'][$lkey];
                $total_taken += $leave_report['leave_taken'][$lkey];
                ?>
                <tr>
                    <td><strong> <?= $leave_report['leave_category'][$lkey] ?></strong>:</td>
                    <td>
                        <?= $leave_report['leave_taken'][$lkey] ?>/<?= $leave_report['leave_quota'][$lkey]; ?> </td>
                </tr>
            <?php }
        }
        ?>

        <tr>
            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;">
                <strong> <?= lang('total') ?></strong>:
            </td>
            <td style="background-color: #e8e8e8; font-size: 14px; font-weight: bold;"> <?= $total_taken; ?>
                /<?= $total_quota; ?> </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="panel panel-custom">
    <div class="panel-heading"><?= lang('leave_report') ?></div>
    <div class="panel-body">
        <div id="panelChart5">
            <div class="chart-pie-my flot-chart"></div>
        </div>
    </div>
</div>
<?php
$all_category = $this->db->get('tbl_leave_category')->result();
$color = array('37bc9b', '7266ba', 'f05050', 'ff902b', '7266ba', 'f532e5', '5d9cec', '7cd600', '91ca00', 'ff7400', '1cc200', 'bb9000', '40c400');
foreach ($all_category as $key => $v_category) {
    if (!empty($my_leave_report['leave_taken'][$key])) {
        $a = $my_leave_report['leave_taken'][$key];
    }
}
if (!empty($a)) {
    ?>
    <script type="text/javascript">
        // CHART PIE
        // -----------------------------------
        (function (window, document, $, undefined) {

            $(function () {

                var data = [
                    <?php
                    if(!empty($all_category)){
                    foreach ($all_category as $key => $v_category) {
                    if (!empty($my_leave_report['leave_taken'][$key])) {
                    $result = $my_leave_report['leave_taken'][$key];
                    ?>
                    {
                        "label": "<?= $v_category->leave_category . ' ( <small>' . lang('quota') . ': ' . $my_leave_report['leave_quota'][$key] . ' ' . lang('taken') . ': ' . $result . '</small>)'?>",
                        "color": "#<?=$color[$key] ?>",
                        "data": <?= $result?>
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

    </script>
<?php } ?>
