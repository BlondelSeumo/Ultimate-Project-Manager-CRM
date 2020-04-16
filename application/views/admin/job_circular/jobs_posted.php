<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<?php
$created = can_action('103', 'created');
$edited = can_action('103', 'edited');
$deleted = can_action('103', 'deleted');
?>
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('job_posted_list') ?></strong>
            <?php if (!empty($created)) { ?>
                <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                    <a href="<?= base_url() ?>admin/job_circular/new_jobs_posted" class="btn btn-xs btn-info"
                       data-toggle="modal"
                       data-placement="top" data-target="#myModal_lg">
                        <i class="fa fa-plus "></i> <?= ' ' . lang('new') . ' ' . lang('jobs_posted') ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('job_title') ?></th>
                <th><?= lang('designation') ?></th>
                <th><?= lang('vacancy_no') ?></th>
                <th><?= lang('last_date') ?></th>
                <?php $show_custom_fields = custom_form_table(14, null);
                if (!empty($show_custom_fields)) {
                    foreach ($show_custom_fields as $c_label => $v_fields) {
                        if (!empty($c_label)) {
                            ?>
                            <th><?= $c_label ?> </th>
                        <?php }
                    }
                }
                ?>
                <th><?= lang('status') ?></th>
                <th><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                $(document).ready(function () {
                    list = base_url + "admin/job_circular/jobs_postedList";
                });
            </script>


            </tbody>
        </table>
    </div>
</div>