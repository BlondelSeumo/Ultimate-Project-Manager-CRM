<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('employee_salary_details') ?></strong>
        </div>
    </div>

    <!-- Table -->
    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th class="col-sm-1"><?= lang('emp_id') ?></th>
            <th><?= lang('name') ?></th>
            <th><?= lang('salary_type') ?></th>
            <th><?= lang('basic_salary') ?></th>
            <th><?= lang('overtime') ?>
                <small>(<?= lang('per_hour') ?>)</small>
            </th>
            <th><?= lang('action') ?></th>

        </tr>
        </thead>
        <tbody>

        <script type="text/javascript">
            $(document).ready(function () {
                list = base_url + "admin/payroll/employee_salaryList";
            });
        </script>
        </tbody>
    </table>
</div>
