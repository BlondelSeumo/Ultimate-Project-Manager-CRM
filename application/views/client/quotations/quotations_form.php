<link href="<?= base_url() ?>plugins/formbuilder/formbuilder.css" rel="stylesheet"/>
<style>


    .fb-main {
        background-color: #fff;
        border-radius: 5px;
        min-height: 600px;
    }

    input[type=text] {
        height: 26px;
        margin-bottom: 3px;
    }

    select {
        margin-bottom: 5px;
        font-size: 40px;
    }
</style>
<style>
    /*Hide Auto-save button*/
    .fb-save-wrapper .js-save-form {
        display: none;
    }
</style>
<?= message_box('success'); ?>

<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('quotations') ?></a></li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#new"
                                                            data-toggle="tab"><?= lang('request_quotations') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <table class="table table-striped table-responsive DataTables">
                <thead>
                <tr>
                    <th><?= lang('title') ?></th>
                    <th><?= lang('client') ?></th>
                    <th><?= lang('date') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('status') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($all_quatations)) {
                    foreach ($all_quatations as $v_quatations) {
                        ?>
                        <tr>
                            <?php
                            $client_info = $this->quotations_model->check_by(array('client_id' => $v_quatations->client_id), 'tbl_client');
                            $user_info = $this->quotations_model->check_by(array('user_id' => $v_quatations->user_id), 'tbl_users');
                            if ($user_info->role_id == 1) {
                                $user = '(' . lang('admin') . ')';
                            } elseif ($user_info->role_id == 2) {
                                $user = '(' . lang('client') . ')';
                            } else {
                                $user = '(' . lang('staff') . ')';
                            }
                            $currency = $this->quotations_model->client_currency_symbol($v_quatations->client_id);
                            if (!empty($client_info)) {
                                if ($client_info->client_status == 1) {
                                    $client_status = lang('person');
                                } else {
                                    $client_status = lang('company');
                                }
                            } else {
                                $client_status = '';
                            }
                            ?>
                            <td>
                                <a href="<?= base_url() ?>client/quotations/quotations_details/<?= $v_quatations->quotations_id ?>"><?= $v_quatations->quotations_form_title; ?></a>
                            </td>
                            <td><?= $v_quatations->name . ' ' . $client_status; ?></td>
                            <td><?= strftime(config_item('date_format'), strtotime($v_quatations->quotations_date)) ?></td>
                            <td>
                                <?php
                                if (!empty($v_quatations->quotations_amount)) {
                                    echo display_money($v_quatations->quotations_amount, $currency->symbol);
                                }
                                ?>
                            </td>
                            <td><?php
                                if ($v_quatations->quotations_status == 'completed') {
                                    echo '<span class="label label-success">' . lang('completed') . '</span>';
                                } else {
                                    echo '<span class="label label-danger">' . lang('pending') . '</span>';
                                };
                                ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="new">
            <form class="form-horizontal" action="<?= base_url() ?>client//quotations/add_form" method="post"
                  id="addQuotationForm">
                <!-- Sidebar ends -->
                <!-- Main bar -->
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="quotationforms_title" autocomplete="off"
                               placeholder="<?= lang('form_title') ?>">
                    </div>
                </div>
                <!--WI_QUOTATION_TITLE-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="fb-main"></div>
                        </div>
                        <div class="box-footer">
                            <div class="pull-right">
                                <input type="hidden" name="quotationforms_code" id="quotationforms_code">
                                <input class="btn btn-primary" type="submit" value="<?= lang('save') ?>" id=""
                                       name="submit">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </form>
            <script src="<?= base_url() ?>asset/vendor/js/vendor.js"></script>
            <script src="<?= base_url() ?>plugins/formbuilder/formbuilder.js"></script>

            <script>
                $(function () {
                    fb = new Formbuilder({
                        selector: '.fb-main',
                        bootstrapData: [
                            {}
                        ]
                    });

                    fb.on('save', function (payload) {
                        console.log(payload);
                        $('#quotationforms_code').val(payload);
                    })
                });
            </script>
            <!-- Mainbar ends -->
            <div class="clearfix"></div>
        </div>

    </div>
</div>