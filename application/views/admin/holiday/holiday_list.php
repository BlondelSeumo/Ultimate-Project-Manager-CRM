<?php include_once 'asset/admin-ajax.php'; ?>
<?php echo message_box('success'); ?>
<?php echo message_box('error');
$created = can_action('71', 'created');
$edited = can_action('71', 'edited');
$deleted = can_action('71', 'deleted');
?>
<div class="row">
    <div class="col-sm-3">
        <form id="existing_customer" action="<?php echo base_url() ?>admin/holiday" method="post">
            <label for="field-1" class="control-label pull-left holiday-vertical"><strong>Year:</strong></label>
            <div class="col-sm-8">
                <input type="text" name="year" class="form-control years" value="<?php
                if (!empty($year)) {
                    echo $year;
                }
                ?>" data-format="yyyy">
            </div>
            <button type="submit" id="search_product" data-toggle="tooltip" data-placement="top" title="Search"
                    class="btn btn-purple pull-right">
                <i class="fa fa-search"></i></button>
        </form>
    </div>
    <?php if (!empty($created)) { ?>
        <div class="col-sm-9 mt">
            <a href="<?= base_url() ?>admin/holiday/add_holiday" class="text-danger" data-toggle="modal"
               data-placement="top" data-target="#myModal"><span
                        class="fa fa-plus "> <?= lang('new') . ' ' . lang('holiday') ?></span></a>
        </div>
    <?php } ?>
</div>
<div class="row ">
    <div class="col-md-3">
        <ul class="mt nav nav-pills nav-stacked navbar-custom-nav">
            <?php
            foreach ($all_holiday_list as $key => $v_holiday_list):
                $year = date('Y');
                $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                ?>
                <li class="<?php
                if ($current_month == $key) {
                    echo 'active';
                }
                ?>">
                    <a aria-expanded="<?php
                    if ($current_month == $key) {
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                    ?>" data-toggle="tab" href="#<?php echo $month_name ?>">
                        <i class="fa fa-fw fa-calendar"></i> <?php echo $month_name; ?> </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-9">
        <div class="tab-content pl0">
            <?php
            foreach ($all_holiday_list as $key => $v_holiday_list):
                $year = date('Y');
                $month_name = date('F', strtotime($year . '-' . $key)); // get full name of month by date query
                ?>
                <div id="<?php echo $month_name ?>" class="tab-pane <?php
                if ($current_month == $key) {
                    echo 'active';
                }
                ?>">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <strong><i class="fa fa-calendar"></i> <?php echo $month_name; ?></strong>
                            </div>

                        </div>
                        <!-- Table -->
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><?= lang('event_name') ?></th>
                                <th class="col-sm-2"><?= lang('start_date') ?></th>
                                <th class="col-sm-2"><?= lang('end_date') ?></th>
                                <th class="col-sm-1"><?= lang('color') ?></th>
                                <?php if (!empty($edited) || !empty($deleted)) { ?>
                                    <th class="col-sm-2"><?= lang('action') ?></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $key = 1 ?>
                            <?php if (!empty($v_holiday_list)): foreach ($v_holiday_list as $v_holiday) : ?>
                                <tr>
                                    <td><?php echo $v_holiday->event_name ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_holiday->start_date)) ?></td>
                                    <td><?= strftime(config_item('date_format'), strtotime($v_holiday->end_date)) ?></td>
                                    <td><span style="background-color:<?= $v_holiday->color ?>"
                                              class="color-tag"></span></td>
                                    <?php if (!empty($edited) || !empty($deleted)) { ?>
                                        <td>
                                            <?php if (!empty($edited)) { ?>

                                                <?php echo btn_edit_modal('admin/holiday/add_holiday/' . $v_holiday->holiday_id); ?>
                                            <?php }
                                            if (!empty($deleted)) { ?>
                                                <?php echo btn_delete('admin/holiday/delete_holiday/' . $v_holiday->holiday_id); ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>

                                </tr>
                                <?php
                                $key++;
                            endforeach;
                                ?>
                            <?php else : ?>
                                <td colspan="3">
                                    <strong><?= lang('nothing_to_display') ?></strong>
                                </td>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>