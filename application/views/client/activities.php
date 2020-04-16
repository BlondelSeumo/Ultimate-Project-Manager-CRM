<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('all_activities'); ?>
            <a onclick="return confirm('<?= lang('delete_alert') ?>')"
               href="<?= base_url() ?>client/settings/clear_activities"
               class="btn btn-xs btn-primary pull-right"><?= lang('clear') ?></a>
        </div>
    </div>

    <div class="">
        <table class="table table-striped" id="Transation_DataTables">
            <thead>
            <tr>
                <th class="col-xs-2"><?= lang('activity_date') ?></th>
                <th class="col-xs-3"><?= lang('user') ?></th>
                <th class="col-xs-1"><?= lang('module') ?></th>

                <th><?= lang('activity') ?></th>

            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($activities_info)) {
                foreach ($activities_info as $v_activity) {
                    ?>
                    <tr>
                        <td><?= display_datetime($v_activity->activity_date); ?></td>
                        <td><?= $this->db->where('user_id', $v_activity->user)->get('tbl_account_details')->row()->fullname; ?></td>
                        <td><?= $v_activity->module ?></td>
                        <td>
                            <?= lang($v_activity->activity) ?>
                            <strong> <?= $v_activity->value1 . ' ' . $v_activity->value2 ?></strong></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#Transation_DataTables').dataTable({
            paging: false
        });
    });
</script>
<!-- end -->