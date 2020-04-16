<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>

<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('job_application_list') ?></strong>
            <?php $is_department_head = is_department_head();
            if ($this->session->userdata('user_type') == 1 || !empty($is_department_head)) { ?>
                <div class="pull-right hidden-print">
                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                         data-title="<?php echo lang('filter_by'); ?>">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right group animated zoomIn"
                            style="width:300px;">
                            <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>
                            <li class="divider"></li>
                            <?php
                            $job_circular_info = $this->job_circular_model->get_permission('tbl_job_circular');
                            if (!empty($job_circular_info)) {
                                foreach ($job_circular_info as $v_circular_info) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_circular_info->job_circular_id ?>">
                                        <a href="#"><?php echo $v_circular_info->job_title; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- Table -->
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped" id="DataTables">
                <thead>
                <tr>
                    <th><?= lang('job_title') ?></th>
                    <th><?= lang('name') ?></th>
                    <th><?= lang('email') ?></th>
                    <th class="col-sm-1"><?= lang('mobile') ?></th>
                    <th class="col-sm-1"><?= lang('apply_on') ?></th>
                    <th class="col-sm-1"><?= lang('status') ?></th>
                    <th class="col-sm-2"><?= lang('action') ?></th>
                </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/job_circular/jobs_applicationsList";
                        $('.filtered > .dropdown-toggle').on('click', function () {
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
                            table_url(base_url + "admin/job_circular/jobs_applicationsList/" + filter_by);
                        });
                        <?php if(!empty($job_appliactions_id)){?>
                        list = base_url + "admin/job_circular/jobs_applicationsList/<?= $job_appliactions_id?>";
                        <?php }?>
                    });
                </script>
                </tbody>
            </table>
        </div>
    </div>
</div>
