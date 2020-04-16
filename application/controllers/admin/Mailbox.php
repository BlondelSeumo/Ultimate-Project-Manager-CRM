<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mailbox
 *
 * @author NaYeM
 */
class Mailbox extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mailbox_model');
        $this->load->helper('ckeditor');
        $this->load->helper('text');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "350px"
            )
        );
        $data['dataTables'] = true;
        $data['select_2'] = true;
        $data['datepicker'] = true;
    }

    public function index($action = NULL, $id = NULL, $status = NULL)
    {
        $data['page'] = lang('mailbox');
        $data['title'] = "Mailbox";
        $user_id = $this->session->userdata('user_id');
        $email = $this->session->userdata('email');

        $this->mailbox_model->_table_name = 'tbl_inbox';
        $this->mailbox_model->_order_by = 'inbox_id';
        $data['read_mail'] = $this->mailbox_model->get_by(array('inbox_id' => $id), true);
        $unread_mail = $this->mailbox_model->get_inbox_message($email, TRUE);
        if (count($unread_mail) > 0) {
            $unread_mail = count($unread_mail);
        } else {
            $unread_mail = 0;
        }
        $data['unread_mail'] = $unread_mail;
        if ($action == 'sent') {
            $data['menu_active'] = 'sent';
            $data['view'] = 'sent';
            $data['get_sent_message'] = $this->mailbox_model->get_sent_message($user_id);
        } elseif ($action == 'read_send_mail') {
            $data['menu_active'] = 'sent';
            $data['view'] = 'read_mail';
            $data['read_mail'] = $this->mailbox_model->check_by(array('sent_id' => $id), 'tbl_sent');
        } elseif ($action == 'draft') {
            $data['menu_active'] = 'draft';
            $data['view'] = 'draft';
            $data['draft_message'] = $this->mailbox_model->get_draft_message($user_id);
        } elseif ($action == 'read_draft_mail') {
            $data['menu_active'] = 'draft';
            $data['view'] = 'read_mail';
            $data['read_mail'] = $this->mailbox_model->check_by(array('draft_id' => $id), 'tbl_draft');
        } elseif ($action == 'favourites') {
            $data['menu_active'] = 'favourites';
            $data['view'] = 'favourites';
            $data['favourites_mail'] = $this->mailbox_model->get_by(array('user_id' => $user_id, 'deleted' => 'no', 'favourites' => '1'), FALSE);
        } elseif ($action == 'trash') {
            $data['menu_active'] = 'trash';
            $data['view'] = 'trash';
            if ($id == 'sent') {
                $data['trash_view'] = 'sent';
                $data['get_sent_message'] = $this->mailbox_model->get_sent_message($user_id, TRUE);
            } elseif ($id == 'draft') {
                $data['trash_view'] = 'draft';
                $data['draft_message'] = $this->mailbox_model->get_draft_message($user_id, TRUE);
            } else {
                $data['trash_view'] = 'inbox';
                $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($email, '', TRUE);
            }
        } elseif ($action == 'read_inbox_mail') {
            $data['menu_active'] = 'inbox';
            $data['view'] = 'read_mail';
            $data['reply'] = 1;
            $this->mailbox_model->_primary_key = 'inbox_id';
            $updata['view_status'] = '1';
            $this->mailbox_model->save($updata, $id);
        } elseif ($action == 'added_favourites') {
            $favdata['favourites'] = $status;
            $this->mailbox_model->_primary_key = 'inbox_id';
            $this->mailbox_model->save($favdata, $id);
            redirect('admin/mailbox/index/inbox');
        } elseif ($action == 'compose') {
            $data['dropzone'] = true;
            $data['view'] = 'compose_mail';
            $data['menu_active'] = 'inbox';
            $profile = profile();
            if ($profile->role_id == 2) {
                $where = array('role_id !=' => '2', 'activated' => '1');
            } else {
                $where = array('activated' => '1');
            }
            $data['get_user_info'] = get_result('tbl_users', $where);
            if (!empty($status)) {
                $data['inbox_info'] = $this->mailbox_model->check_by(array('inbox_id' => $id), 'tbl_inbox');
            } elseif (!empty($id)) {
                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->_order_by = 'draft_id';
                $data['get_draft_info'] = $this->mailbox_model->get_by(array('draft_id' => $id), TRUE);
            }
            $data['editor'] = $this->data;
        } else {
            $data['menu_active'] = 'inbox';
            $data['view'] = 'inbox';
            $data['get_inbox_message'] = $this->mailbox_model->get_inbox_message($email);
        }
        // get mailbox credintial from tbl_user

        $data['subview'] = $this->load->view('admin/mailbox/mailbox', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }


    public function delete_inbox_mail($id)
    {
        $value = array('deleted' => 'Yes');
        $this->mailbox_model->set_action(array('inbox_id' => $id), $value, 'tbl_inbox');
        $inbox_info = $this->mailbox_model->check_by(array('inbox_id' => $id), 'tbl_inbox');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'mailbox',
            'module_field_id' => $id,
            'activity' => lang('activity_delete_inbox'),
            'icon' => 'fa-circle-o',
            'value1' => $inbox_info->subject
        );
        $this->mailbox_model->_table_name = 'tbl_activities';
        $this->mailbox_model->_primary_key = 'activities_id';
        $this->mailbox_model->save($activity);

        $type = "success";
        $message = lang('delete_msg');
        set_message($type, $message);
        redirect('admin/mailbox/index/inbox');
    }

    public function delete_mail($action, $from_trash = NULL, $v_id = NULL)
    {

        // get sellected id into inbox email page
        $selected_id = $this->input->post('selected_id', TRUE);
        if (!empty($selected_id)) { // check selected message is empty or not
            foreach ($selected_id as $v_id) {
                if (!empty($from_trash)) {
                    if ($action == 'inbox') {
                        $activities = lang('activity_delete_tash_inbox');
                        $inbox_info = $this->mailbox_model->check_by(array('inbox_id' => $v_id), 'tbl_inbox');

                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'mailbox',
                            'module_field_id' => $v_id,
                            'activity' => $activities,
                            'icon' => 'fa-circle-o',
                            'value1' => $inbox_info->to
                        );
                        $this->mailbox_model->_table_name = 'tbl_activities';
                        $this->mailbox_model->_primary_key = 'activities_id';
                        $this->mailbox_model->save($activity);

                        $this->mailbox_model->_table_name = 'tbl_inbox';
                        $this->mailbox_model->delete_multiple(array('inbox_id' => $v_id));
                    } elseif ($action == 'sent') {

                        $activities = lang('activity_delete_tash_sent');
                        $inbox_info = $this->mailbox_model->check_by(array('sent_id' => $v_id), 'tbl_sent');

                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'mailbox',
                            'module_field_id' => $v_id,
                            'activity' => $activities,
                            'icon' => 'fa-circle-o',
                            'value1' => $inbox_info->to
                        );
                        $this->mailbox_model->_table_name = 'tbl_activities';
                        $this->mailbox_model->_primary_key = 'activities_id';
                        $this->mailbox_model->save($activity);

                        $this->mailbox_model->_table_name = 'tbl_sent';
                        $this->mailbox_model->delete_multiple(array('sent_id' => $v_id));
                    } else {
                        $activities = lang('activity_delete_tash_draft');
                        $inbox_info = $this->mailbox_model->check_by(array('draft_id' => $v_id), 'tbl_draft');

                        $activity = array(
                            'user' => $this->session->userdata('user_id'),
                            'module' => 'mailbox',
                            'module_field_id' => $v_id,
                            'activity' => $activities,
                            'icon' => 'fa-circle-o',
                            'value1' => $inbox_info->to
                        );
                        $this->mailbox_model->_table_name = 'tbl_activities';
                        $this->mailbox_model->_primary_key = 'activities_id';
                        $this->mailbox_model->save($activity);

                        $this->mailbox_model->_table_name = 'tbl_draft';
                        $this->mailbox_model->delete_multiple(array('draft_id' => $v_id));
                    }
                } else {
                    $value = array('deleted' => 'Yes');
                    if ($action == 'inbox') {
                        $activities = lang('activity_delete_inbox');
                        $inbox_info = $this->mailbox_model->check_by(array('inbox_id' => $v_id), 'tbl_inbox');
                        $this->mailbox_model->set_action(array('inbox_id' => $v_id), $value, 'tbl_inbox');
                    } elseif ($action == 'sent') {
                        $this->mailbox_model->set_action(array('sent_id' => $v_id), $value, 'tbl_sent');
                        $activities = lang('activity_delete_sent');
                        $inbox_info = $this->mailbox_model->check_by(array('sent_id' => $v_id), 'tbl_sent');
                    } else {
                        $this->mailbox_model->set_action(array('draft_id' => $v_id), $value, 'tbl_draft');
                        $activities = lang('activity_delete_draft');
                        $inbox_info = $this->mailbox_model->check_by(array('draft_id' => $v_id), 'tbl_draft');
                    }
                    $activity = array(
                        'user' => $this->session->userdata('user_id'),
                        'module' => 'mailbox',
                        'module_field_id' => $v_id,
                        'activity' => $activities,
                        'icon' => 'fa-circle-o',
                        'value1' => $inbox_info->to
                    );
                    $this->mailbox_model->_table_name = 'tbl_activities';
                    $this->mailbox_model->_primary_key = 'activities_id';
                    $this->mailbox_model->save($activity);
                }
            }
            $type = "success";
            $message = lang('delete_msg');
        } elseif (!empty($v_id)) {
            if ($action == 'inbox') {
                $activities = lang('activity_delete_tash_inbox');
                $inbox_info = $this->mailbox_model->check_by(array('inbox_id' => $v_id), 'tbl_inbox');

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'mailbox',
                    'module_field_id' => $v_id,
                    'activity' => $activities,
                    'icon' => 'fa-circle-o',
                    'value1' => $inbox_info->to
                );
                $this->mailbox_model->_table_name = 'tbl_activities';
                $this->mailbox_model->_primary_key = 'activities_id';
                $this->mailbox_model->save($activity);

                $this->mailbox_model->_table_name = 'tbl_inbox';
                $this->mailbox_model->delete_multiple(array('inbox_id' => $v_id));
            } elseif ($action == 'sent') {
                $activities = lang('activity_delete_sent');
                $inbox_info = $this->mailbox_model->check_by(array('sent_id' => $v_id), 'tbl_sent');

                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'mailbox',
                    'module_field_id' => $v_id,
                    'activity' => $activities,
                    'icon' => 'fa-circle-o',
                    'value1' => $inbox_info->to
                );
                $this->mailbox_model->_table_name = 'tbl_activities';
                $this->mailbox_model->_primary_key = 'activities_id';
                $this->mailbox_model->save($activity);

                $this->mailbox_model->_table_name = 'tbl_sent';
                $this->mailbox_model->delete_multiple(array('sent_id' => $v_id));
            } else {
                $activities = lang('activity_delete_tash_draft');
                $inbox_info = $this->mailbox_model->check_by(array('draft_id' => $v_id), 'tbl_draft');
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'mailbox',
                    'module_field_id' => $v_id,
                    'activity' => $activities,
                    'icon' => 'fa-circle-o',
                    'value1' => $inbox_info->to
                );
                $this->mailbox_model->_table_name = 'tbl_activities';
                $this->mailbox_model->_primary_key = 'activities_id';
                $this->mailbox_model->save($activity);

                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->delete_multiple(array('draft_id' => $v_id));
            }


            if ($action == 'inbox') {
                redirect('admin/mailbox/index/trash/inbox');
            } elseif ($action == 'sent') {
                redirect('admin/mailbox/index/trash/sent');
            } else {
                redirect('admin/mailbox/index/trash/draft');
            }
            $type = "success";
            $message = lang('delete_msg');
        } else {
            $type = "error";
            $message = lang('select_message');
        }
        set_message($type, $message);
        if ($action == 'inbox') {
            redirect('admin/mailbox/index/inbox');
        } elseif ($action == 'sent') {
            redirect('admin/mailbox/index/sent');
        } else {
            redirect('admin/mailbox/index/draft');
        }
    }

    public function send_mail()
    {
        $discard = $this->input->post('discard', TRUE);
        if (!empty($discard)) {
            redirect('admin/mailbox/index/inbox');
        }
        $all_email = $this->input->post('to', TRUE);

        // get all email address
        foreach ($all_email as $v_email) {
            $data = $this->mailbox_model->array_from_post(array('subject', 'message_body'));
            $upload_file = array();
            $resourceed_file = array();

            $files = $this->input->post("files",true);
            $target_path = getcwd() . "/uploads/";
            //process the fiiles which has been uploaded by dropzone
            if (!empty($files) && is_array($files)) {
                foreach ($files as $key => $file) {
                    if (!empty($file)) {
                        $file_name = $this->input->post('file_name_' . $file,true);
                        $new_file_name = move_temp_file($file_name, $target_path);
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $size = $this->input->post('file_size_' . $file,true) / 1000;
                        if ($new_file_name) {
                            $up_data = array(
                                "fileName" => $new_file_name,
                                "path" => "uploads/" . $new_file_name,
                                "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                                "ext" => '.' . end($file_ext),
                                "size" => round($size, 2),
                                "is_image" => $is_image,
                            );
                            array_push($upload_file, $up_data);

                            array_push($resourceed_file, $new_file_name);

                        }
                    }
                }
            }

            $fileName = $this->input->post('fileName',true);
            $path = $this->input->post('path',true);
            $fullPath = $this->input->post('fullPath',true);
            $size = $this->input->post('size',true);
            $is_image = $this->input->post('is_image',true);

            if (!empty($fileName)) {
                foreach ($fileName as $key => $name) {
                    $old['fileName'] = $name;
                    $old['path'] = $path[$key];
                    $old['fullPath'] = $fullPath[$key];
                    $old['size'] = $size[$key];
                    $old['is_image'] = $is_image[$key];

                    array_push($upload_file, $old);
                    array_push($resourceed_file, $name);
                }
            }
            if (!empty($upload_file)) {
                $data['attach_file'] = json_encode($upload_file);
                $idata['attach_file'] = json_encode($upload_file);
            } else {
                $data['attach_file'] = null;
                $idata['attach_file'] = NULL;
            }

            $data['to'] = $v_email;
            /*
             * Email Configuaration 
             */
            $user_id = $this->session->userdata('user_id');
            $profile_info = $this->mailbox_model->check_by(array('user_id' => $user_id), 'tbl_account_details');
            $user_info = $this->mailbox_model->check_by(array('user_id' => $user_id), 'tbl_users');
            $mailbox = array('email' => $user_info->email, 'name' => $profile_info->fullname);

            // get company name
            $name = $profile_info->fullname;
            $info = $data['subject'];
            // set from email
            $from = array($name, $info);
            // set sender email
            $to = $v_email;
            //set subject
            $subject = $data['subject'];
            $data['user_id'] = $user_id;
            $data['message_time'] = date('Y-m-d H:i:s');
            $draf = $this->input->post('draf', TRUE);
            if (!empty($draf)) {
                $data['to'] = serialize($all_email);
                // save into send 
                $this->mailbox_model->_table_name = 'tbl_draft';
                $this->mailbox_model->_primary_key = 'draft_id';
                $this->mailbox_model->save($data);
                redirect('admin/mailbox/index/inbox');
            } else {
                // save into send 
                $this->mailbox_model->_table_name = 'tbl_sent';
                $this->mailbox_model->_primary_key = 'sent_id';
                $send_id = $this->mailbox_model->save($data);
                // get mail info by send id to send            
                $this->mailbox_model->_order_by = 'sent_id';
                $data['read_mail'] = $this->mailbox_model->get_by(array('sent_id' => $send_id), true);
                // set view page
                $message = $this->load->view('admin/mailbox/send_email', $data, TRUE);

                $params['subject'] = $subject;
                $params['message'] = $message;
                $params['recipient'] = $data['to'];

                $send_email = $this->send_email($params, $resourceed_file);

                // save into inbox table procees 
                $idata['to'] = $data['to'];
                $idata['from'] = $user_info->email;
                $idata['user_id'] = $user_id;
                $idata['subject'] = $data['subject'];
                $idata['message_body'] = $data['message_body'];
                $idata['message_time'] = date('Y-m-d H:i:s');
                // save into inbox
                $this->mailbox_model->_table_name = 'tbl_inbox';
                $this->mailbox_model->_primary_key = 'inbox_id';
                $this->mailbox_model->save($idata);
            }
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'mailbox',
                'module_field_id' => $user_id,
                'activity' => lang('activity_msg_sent'),
                'icon' => 'fa-circle-o',
                'value1' => $v_email
            );
            $this->mailbox_model->_table_name = 'tbl_activities';
            $this->mailbox_model->_primary_key = 'activities_id';
            $this->mailbox_model->save($activity);
        }

        if ($send_email) {
            $type = "success";
            $message = lang('msg_sent');
            set_message($type, $message);
            redirect('admin/mailbox/index/sent');
        } else {
            show_error($this->email->print_debugger());
        }
    }

    function send_email($params, $attachments = null)
    {

        $config = array();
        // If postmark API is being used
        if (config_item('use_postmark') == 'TRUE') {
            $config = array(
                'api_key' => config_item('postmark_api_key')
            );
            $this->load->library('postmark', $config);
            $this->postmark->from(config_item('postmark_from_address'), config_item('company_name'));
            $this->postmark->to($params['recipient']);
            $this->postmark->subject($params['subject']);
            $this->postmark->message_plain($params['message']);
            $this->postmark->message_html($params['message']);
            // Check resourceed file
            if (isset($attachments)) {
                foreach ($attachments as $files) {
                    $this->postmark->resource(base_url() . 'uploads/' . $files);
                }
            }
            $this->postmark->send();
        } else {
            // If using SMTP
//            if (config_item('protocol') == 'smtp') {
//                $this->load->library('encrypt');
//                $config = array(
//                    'protocol' => config_item('protocol'),
//                    'smtp_host' => config_item('smtp_host'),
//                    'smtp_port' => config_item('smtp_port'),
//                    'smtp_user' => config_item('smtp_user'),
//                    'smtp_pass' => config_item('smtp_pass'),
//                    'smtp_crypto' => config_item('email_encryption'),
//                    'crlf' => "\r\n"
//                );
//            }

            // Send email
            $config['useragent'] = 'UniqueCoder LTD';
            $config['mailpath'] = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = "html";
            $config['charset'] = 'utf-8';
            $config['newline'] = "\r\n";
            $config['crlf'] = "\r\n";
            $config['smtp_timeout'] = '30';
            $config['protocol'] = config_item('protocol');
            $config['smtp_host'] = config_item('smtp_host');
            $config['smtp_port'] = config_item('smtp_port');
            $config['smtp_user'] = trim(config_item('smtp_user'));
            $config['smtp_pass'] = decrypt(config_item('smtp_pass'));
            $config['smtp_crypto'] = config_item('smtp_encryption');

            $this->load->library('email', $config);
            $this->email->clear();
            $this->email->from(config_item('company_email'), config_item('company_name'));
            $this->email->to($params['recipient']);

            $this->email->subject($params['subject']);
            $this->email->message($params['message']);
            if (isset($attachments)) {
                foreach ($attachments as $v_files) {
                    $this->email->attach('uploads/' . $v_files);
                }
            }
            $send = $this->email->send();
            if (!empty($test)) {
                if ($send) {
                    return $send;
                } else {
                    $error = show_error($this->email->print_debugger());
                    return $error;
                }
            } else {
                if ($send) {
                    return $send;
                } else {
                    send_later($params);
                }

            }
            return true;
        }
    }


    public function restore($action, $id)
    {
        $value = array('deleted' => 'No');
        if ($action == 'inbox') {
            $this->mailbox_model->set_action(array('inbox_id' => $id), $value, 'tbl_inbox');
        } elseif ($action == 'sent') {
            $this->mailbox_model->set_action(array('sent_id' => $id), $value, 'tbl_sent');
        } else {
            $this->mailbox_model->set_action(array('draft_id' => $id), $value, 'tbl_draft');
        }
        if ($action == 'inbox') {
            redirect('admin/mailbox/index/inbox');
        } elseif ($action == 'sent') {
            redirect('admin/mailbox/index/sent');
        } else {
            redirect('admin/mailbox/index/draft');
        }
    }

    public function download_file($file)
    {
        $this->load->helper('download');
        if (file_exists(('uploads/' . $file))) {
            $down_data = file_get_contents('uploads/' . $file); // Read the file's contents
            force_download($file, $down_data);
        } else {
            $type = "error";
            $message = 'Operation Fieled !';
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/mailbox');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

}
