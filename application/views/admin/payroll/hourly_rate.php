<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('95', 'created');
$edited = can_action('95', 'edited');
$deleted = can_action('95', 'deleted');
if (!empty($created) || !empty($edited)){
?>
<div class="nav-tabs-custom">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs">
        <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                            data-toggle="tab"><?= lang('hourly_rate_list') ?></a>
        </li>
        <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                            data-toggle="tab"><?= lang('set_hourly_grade') ?></a></li>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
            <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('hourly_rate_list') ?></strong></div>
                </header>
                <?php } ?>
                <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th class="col-sm-1"><?= lang('sl') ?></th>
                        <th><?= lang('hourly_grade') ?></th>
                        <th><?= lang('hourly_rates') ?></th>
                        <?php if (!empty($edited) || !empty($deleted)) { ?>
                            <th class="col-sm-2"><?= lang('action') ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            list = base_url + "admin/payroll/hourly_rateList";
                        });
                    </script>

                    </tbody>
                </table>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
                <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                    <form data-parsley-validate="" novalidate="" role="form" enctype="multipart/form-data"
                          action="<?php echo base_url() ?>admin/payroll/set_hourly_rate/<?php
                          if (!empty($hourly_rate->hourly_rate_id)) {
                              echo $hourly_rate->hourly_rate_id;
                          }
                          ?>" method="post" class="form-horizontal form-groups-bordered">
                        <div class="row">
                            <div class="col-sm-12 form-groups-bordered">
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('hourly_grade') ?><span
                                            class="required"> *</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" name="hourly_grade" value="<?php
                                        if (!empty($hourly_rate->hourly_grade)) {
                                            echo $hourly_rate->hourly_grade;
                                        }
                                        ?>" class="form-control" required
                                               placeholder="<?= lang('enter') . ' ' . lang('hourly_grade') ?>">
                                    </div>
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-3 control-label"><?= lang('hourly_rates') ?><span
                                            class="required"> *</span></label>
                                    <div class="col-sm-5">
                                        <input type="text" data-parsley-type="number" name="hourly_rate" value="<?php
                                        if (!empty($hourly_rate->hourly_rate)) {
                                            echo $hourly_rate->hourly_rate;
                                        }
                                        ?>" class="salary form-control" required
                                               placeholder="<?= lang('enter') . ' ' . lang('hourly_rates') ?>">
                                    </div>
                                </div>

                                <div class="btn-bottom-toolbar text-right">
                                    <?php
                                    if (!empty($hourly_rate)) { ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                        <button type="button" onclick="goBack()"
                                                class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                    <?php } else {
                                        ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                    <?php }
                                    ?>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            <?php } else { ?>
        </div>
        <?php } ?>
    </div>
</div>



