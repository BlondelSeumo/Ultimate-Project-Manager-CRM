<?= message_box('success') ?>
<?= message_box('error') ?>

<?php
$edited = can_action('152', 'edited');
$deleted = can_action('152', 'deleted');
?>

<section class="panel panel-custom ">
    <header class="panel-heading"><?= lang('all_payments') ?></header>
    <div class="panel-body">
        <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th><?= lang('payment_date') ?></th>
                <th><?= lang('return_stock') . ' ' . lang('date') ?></th>
                <th><?= lang('return_stock') ?></th>
                <th><?= lang('supplier') ?></th>
                <th><?= lang('amount') ?></th>
                <th><?= lang('payment_method') ?></th>
                <?php if (!empty($edited) || !empty($deleted)) { ?>
                    <th class="hidden-print"><?= lang('action') ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                list = base_url + "admin/return_stock/paymentList";
            </script>
            </tbody>
        </table>
    </div>
</section>