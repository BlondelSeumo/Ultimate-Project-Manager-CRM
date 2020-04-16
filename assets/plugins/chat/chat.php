<script type="text/javascript">
    var base_url = '<?= base_url()?>';
    var userid = '<?= $this->session->userdata('user_id')?>';
    $(document).ready(function () {
        initScrollbar("#chat_list", {setHeight: 600});
        // my
        $('#open_chat_list').click(function () {
            $('#chat_box').css("margin-right", "256px");
            $('#chat_list').fadeIn(5);
            $('#open_chat_list').hide();
        });
        // my
        $('#close_chat_list').click(function () {
            $('#open_chat_list').show();
            $('#chat_box').css("margin-right", "70px");
            $('#chat_list').fadeOut(5);
        });
        $('#chat_multi_user_button').click(function () {
            $.ajax({
                url: base_url + "chat/load_multi_chat",
                type: "get",
                success: function (res) {
                    $('#chat-main-body').html(res);
                }
            });
        });
        // my custom
        $('.start_chat').click(function () {
            // var panel = $('#chat_box').children('div').length;
            // if(panel > 2){
            //   // var child=".panel:nth-child("
            //    $('#chat_box').children('.panel:nth-child('+parseInt(panel-1)+')').addClass('hide');
            //    // $('#chat_box').children('.panel:last-child').addClass('new_panel');
            // }
            var user_id = $(this).data().user_id;
            $.ajax({
                url: base_url + "chat/open_chat_box",
                type: "get",
                data: {
                    user_id: user_id
                },
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        handle_error("#chat-body-errors", res.error_res);
                        return;
                    } else {
                        if (res.exist) {
                            var exist = 1;
                        } else {
                            exist = null;
                        }
                        all_chat_messages(1, function () {
                            open_chat_box(res.chatid, exist);
                            open_chats_boxes();
//                            calculate_popups()
                        });
                    }
                }
            });
        });
        // Get open chat once page has loaded
        open_chats_boxes();

        setInterval(function () {
            all_chat_messages();
        }, interval_time);
    });

    function all_conversations(user_id, myCallBack=null) {
        $.ajax({
            url: base_url + "chat/all_conversations/" + user_id,
            type: "POST",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    for (var i = 0; i < res.chats.length; i++) {

                        var chat = res.chats[i];
                        if ($('#conversation_chat_' + chat.chatid).length) {
                            $('#conversation_chat_' + chat.chatid).html(chat.messages_template);
//                            $('#conversation_chat_' + chat.chatid).scrollTop($('#conversation_chat_' + chat.chatid)[0].scrollHeight);
                        }
                    }// end for loop

                    if (typeof myCallBack === "function") {
                        myCallBack();
                    }
                }
            }
        });
    }

    var interval_time = 5000;
    var open_chats = new Array();

    /* // my custom */
    function open_chat_box(id, exist) {
        $.ajax({
            url: base_url + "chat/active_chat_box/" + id,
            type: "get",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    // Success
                    if (exist) {
                        $('#open_chat_box_' + id).removeClass('hide');
                        open_chat_box(id)
                    } else {
                        build_chat_area(res);
                    }
                }
            }
        });
    }

    // Builds a chat window from a bubble
    function build_chat_area(data) {
        $('#open_chat_box_' + data.chatid).removeClass('chat_badge custom-bg');
        $('#open_chat_box_' + data.chatid).addClass('panel b0 mb0');
        $('#open_chat_box_' + data.chatid).attr('onclick', null);
        $('#open_chat_box_' + data.chatid).html("");
        $('#open_chat_box_' + data.chatid).append('<div class="panel-heading custom-bg pt-sm"><div class="">' + data.title + '<div class="pull-right chat-icon">' +
            '<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"  title="' + lsetting + '" href="#"><i class="fa fa-cog" aria-hidden="true"></i><ul class="dropdown-menu chat-setting-dropdown animated zoomIn"><li><a href="' + base_url + "chat/conversations/" + data.chatid + '">' + lfull_conversation + '"</a></li><li><a data-toggle="modal" data-target="#myModal"  href="' + base_url + "chat/change_title/" + data.chatid + '">' + ledit_name + '"</a></li> <li><a href="#" onclick="delete_chat_box(' + data.chatid + ')">' + ldelete_conversation + '</a></li></ul></a><i data-toggle="tooltip" data-placement="top" onclick="minimize_chat_box(' + data.chatid + ')" title="' + lminimize + '" class="fa fa-minus"></i><i data-toggle="tooltip" onclick="close_chat_box(' + data.chatid + ')" data-placement="top" title="<?= lang('close') ?>" class="fa fa-times" aria-hidden="true"></i></div></div></div>');
        $('#open_chat_box_' + data.chatid).append('<div class="chat-body br bl" id="open_chat_' + data.chatid + '"></div>');
        $('#open_chat_box_' + data.chatid).append('<div class="panel-footer b0 chat-input-box"><input type="text" name="reply" class="form-control" id="chat_input_message_' + data.chatid + '" placeholder="Type a message then Press Enter" onkeypress="return send_message(event, ' + data.chatid + ');"></div>');

        get_chat_messages(data.chatid, 0);
    }

    // Build open chat
    function all_chat_messages(nosound=0, myCallBack=null) {
        $.ajax({
            url: base_url + "chat/all_chat_messages/",
            type: "get",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    // Loop through all chat windows
                    var new_window_flag = false;
                    for (var i = 0; i < res.chats.length; i++) {
                        var chat = res.chats[i];
                        if (!$('#open_chat_box_' + chat.chatid).length) {
                            // Create Chat Bubble
                            $('#chat_box').append(chat.chat_badge);
                            new_window_flag = true;
                        } else {

                            $('#open_chat_box_' + chat.chatid).removeClass('chat_badge custom-bg');
                            $('#open_chat_box_' + chat.chatid).addClass('panel b0 mb0');

                            var last_reply_id = $('#last_reply_id_' + chat.chatid).val();
                            if ($('#open_chat_' + chat.chatid).length) {
                                $('#open_chat_' + chat.chatid).html(chat.messages_template);
                                $('#open_chat_' + chat.chatid).scrollTop($('#open_chat_' + chat.chatid)[0].scrollHeight);
//                                alert('new');
                                // Now check
                                if (!nosound) {
                                    // alert(chat.to_user_id);
                                    if (last_reply_id != $('#last_reply_id_' + chat.chatid).val()) {
                                        // alert(last_reply_id);
                                        $("#chat-tune").trigger('play');
                                    }
                                }
                            } else {
                                // Update active chat bubbles
                                if (chat.unread == 1) {
                                    // Look for unread sign
                                    if (!$('#open_chat_box_' + chat.chatid).length) {
                                        $("#chat-tune").trigger('play');
                                    }
                                    $('#open_chat_box_' + chat.chatid).html('<div class="panel-heading custom-bg minimize-chatbox "><span class="badge-chat small-text">NEW</span>' + chat.title + '</div>');
                                } else {
                                    $('#open_chat_box_' + chat.chatid).html('<div class="panel-heading custom-bg minimize-chatbox ">' + chat.title + '</div>');
                                }
                            }
                        }

                    }// end for loop
                    if (new_window_flag) {
                        $("#chat-tune").trigger('play');
                    }
                    if (typeof myCallBack === "function") {
                        myCallBack();
                    }
                }
            }
        });
    }

    // get the chat log to the chat window
    function get_chat_messages(id, nosound=0) {
        // Get last reply id
        var last_reply_id = $('#last_reply_id_' + id).val();
        $.ajax({
            url: base_url + "chat/get_chat_messages/" + id,
            type: "get",
            dataType: 'JSON',
            success: function (data) {
                if ($('#open_chat_' + id).length) {
                    $('#open_chat_' + id).html(data.messages_template);
                    $('#open_chat_' + id).scrollTop($('#open_chat_' + id)[0].scrollHeight);
                    // Now check
                    if (nosound) {
                        if (last_reply_id != $('#last_reply_id_' + id).val()) {
                            $("#chat-tune").trigger('play');
                        }
                    }
                } else {
                    // Update active chat bubbles
                    if (data.unread == 1) {
                        $('#open_chat_box_' + data.chatid).html('<div class="panel-heading custom-bg minimize-chatbox "><span class="badge-chat small-text">NEW</span>' + data.title + '</div>');
                    } else {
                        $('#open_chat_box_' + data.chatid).html('<div class="panel-heading custom-bg minimize-chatbox ">' + data.title + '</div>');
                    }
                }
            }
        });
    }


    // Close active chat window
    // We make ajax call to flag the chat as closed (for refreshes)
    function close_chat_box(id) {
        $.ajax({
            url: base_url + "chat/close_chat_box/" + id,
            type: "get",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    // Success
                    delete_chat_area(res.chatid);
                }
            }
        });
    }

    // Minimize active chat window
    // if the active value is == 0 then We make ajax call to flag the chat as minimize
    function minimize_chat_box(id) {
        $.ajax({
            url: base_url + "chat/minimize_chat_box/" + id,
            type: "get",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    //  if Success
                    minimize_chat_area(res);
                }
            }
        });
    }

    // Deletes the chat for the user.
    function delete_chat_box(id) {
        $.ajax({
            url: base_url + "chat/delete_chat_box/" + id,
            type: "get",
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    handle_error("#open_chat_box_" + id, res.error_res);
                    return;
                } else {
                    // Success
                    delete_chat_area(res.chatid);
                }
            }
        });
    }
    // Wait For Enter
    // Waits for the enter key to be pressed before sending the message.
    function send_message(event, chatid) {
        // look for window.event in case event isn't passed in
        var e = event || window.event;
        if (e.keyCode == 13) {
            // Send Chat Message
            var message = $('#chat_input_message_' + chatid).val();
            if (message) {
                $.ajax({
                    url: base_url + "chat/send_message/" + chatid,
                    type: "get",
                    data: {
//                    hash: global_hash,
                        message: message
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.error) {
                            handle_error("#open_chat_" + chatid, res.error_res);
                            return false;
                            ;
                        } else {
                            // Success
                            $('#chat_input_message_' + chatid).val("");
                            // Load new chat messages
                            all_chat_messages(1);
                            return false;
                        }
                    }
                });
            }
            return false;
        }
        return true;
    }

    function delete_chat_area(chatid) {
        $('#open_chat_box_' + chatid).remove();
    }

    function minimize_chat_area(data) {
        $('#open_chat_box_' + data.chatid).attr('onclick', 'open_chat_box(' + data.chatid + ')');
        if (data.unread == 1) {
            $('#open_chat_box_' + data.chatid).html('<div class="panel-heading custom-bg minimize-chatbox "><span class="badge-chat small-text">NEW</span>' + data.title + '</div>');
        } else {
            $('#open_chat_box_' + data.chatid).html('<div class="panel-heading custom-bg minimize-chatbox ">' + data.title + '</div>');
        }
    }

    /* Get a list of the user's active chat windows */
    // my one
    function open_chats_boxes() {
        $.ajax({
            url: base_url + "chat/open_chats_boxes",
            type: "get",
            data: {},
            dataType: 'JSON',
            success: function (res) {
                $('#chat_box').html(res.view);
                // Get active chat list
                for (var i = 0; i < res.open_chats.length; i++) {
                    open_chats.push(res.open_chats[i]);
                }
                // Unique it
                open_chats = jQuery.unique(open_chats);
            }
        });
    }

    function handle_error(element, error_res) {
        $(element).html(error_res);
    }

    function remove_error(element) {
        $(element).empty();
    }

    function disable_button(element) {
        $(element).attr("disabled", "disabled");
    }

    function disable_remove(element) {
        $(element).removeAttr("disabled");
        ;
    }

    function remove_chat_from_active_chats(id) {
        for (var i = 0; i < open_chats.length; i++) {
            if (open_chats[i] == id) {
                // Pop it
                open_chats.splice(i, 1);
            }
        }
    }
</script>
