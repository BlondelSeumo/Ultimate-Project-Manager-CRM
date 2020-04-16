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
    function check_old_password(val) {
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
                            $("#id_old_error_msg").append(result);
                            document.getElementById('change_old_password').disabled = true;
                        } else {
                            $("#id_old_error_msg").css("display", "none");
                            document.getElementById('change_old_password').disabled = false;
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
    function check_username_current_password(val) {
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
                            $("#id_user_error_msg").append(result);
                            document.getElementById('change_username').disabled = true;
                        } else {
                            $("#id_user_error_msg").css("display", "none");
                            document.getElementById('change_username').disabled = false;
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

    function check_current_password() {
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
                            document.getElementById('change_email').disabled = true;
                        } else {
                            $("#id_error_msg").css("display", "none");
                            document.getElementById('change_email').disabled = false;
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
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }

    }
    ;
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
    function get_item_name_by_id(stock_sub_category_id) {
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/get_item_name_by_id/" + stock_sub_category_id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        $("#item_name").html("<option value='' ><?= lang('select') . ' ' . lang('item_name') ?></option>");
                        $("#item_name").append(result);

                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }

    }
    ;
    function check_advance_amount(str) {

        var amount = $.trim(str);
        var user_id = $.trim($("#user_id").val());
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/check_advance_amount/" + amount + "/" + user_id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        document.getElementById('advance_amount').innerHTML = result;
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
    function get_leave_details(user_id) {
        if (user_id) {
            var strURL = base_url + "admin/global_controller/get_leave_details/" + user_id;

            var req = getXMLHTTP();
            if (req) {
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        // only if "OK"
                        if (req.status == 200) {
                            var result = req.responseText;

                            var msg = result.trim();
                            if (msg) {
                                document.getElementById('leave_details').innerHTML = result;
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
    }
    function check_available_leave(date) {
        var leave_category_id = $.trim($("#leave_category").val());

        <?php if (!empty(admin_head())) { ?>
        var user_id = $.trim($("#users_id option:selected").val());
        <?php }else{?>
        var user_id = $.trim($("#user_id").val());
        <?php }?>
        var leave_type = $.trim($("input:radio[name=leave_type]:checked").val());
        if (leave_type == 'single_day') {
            var start_date = date;
            var end_date = null;
        }
        if (leave_type == 'multiple_days') {
            var start_date = $.trim($("#multiple_days_start_date").val());
            var end_date = date;
        }
        if (leave_type == 'hours') {
            var start_date = date;
            var end_date = null;
        }
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/check_available_leave/" + user_id + "/" + start_date + "/" + end_date + "/" + leave_category_id;
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
                            document.getElementById('file-save-button').disabled = true;
                        } else {
                            document.getElementById('file-save-button').disabled = false;
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

    function get_employee_by_designations_id(designation_id) {
        var base_url = '<?= base_url() ?>';
        var strURL = base_url + "admin/global_controller/get_employee_by_designations_id/" + designation_id;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        $("#employee").html("<option value='' ><?= lang('select_employee')?>...</option>");
                        $("#employee").append(result);

                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("POST", strURL, true);
            req.send(null);
        }

    }
    ;
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
                            $("#id_duplicate_emp").css("display", "block");
                            document.getElementById('sbtn').disabled = true;
                        } else {
                            $("#id_duplicate_emp").css("display", "none");
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
    ;
    function check_match_password(val) {
        var base_url = '<?= base_url() ?>';
        var password = $.trim($("#password").val());
        var strURL = base_url + "admin/global_controller/check_match_password/" + val + "/" + password;
        var req = getXMLHTTP();
        if (req) {
            req.onreadystatechange = function () {
                if (req.readyState == 4) {
                    // only if "OK"
                    if (req.status == 200) {
                        var result = req.responseText;
                        if (result) {
                            $("#passqord_match").append(result);
                            document.getElementById('sbtn').disabled = true;
                        } else {
                            $("#passqord_match").css("display", "none");
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

</script>