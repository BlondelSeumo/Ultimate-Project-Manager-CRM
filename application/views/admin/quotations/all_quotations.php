<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <div class="panel-title">
                    <strong><?= lang('quotations') ?></strong>
                </div>
            </div>
            <br/>
            <div class="panel-body">
                <div class="">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th><?= lang('title') ?></th>
                            <th><?= lang('client') ?></th>
                            <th><?= lang('date') ?></th>
                            <th><?= lang('amount') ?></th>
                            <th><?= lang('status') ?></th>
                            <th><?= lang('generated_by') ?></th>
                            <th><?= lang('action') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/quotations/quotationsList";
                            });
                        </script>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>