<style type="text/css">
    ._filter_data .dropdown-menu li a, .bootstrap-select .dropdown-menu li a {
        padding: 1px 14px;
        font-size: 12px;
    }
</style>
<div class="row">
    <div class="col-sm-12" data-spy="scroll" data-offset="0">
        <div class="panel panel-custom">
            <div class="panel-heading">
                <div class="panel-title"><?= lang('all_activities'); ?>
                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                         data-title="<?php echo lang('filter_by'); ?>">
                        <button type="button" class="btn btn-default btn-xs ml dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right group animated zoomIn"
                            style="width:300px;">
                            <li class="filter_by"><a href="#"><?php echo lang('all'); ?></a></li>

                            <?php
                            $activities_type = array('client' => 'client', 'estimates' => 'estimates', 'proposals' => 'proposals', 'settings' => 'settings', 'leads' => 'leads',
                                'invoice' => 'invoice', 'user' => 'user', 'payroll' => 'payroll', 'departments' => 'departments',
                                'account' => 'account', 'announcements' => 'announcements', 'attendance' => 'attendance', 'award' => 'award', 'bugs' => 'bugs', 'goal_tracking' => 'goal_tracking',
                                'holiday' => 'holiday', 'items' => 'items', 'job_circular' => 'job_circular', 'knowledgebase' => 'knowledgebase',
                                'leave_management' => 'leave_management', 'opportunities' => 'opportunities', 'overtime' => 'overtime',
                                'projects' => 'projects', 'quotations' => 'quotations', 'stock' => 'stock', 'tasks' => 'tasks', 'training' => 'training',
                                'tickets' => 'tickets', 'transactions' => 'transactions');
                            if (!empty($activities_type)) {
                                foreach ($activities_type as $type => $v_type) { ?>
                                    <li class="filter_by" id="<?= $type ?>"><a
                                            href="#"><?php echo lang($v_type); ?></a></li>
                                <?php }

                            }
                            ?>

                            <div class="clearfix"></div>
                        </ul>
                    </div>

                    <a onclick="return confirm('<?= lang('delete_alert') ?>')"
                       href="<?= base_url() ?>admin/settings/clear_activities"
                       class="btn btn-xs btn-primary pull-right"><?= lang('clear') ?></a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-striped" id="DataTables">
                        <thead>
                        <tr>
                            <th class="col-xs-2"><?= lang('activity_date') ?></th>
                            <th class="col-xs-3"><?= lang('user') ?></th>
                            <th class="col-xs-1"><?= lang('module') ?></th>

                            <th><?= lang('activity') ?></th>

                        </tr>
                        </thead>
                        <tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/settings/activitiesList";
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
                                    table_url(base_url + "admin/settings/activitiesList/" + filter_by);
                                });
                            });
                        </script>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- end -->