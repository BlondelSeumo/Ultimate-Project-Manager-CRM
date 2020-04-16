<script language="javascript" type="text/javascript">
    function getXMLHTTP() { //fuction to return the xml http object
        var xmlhttp = false;
        try {
            xmlhttp = new XMLHttpRequest();
        } catch (e) {
            try {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
                try {
                    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e1) {
                    xmlhttp = false;
                }
            }
        }

        return xmlhttp;
    }

    function check_duplicate_emp_id(val) {
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/check_duplicate_emp_id/" + val;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        if (result) {
                            $("#id_exist_msg").append(result);
                            document.getElementById('sbtn').disabled = true;
                        } else {
                            document.getElementById('sbtn').disabled = false;
                        }

                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }
    }

    $(document).on("change", function () {
        var change_email_password = $('#change_email_password').val();
        var old_password = $('#old_password').val();
        var change_username = $('#change_username').val();
        var check_username = $('#check_username').val();
        var employment_id = $('#check_employment_id').val();
        var check_email_addrees = $('#check_email_addrees').val();
        var user_id = $('#user_id').val();
        var id, btn, value, url, userid;

        if (change_email_password) {
            id = 'email_password';
            btn = 'new_uses_btn';
            value = change_email_password;
            url = 'check_current_password';
        }
        if (old_password) {
            id = 'old_password_error';
            btn = 'old_password_button';
            value = old_password;
            url = 'check_current_password';
        }
        if (change_username) {
            id = 'username_error';
            btn = 'change_username_btn';
            value = change_username;
            userid = user_id;
            url = 'check_current_password';
        }
        if (check_username) {
            id = 'check_username_error';
            btn = 'new_uses_btn';
            url = 'check_existing_user_name'
            value = check_username;
        }
        if (employment_id) {
            id = 'employment_id_error';
            btn = 'new_uses_btn';
            url = 'check_duplicate_emp_id'
            value = employment_id;
            userid = user_id;
        }
        if (check_email_addrees) {
            id = 'email_addrees_error';
            btn = 'new_uses_btn';
            url = 'check_email_addrees'
            value = check_email_addrees;
            userid = user_id;
        }
        if (userid) {
            user_id = userid;
        } else {
            user_id = "";
        }
        if (url) {
            $.ajax({
                url: base_url + "admin/global_controller/" + url + '/' + user_id,
                type: "POST",
                data: {
                    name: value,
                },
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        handle_error("#" + id, res.error);
                        disable_button("#" + btn);
                        return;
                    } else {
                        remove_error("#" + id);
                        disable_remove("#" + btn);
                        return;
                    }
                }
            });
        }
    });


    function check_current_password(val) {
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/check_current_password/" + val;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        if (result) {
                            $("#id_error_msg").css("display", "block");
                            document.getElementById('sbtn').disabled = true;
                        } else {
                            $("#id_error_msg").css("display", "none");
                            document.getElementById('sbtn').disabled = false;
                        }

                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }

    }

    function get_milestone_by_id(project_id) {
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/get_milestone_by_project_id/" + project_id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        $("#milestone_show").find('option').remove();
                        $("#milestone").append(result);
                        $("#milestone_show").show();
                        $("#milestone").show();

                        document.getElementById('milestone').disabled = false;
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }

    }

    function get_related_moduleName(val, proposal) {
        var base_url = '<?= base_url() ?>';
        if (proposal) {
            var strURL = base_url + "admin/global_controller/get_related_moduleName_by_value/" + val + '/' + proposal;
        } else {
            var strURL = base_url + "admin/global_controller/get_related_moduleName_by_value/" + val;
        }
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        $('#related_result').empty();
                        if (result) {
                            $("#related_to").html('<label for="field-1" class="col-sm-3 control-label"><?= lang('select') . ' '?>' + capitalise(val) + '</label>');
                            $("#related_to").append(result);
                        } else {
                            $("#related_to").empty();
                        }
                        if (val == 'project') {
                            init_selectpicker();
                            $("#milestone_show").show();
                            $("#milestone").show();
                            document.getElementById('milestone').disabled = false;
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);
                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", true);
                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);

                            $('.milestone_module').show();
                            $('.milestone_module').prop("disabled", false);
                            $('.project_module').show();
                            $('.project_module').prop("disabled", false);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);

                        }
                        if (val == 'opportunities') {
                            init_selectpicker();
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);
                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", true);
                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);

                            $('.opportunities_module').show();
                            $('.opportunities_module').prop("disabled", false);

                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);
                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);

                        }
                        if (val == 'leads') {
                            init_selectpicker();
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);
                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", true);

                            $('.leads_module').show();
                            $('.leads_module').prop("disabled", false);

                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);

                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);

                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);

                            $('.client_module').hide();
                            $('.client_module').prop("disabled", true);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);

                        }
                        if (val == 'client') {
                            init_selectpicker();
                            $('.client_module').show();
                            $('.client_module').prop("disabled", false);

                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                        }
                        if (val == 'supplier') {
                            init_selectpicker();
                            $('.leads_module').show();
                            $('.leads_module').prop("disabled", false);
                            $('.client_module').hide();
                            $('.client_module').prop("disabled", true);
                        }
                        if (val == 'bug') {
                            init_selectpicker();
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);

                            $('.bugs_module').show();
                            $('.bugs_module').prop("disabled", false);

                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);
                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);
                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);

                        }
                        if (val == 'goal') {
                            init_selectpicker();
                            $('.goal_tracking').show();
                            $('.goal_tracking').prop("disabled", false);

                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", false);

                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);
                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);
                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);
                        }
                        if (val == 'sub_task') {
                            init_selectpicker();
                            $('.sub_task').show();
                            $('.sub_task').prop("disabled", false);

                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", false);

                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);
                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);
                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);
                            $('.expenses').hide();
                            $('.expenses').prop("disabled", true);
                        }
                        if (val == 'expenses') {
                            init_selectpicker();
                            $('.expenses').show();
                            $('.expenses').prop("disabled", false);

                            $('.bugs_module').hide();
                            $('.bugs_module').prop("disabled", false);

                            $('.leads_module').hide();
                            $('.leads_module').prop("disabled", true);
                            $('.opportunities_module').hide();
                            $('.opportunities_module').prop("disabled", true);
                            $('.milestone_module').hide();
                            $('.milestone_module').prop("disabled", true);
                            $('.project_module').hide();
                            $('.project_module').prop("disabled", true);
                            $('.goal_tracking').hide();
                            $('.goal_tracking').prop("disabled", true);
                            $('.sub_task').hide();
                            $('.sub_task').prop("disabled", true);
                        }
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }
    }

    // Init bootstrap select picker
    function init_selectpicker() {
        $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
            showSubtext: true,
        });
    }

    function capitalise(string) {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }

    function check_user_name(str) {

        var user_name = $.trim(str);
        var user_id = $.trim($("#user_id").val());
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/check_existing_user_name/" + user_name + "/" + user_id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        document.getElementById('username_result').innerHTML = result;
                        var msg = result.trim();
                        if (msg) {
                            document.getElementById('sbtn').disabled = true;
                        } else {
                            document.getElementById('sbtn').disabled = false;
                        }

                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);


        }
    }

    function get_project_by_id(id) {

        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/get_project_by_client_id/" + id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        $('#client_project').empty();
                        $("#client_project").append(result);
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);


        }
    }


</script>