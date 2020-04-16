<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('all_timechange_request') ?></strong>

            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                <a href="<?= base_url() ?>admin/attendance/add_time_manually" class="btn btn-xs btn-info"
                   data-toggle="modal"
                   data-placement="top" data-target="#myModal">
                    <i class="fa fa-plus "></i> <?= ' ' . lang('add') ?></a>
            </div>
        </div>
    </div>
    <!-- Table -->

    <table class="table table-striped " id="DataTables">
        <thead>
        <tr>
            <th><?= lang('emp_id') ?></th>
            <th><?= lang('name') ?></th>
            <th><?= lang('time_in') ?></th>
            <th><?= lang('time_out') ?></th>
            <th><?= lang('status') ?></th>
            <th><?= lang('action') ?></th>
        </tr>
        </thead>
        <tbody>

        <script type="text/javascript">
            $(document).ready(function () {
                list = base_url + "admin/attendance/timechange_requestList";
                $('.dropdown-toggle').on('click', function () {
                    if ($('.group').css('display') == 'block') {
                        $('.group').css('display', 'none');
                    } else {
                        $('.group').css('display', 'block')
                    }
                });
                $('.filter_by').on('click', function () {
                    $('.filter_by').removeClass('active');
                    $('.group').css('display', 'block');
                    $(this).addClass('active');
                    var filter_by = $(this).attr('id');
                    if (filter_by) {
                        filter_by = filter_by;
                    } else {
                        filter_by = '';
                    }
                    table_url(base_url + "admin/attendance/timechange_requestList/" + filter_by);
                });
            });
        </script>
        </tbody>
    </table>
</div>
