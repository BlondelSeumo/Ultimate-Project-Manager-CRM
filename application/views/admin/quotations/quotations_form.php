<link href="<?= base_url() ?>plugins/formbuilder/formbuilder.css" rel="stylesheet"/>
<style>


    .fb-main {
        background-color: #fff;
        min-height: 600px;
    }

    input[type=text] {
        height: 26px;
        margin-bottom: 3px;
    }


</style>
<style>
    /*Hide Auto-save button*/
    .fb-save-wrapper .js-save-form{
        display:none;
    }
</style>
<?= message_box('success'); ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('quotations_form') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new" data-toggle="tab"><?= lang('new_quotations_form') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><?= lang('title') ?></th>
                        <th><?= lang('created_by') ?></th>
                        <th><?= lang('created_date') ?></th>
                        <th><?= lang('status') ?></th>
                        <th ><?= lang('action') ?></th>
                    </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/quotations/quotationsformList";
                    });
                </script>
                </tbody>
            </table>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form  data-parsley-validate="" novalidate="" class="form-horizontal" action="<?= base_url() ?>admin/quotations/add_form" method="post" id="addQuotationForm">
                <!-- Sidebar ends -->
                <!-- Main bar -->
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" required="" style="height: 31px;margin-bottom: 10px;" class="form-control" name="quotationforms_title" autocomplete="off" placeholder="<?= lang('form_title') ?>">
                    </div>
                </div>
                <!--WI_QUOTATION_TITLE-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary" >
                            <div class="fb-main"></div>
                        </div>
                        <div class="panel-footer">
                            <div class="pull-right">
                                <input type="hidden" name="quotationforms_code" id="quotationforms_code">
                                <input class="btn btn-primary" type="submit" value="<?= lang('save') ?>" id="" name="submit">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </form>
            <script src="<?= base_url() ?>asset/vendor/js/vendor.js"></script>
            <script src="<?= base_url() ?>plugins/formbuilder/formbuilder.js"></script>

            <script>
                $(function() {
                    fb = new Formbuilder({
                        selector: '.fb-main',
                        bootstrapData: [
                            {}
                        ]
                    });

                    fb.on('save', function(payload) {
                        // console.log(payload);
                        $('#quotationforms_code').val(payload);
                    })
                });
            </script>
            <!-- Mainbar ends -->
            <div class="clearfix"></div>
        </div>

    </div>
</div>