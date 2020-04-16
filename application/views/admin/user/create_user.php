<?php include_once 'asset/admin-ajax.php'; ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>asset/css/kendo.default.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>asset/css/kendo.common.min.css" />
<script type="text/javascript" src="<?php echo base_url(); ?>asset/js/kendo.all.min.js"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <strong>Create User</strong>
                </div>
            </div>
            <div class="panel-body">


                <div class="panel-body">
                    <form role="form" id="userform" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/user/save_user" method="post" class="form-horizontal form-groups-bordered">
                        <div class="row">
                            <div class="col-sm-6">

                                <input type="hidden" id="username_flag" value="">

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Name<span class="required">*</span></label>

                                    <div class="col-sm-8">
                                        <input type="text" name="name" value="<?php echo $user_login_details->name; ?>" class="form-control"  placeholder="Name" required/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Email<span class="required">*</span></label>

                                    <div class="col-sm-8">
                                        <input type="text" name="email" value="<?php echo $user_login_details->email; ?>" class="form-control"  placeholder="Email" required/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Login<span class="required">*</span></label>

                                    <div class="col-sm-8">
                                        <input type="text" name="user_name" value="<?php echo $user_login_details->user_name; ?>" class="form-control" onchange="check_user_name(this.value)" placeholder="User Name" required/>
                                        <div class="required" id="username_result"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">password<span class="required">*</span></label>

                                    <div class="col-sm-8">
                                        <input type="password" name="password" value="<?php
                                        if (!empty($employee_id)) {
                                            echo' 12345 ';
                                        }
                                        ?>"  class="form-control"  placeholder="Password" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="field-1" class="col-sm-3 control-label">Type of User<span class="required">*</span></label>

                                    <div class="col-sm-8">
                                        <select id="user_type" name="flag" class="form-control" required>
                                            <option value="" <?php echo $user_login_details->flag == 3 ? 'selected' : '' ?>>Select User Type</option>
                                            <option <?php echo $user_login_details->flag == 1 ? 'selected' : '' ?> value="1">Admin</option>
                                            <option <?php echo $user_login_details->flag == 0 ? 'selected' : '' ?> value="0">User</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="user_flag" value="<?php echo $user_login_details->flag ?>">
                                </div>
                                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employee_id ?>">

                                <div class="col-sm-offset-3 col-sm-8">
                                    <button type="submit" id="sbtn" class="btn btn-primary"><?php echo!empty($employee_id) ? 'Update User' : 'Create User' ?></button>
                                </div>

                            </div>
                            <div class="col-sm-6">

                                <div id="roll" class="list-group">
                                    <a href="#" class="list-group-item disabled">
                                        User Permission Level
                                    </a>
                                    <a href="#" class="list-group-item">
                                        <div class="k-header">
                                            <div class="box-col">
                                                <div id="treeview"></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>




    <script>
                $("#treeview").kendoTreeView({
        checkboxes: {
        checkChildren: true,
                template: "<input type='checkbox' #= item.check# name='menu[]' value='#= item.value #'  />"

        },
                check: onCheck,
                dataSource: [
<?php foreach ($result as $parent => $v_parent): ?>
    <?php if (is_array($v_parent)): ?>
        <?php foreach ($v_parent as $parent_id => $v_child): ?>
                            {
                            id: "", text: "<?php echo $parent; ?>", value: "<?php
            if (!empty($parent_id)) {
                echo $parent_id;
            }
            ?>", expanded: false, items: [
            <?php foreach ($v_child as $child => $v_sub_child) : ?>
                <?php if (is_array($v_sub_child)): ?>
                    <?php foreach ($v_sub_child as $sub_chld => $v_sub_chld): ?>
                                        {
                                        id: "", text: "<?php echo $child; ?>", value: "<?php
                        if (!empty($sub_chld)) {
                            echo $sub_chld;
                        }
                        ?>", expanded: false, items: [
                        <?php foreach ($v_sub_chld as $sub_chld_name => $sub_chld_id): ?>
                                            {
                                            id: "", text: "<?php echo $sub_chld_name; ?>",<?php
                            if (!empty($roll[$sub_chld_id])) {
                                echo $roll[$sub_chld_id] ? 'check: "checked",' : '';
                            }
                            ?> value: "<?php
                            if (!empty($sub_chld_id)) {
                                echo $sub_chld_id;
                            }
                            ?>",
                                            },
                        <?php endforeach; ?>
                                        ]
                                        },
                    <?php endforeach; ?>
                <?php else: ?>
                                    {
                                    id: "", text: "<?php echo $child; ?>", <?php
                    if (!is_array($v_sub_child)) {
                        if (!empty($roll[$v_sub_child])) {
                            echo $roll[$v_sub_child] ? 'check: "checked",' : '';
                        }
                    }
                    ?> value: "<?php
                    if (!empty($v_sub_child)) {
                        echo $v_sub_child;
                    }
                    ?>",
                                    },
                <?php endif; ?>
            <?php endforeach; ?>
                            ]
                            },
        <?php endforeach; ?>
    <?php else: ?>
                        { <?php if ($parent == 'Dashboard') {
            ?>
                            id: "", text: "<?php echo $parent ?>", <?php echo 'check: "checked",';
            ?>  value: "<?php
            if (!is_array($v_parent)) {
                echo $v_parent;
            }
            ?>"
            <?php
        } else {
            ?>
                            id: "", text: "<?php echo $parent ?>", <?php
            if (!is_array($v_parent)) {
                if (!empty($roll[$v_parent])) {
                    echo $roll[$v_parent] ? 'check: "checked",' : '';
                }
            }
            ?> value: "<?php
            if (!is_array($v_parent)) {
                echo $v_parent;
            }
            ?>"
        <?php }
        ?>
                        },
    <?php endif; ?>
<?php endforeach; ?>
                ]
        });
                // show checked node IDs on datasource change
                        function onCheck() {
                        var checkedNodes = [],
                                treeView = $("#treeview").data("kendoTreeView"),
                                message;
                                checkedNodeIds(treeView.dataSource.view(), checkedNodes);
                                $("#result").html(message);
                        }
    </script>


    <script type="text/javascript">

                $(function () {
                $("#treeview .k-checkbox input").eq(0).hide();
                        $('form').submit(function () {
                $('#treeview :checkbox').each(function () {
                if (this.indeterminate) {
                this.checked = true;
                }
                });
                })
                })
    </script>

    <script>
                        $(document).ready(function(){

                var user_flag = document.getElementById("user_type").value;
                        if (user_flag == '' || user_flag == '0')
                {
                $("#roll").show();
                }
                else
                {
                $("#roll").hide();
                }

                // on change user type select action
                $('#user_type').on('change', function() {
                if (this.value == '0' || this.value == '')
                        //.....................^.......
                        {
                        $("#roll").show();
                        }
                else
                {
                $("#roll").hide();
                }
                });
                });</script>

    <script>

                        $().ready(function() {

                // validate signup form on keyup and submit
                $("#userform").validate({
                rules: {
                user_name: "required",
                        name: "required",
                        user_name: {
                        required: true,
                                minlength: 4
                        },
                        password: {
                        required: true,
                                minlength: 6
                        },
                        email: {
                        required: true,
                                email: true
                        }

                },
                        highlight: function(element) {
                        $(element).closest('.form-group').addClass('has-error');
                        },
                        unhighlight: function(element) {
                        $(element).closest('.form-group').removeClass('has-error');
                        },
                        errorElement: 'span',
                        errorClass: 'help-block',
                        errorPlacement: function(error, element) {
                        if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                        } else {
                        error.insertAfter(element);
                        }
                        },
                        messages: {
                        user_name: {
                        required: "Please enter a username",
                                minlength: "Your username must consist of at least 4 characters"
                        },
                                password: {
                                required: "Please provide a password",
                                        minlength: "Your password must be at least 6 characters long"
                                },
                                email: "Please enter a valid email address",
                                name: "Please enter your Name"

                        }

                });
                });
    </script>
