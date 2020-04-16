<div class="panel panel-custom">
    <header class="panel-heading ">
        <div class="panel-title"><strong><?= lang('all') . ' ' . lang('return_stock') ?></strong>
            <a class="btn btn-success btn-xs pull-right mr" data-toggle="modal" data-target="#myModal"
               href="<?= base_url('client/invoice/refund_items') ?>"><?= lang('refund_items') ?></a>
        </div>
    </header>
    <div class="table-responsive">
        <table class="table table-striped DataTables " id="DataTables">
            <thead>
            <tr>
                <th><?= lang('reference_no') ?></th>
                <th><?= lang('return_stock_date') ?></th>
                <th><?= lang('due_amount') ?></th>
                <th><?= lang('status') ?></th>
                <th class="col-options no-sort"><?= lang('action') ?></th>
            </tr>
            </thead>
            <tbody>
            <script type="text/javascript">
                list = base_url + "client/invoice/RefundList";
            </script>
            </tbody>
        </table>
    </div>
</div>