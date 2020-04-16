<style type="text/css">
    .dragger {
        background: url(../../assets/img/dragger.png) 0px 15px no-repeat;
        cursor: pointer;
    }
</style>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('admin') . ' ' . lang('dashboard') . ' ' . lang('settings') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <?php
                $all_report = $this->db->where('report', 1)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();
                ?>
                <tbody id="report_menu">
                <?php
                foreach ($all_report as $v_report) {
                    ?>
                    <tr class="report_menu" id="<?= $v_report->id ?>">
                        <td class="dragger pl-lg">
                            <?= lang($v_report->name) ?>
                        </td>
                        <td class="pl-lg">
                            <input type="text" data-id="<?= $v_report->id ?>" name="col" value="<?= $v_report->col ?>"
                                   class="form-control change_status">
                        </td>
                        <td>
                            <label
                                class="col-lg-6 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-5 checkbox change_status">
                                <input data-id="<?= $v_report->id ?>" data-toggle="toggle"
                                       name="status"
                                       value="1" <?php if ($v_report->status == 1) {
                                    echo 'checked';
                                } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </td>

                    </tr>
                <?php }
                ?>
                </tbody>
                <tbody id="menu">
                <?php $all_dashboard_data = $this->db->where('report', 0)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();;
                foreach ($all_dashboard_data as $v_dashboard) {
                    ?>
                    <tr class="menu" id="<?= $v_dashboard->id ?>">
                        <td class="dragger pl-lg">
                            <?= lang($v_dashboard->name) ?>
                        </td>
                        <td class="pl-lg">
                            <input data-id="<?= $v_dashboard->id ?>" type="text" name="col"
                                   value="<?= $v_dashboard->col ?>" class="form-control column">
                        </td>
                        <td>
                            <label
                                class="col-lg-6 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-5 checkbox change_status">
                                <input data-id="<?= $v_dashboard->id ?>" data-toggle="toggle"
                                       name="status"
                                       value="1" <?php if ($v_dashboard->status == 1) {
                                    echo 'checked';
                                } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </td>

                        <td>
                            <label data-toggle="tooltip" data-placement="top" title="<?= lang('staff_also_can_see') ?>"
                                   class="col-lg-6 control-label"><?= lang('staff') ?>
                                <i class="fa fa-question-circle"></i>
                            </label>
                            <div class="col-lg-5 checkbox for_staff">
                                <input data-id="<?= $v_dashboard->id ?>" data-toggle="toggle"
                                       name="for_staff"
                                       value="1" <?php if ($v_dashboard->for_staff == 1) {
                                    echo 'checked';
                                } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="panel panel-custom">
    <header class="panel-heading "><?= lang('client') . ' ' . lang('dashboard') . ' ' . lang('settings') ?></header>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped ">
                <?php
                $all_client_report = $this->db->where('report', 2)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();
                ?>
                <tbody id="client_report_menu">
                <?php
                foreach ($all_client_report as $v_client_report) {
                    ?>
                    <tr class="client_report_menu" id="<?= $v_client_report->id ?>">
                        <td class="dragger pl-lg">
                            <?= lang($v_client_report->name) ?>
                        </td>
                        <td class="pl-lg">
                            <input data-id="<?= $v_client_report->id ?>" type="text" name="col"
                                   value="<?= $v_client_report->col ?>" class="form-control column">
                        </td>
                        <td>
                            <label
                                class="col-lg-6 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-5 checkbox change_status">
                                <input data-id="<?= $v_client_report->id ?>" data-toggle="toggle"
                                       name="status"
                                       value="1" <?php if ($v_client_report->status == 1) {
                                    echo 'checked';
                                } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
                </tbody>
                <tbody id="client_menu">
                <?php $all_client_dashboard_data = $this->db->where('report', 3)->order_by('order_no', 'ASC')->get('tbl_dashboard')->result();;
                foreach ($all_client_dashboard_data as $v_client_dashboard) {
                    ?>
                    <tr class="client_menu" id="<?= $v_client_dashboard->id ?>">
                        <td class="dragger pl-lg">
                            <?= lang($v_client_dashboard->name) ?>
                        </td>
                        <td class="pl-lg">
                            <input data-id="<?= $v_client_dashboard->id ?>" type="text" name="col"
                                   value="<?= $v_client_dashboard->col ?>" class="form-control column">
                        </td>
                        <td>
                            <label
                                class="col-lg-6 control-label"><?= lang('active') ?></label>
                            <div class="col-lg-5 checkbox change_status">
                                <input data-id="<?= $v_client_dashboard->id ?>" data-toggle="toggle"
                                       name="status"
                                       value="1" <?php if ($v_client_dashboard->status == 1) {
                                    echo 'checked';
                                } ?> data-on="<?= lang('yes') ?>" data-off="<?= lang('no') ?>"
                                       data-onstyle="success btn-xs"
                                       data-offstyle="danger btn-xs" type="checkbox">
                            </div>
                        </td>
                    </tr>
                <?php }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?= base_url() ?>assets/plugins/jquery-ui/jquery-u.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.change_status input[type="checkbox"]').change(function () {
            var id = $(this).data().id;
            var status = $(this).is(":checked");
            if (status == true) {
                status = 1;
            } else {
                status = 2;
            }
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/settings/save_dashboard/' + id + '/' + status, // the url where we want to POST
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
//                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });
        $('.for_staff input[type="checkbox"]').change(function () {
            var id = $(this).data().id;
            var status = $(this).is(":checked");
            if (status == true) {
                status = 's_' + 1;
            } else {
                status = 's_' + 0;
            }

            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/settings/save_dashboard/' + id + '/' + status, // the url where we want to POST
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
//                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });
        $('input[name="col"]').change(function () {
            var id = $(this).data().id;
            var col = $(this).val();
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/settings/save_dashboard/' + id + '/' + col, // the url where we want to POST
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
//                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })

        });
    })
    $(function () {
        $('tbody[id^="report_menu"]').sortable({
            connectWith: ".report_menu",
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            stop: function (event, ui) {
                var id = JSON.stringify(
                    $('tbody[id^="report_menu"]').sortable(
                        'toArray',
                        {
                            attribute: 'id'
                        }
                    )
                );
                var formData = {
                    'report_menu': id
                };
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '<?= base_url()?>admin/settings/save_dashboard/', // the url where we want to POST
                    data: formData, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    success: function (res) {
                        if (res) {
//                            toastr[res.status](res.message);
                        } else {
                            alert('There was a problem with AJAX');
                        }
                    }
                })

            }
        });
        $(".report_menu").disableSelection();

        $('tbody[id^="menu"]').sortable({
            connectWith: ".menu",
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            stop: function (event, ui) {
                var mid = JSON.stringify(
                    $('tbody[id^="menu"]').sortable(
                        'toArray',
                        {
                            attribute: 'id'
                        }
                    )
                );

                var formData = {
                    'menu': mid
                };
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '<?= base_url()?>admin/settings/save_dashboard/', // the url where we want to POST
                    data: formData, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    success: function (res) {
                        if (res) {
//                            toastr[res.status](res.message);
                        } else {
                            alert('There was a problem with AJAX');
                        }
                    }
                })
            }
        });
        $(".menu").disableSelection();
    });
    $(function () {
        $('tbody[id^="client_report_menu"]').sortable({
            connectWith: ".client_report_menu",
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            stop: function (event, ui) {
                var id = JSON.stringify(
                    $('tbody[id^="client_report_menu"]').sortable(
                        'toArray',
                        {
                            attribute: 'id'
                        }
                    )
                );
                var formData = {
                    'report_menu': id
                };
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '<?= base_url()?>admin/settings/save_dashboard/', // the url where we want to POST
                    data: formData, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    success: function (res) {
                        if (res) {
//                            toastr[res.status](res.message);
                        } else {
                            alert('There was a problem with AJAX');
                        }
                    }
                })

            }
        });
        $(".client_report_menu").disableSelection();

        $('tbody[id^="client_menu"]').sortable({
            connectWith: ".client_menu",
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            stop: function (event, ui) {
                var mid = JSON.stringify(
                    $('tbody[id^="client_menu"]').sortable(
                        'toArray',
                        {
                            attribute: 'id'
                        }
                    )
                );

                var formData = {
                    'menu': mid
                };
                $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '<?= base_url()?>admin/settings/save_dashboard/', // the url where we want to POST
                    data: formData, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    success: function (res) {
                        if (res) {
//                            toastr[res.status](res.message);
                        } else {
                            alert('There was a problem with AJAX');
                        }
                    }
                })
            }
        });
        $(".menu").disableSelection();
    });
</script>