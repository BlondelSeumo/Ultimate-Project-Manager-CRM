<?php
$this->load->view('admin/components/htmlheader');
$opened = $this->session->userdata('opened');
$this->session->unset_userdata('opened');
$time = date('h:i:s');
$r = display_time($time);
$time1 = explode(' ', $r);
$timezone = config_item('timezone');
if (empty($timezone)) {
    $timezone = 'Australia/Sydney';
}
?>


<script type="text/javascript">
    function startTime() {
        var c_time = new Date();
        var time = new Date(c_time.toLocaleString('en-US', {timeZone: '<?= $timezone?>'}));

        var date = time.getDate();
        var month = time.getMonth() + 1;
        var years = time.getFullYear();
        var hr = time.getHours();
        var hour = time.getHours();
        var min = time.getMinutes();
        var minn = time.getMinutes();
        var sec = time.getSeconds();
        var secc = time.getSeconds();
        if (date <= 9) {
            var dates = "0" + date;
        } else {
            dates = date;
        }
        if (month <= 9) {
            var months = "0" + month;
        } else {
            months = month;
        }
        <?php if(empty($time1[1])){?>
        var ampm = ' ';
        <?php }else{?>
        var ampm = " PM "
        if (hr < 12) {
            ampm = " AM "
        }
        if (hr > 12) {
            hr -= 12
        }
        <?php }?>

        if (hr < 10) {
            hr = " " + hr
        }
        if (min < 10) {
            min = "0" + min
        }
        if (sec < 10) {
            sec = "0" + sec
        }
        document.getElementById('txt').innerHTML = hr + ":" + min + ":" + sec + ampm;
        var t = setTimeout(function () {
            startTime()
        }, 500);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }
        ;  // add zero in front of numbers < 10
        return i;
    }

</script>
<body onload="startTime();" class="<?php if (!empty($opened)) {
    echo 'offsidebar-open';
} ?> <?= config_item('layout-h') . ' ' . config_item('aside-float') . ' ' . config_item('aside-collapsed') . ' ' . config_item('layout-boxed') . ' ' . config_item('layout-fixed') ?>">
<div class="wrapper">
    <!-- top navbar-->
    <?php $this->load->view('admin/components/header'); ?>
    <!-- sidebar-->
    <?php if (empty(config_item('layout-h'))) { ?>
        <?php $this->load->view('admin/components/sidebar'); ?>
    <?php } ?>
    <!-- offsidebar-->
    <?php $this->load->view('client/components/offsidebar'); ?>
    <!-- Main section-->
    <section>
        <?php if (!empty(config_item('layout-h'))) { ?>
            <!-- sidebar-->
            <?php $this->load->view('admin/components/horizontal_sidebar'); ?>
        <?php } ?>
        <?php
        $active_pre_loader = config_item('active_pre_loader');
        if (!empty($active_pre_loader) && $active_pre_loader == 1) {
            ?>
            <div id="loader-wrapper">
                <div id="loader"></div>
            </div>
        <?php } ?>
        <!-- Page content-->
        <div class="content-wrapper">
            <div class="content-heading">
                <?php
                $breadcrumbs = $this->breadcrumbs->build_breadcrumbs();
                if (empty($breadcrumbs)) {
                    $breadcrumbs = lang($this->uri->segment(2));
                }
                echo $breadcrumbs;

                $user_id = $this->session->userdata('user_id');
                $attendance_info = $this->db->where('user_id', $user_id)->get('tbl_attendance')->result();
                foreach ($attendance_info as $v_info) {
                    $all_clocking[] = $this->admin_model->check_by(array('attendance_id' => $v_info->attendance_id, 'clocking_status' => 1), 'tbl_clock');
                }
                if (!empty($all_clocking)) {
                    foreach ($all_clocking as $v_clocking) {
                        if (!empty($v_clocking)) {
                            $clocking = $v_clocking;
                        }
                    }
                }
                ?>
                <div class="pull-right">
                    <small class="text-sm">
                        &nbsp;<?php echo lang(date('l')) . ' ' . lang(date('jS')) . ' ' . lang(date('F')) . ' ' . date('\- Y,'); ?>
                        &nbsp;<?= lang('time') ?>
                        &nbsp;<span id="txt"></span></small>
                    <?php if (!empty($clocking->clock_id)): ?>
                        <a href="<?php echo base_url() ?>admin/dashboard/set_clocking/<?php
                        if (!empty($clocking)) {
                            echo $clocking->clock_id;
                        }
                        ?>" class="btn btn-danger clock_in_button"><i
                                class="fa fa-arrow-left"></i> <?= lang('clock_out') ?></a>
                    <?php else: ?>
                        <a href="<?php echo base_url() ?>admin/dashboard/set_clocking/<?php
                        if (!empty($clocking)) {
                            echo $clocking->clock_id;
                        }
                        ?>" class="btn btn-success clock_in_button"><i
                                class="fa fa-sign-out"></i> <?= lang('clock_in') ?></a>
                        </button>
                    <?php endif; ?>

                </div>
            </div>
            <?= (!empty(config_item('layout-h')) == ' layout-h ' ? '</div>' : '') ?>
            <div class="row">
                <div class="col-lg-12">
                    <?php echo $subview ?>
                </div>
            </div>
            <?= (!empty(config_item('layout-h')) != ' layout-h ' ? '</div>' : '') ?>
    </section>
    <!-- Page footer-->
    <footer>
        <div class="pull-right hidden-xs">
            <?= '<b>' . lang('version') . '</b> ' . config_item('version') ?>
        </div>
        <strong>&copy; <a href="<?= config_item('copyright_url') ?>"> <?= config_item('copyright_name') ?></a>.</strong>
        All rights reserved.
    </footer>
</div>
<?php
$this->load->view('admin/components/footer');
$direction = $this->session->userdata('direction');
if (!empty($direction) && $direction == 'rtl') {
    $RTL = 'on';
} else {
    $RTL = config_item('RTL');
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        $(".clock_in_button").click(function () {
            var ubtn = $(this);
            ubtn.html('<?= lang('please_wait')?>' + '...');
            ubtn.addClass('disabled');
        });

        $('[data-ui-slider]').slider({
            <?php
            if (!empty($RTL)) {?>
            reversed: true,
            <?php }
            ?>
        });
        /*
         * Multiple drop down select
         */
        $(".select_box").select2({
            <?php

            if (!empty($RTL)) {?>
            dir: "rtl",
            <?php }
            ?>
        });
        $(".select_2_to").select2({
            tags: true,
            <?php
            if (!empty($RTL)) {?>
            dir: "rtl",
            <?php }
            ?>
            allowClear: true,
            placeholder: 'To : Select or Write',
            tokenSeparators: [',', ' ']
        });
        $(".select_multi").select2({
            tags: true,
            <?php
            if (!empty($RTL)) {?>
            dir: "rtl",
            <?php }
            ?>
            allowClear: true,
            placeholder: 'Select Multiple',
            tokenSeparators: [',', ' ']
        });
    })
</script>

<script type="text/javascript">
    $(document).on("click", '.is_complete input[type="checkbox"]', function () {
            var task_id = $(this).data().id;
            var task_complete = $(this).is(":checked");
            var formData = {
                'task_id': task_id,
                'task_progress': 100,
                'task_status': 'completed'
            };
            $.ajax({
                type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                url: '<?= base_url()?>admin/tasks/completed_tasks/' + task_id, // the url where we want to POST
                data: formData, // our data object
                dataType: 'json', // what type of data do we expect back from the server
                encode: true,
                success: function (res) {
                    if (res) {
                        toastr[res.status](res.message);
                    } else {
                        alert('There was a problem with AJAX');
                    }
                }
            })
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#permission_user_1').hide();
        $("div.action_1").hide();
        $("input[name$='permission']").click(function () {
            $("#permission_user_1").removeClass('show');
            if ($(this).attr("value") == "custom_permission") {
                $("#permission_user_1").show();
            } else {
                $("#permission_user_1").hide();
            }
        });
        $("input[name$='assigned_to[]']").click(function () {
            var user_id = $(this).val();
            $("#action_1" + user_id).removeClass('show');
            if (this.checked) {
                $("#action_1" + user_id).show();
            } else {
                $("#action_1" + user_id).hide();
            }

        });
    });
</script>

<?php $this->load->view('admin/_layout_modal'); ?>
<?php $this->load->view('admin/_layout_modal_lg'); ?>
<?php $this->load->view('admin/_layout_modal_large'); ?>
<?php $this->load->view('admin/_layout_modal_extra_lg'); ?>
