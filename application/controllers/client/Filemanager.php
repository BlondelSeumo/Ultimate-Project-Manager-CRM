<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);

class Filemanager extends Client_Controller
{
    public function index()
    {
        $data['title'] = lang('filemanager');
        $data['breadcrumbs'] = lang('filemanager');
        $data['subview'] = $this->load->view('client/filemanager/filemanager', $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function elfinder_init()
    {
        $this->load->helper('path');
        $_allowed_files = explode('|', config_item('allowed_files'));
        $config_allowed_files = array();
        if (is_array($_allowed_files)) {
            foreach ($_allowed_files as $v_extension) {
                array_push($config_allowed_files, '.' . $v_extension);
            }
        }
        $allowed_files = array();
        if (is_array($config_allowed_files)) {
            foreach ($config_allowed_files as $extension) {
                $_mime = get_mime_by_extension($extension);
                if ($_mime == 'application/x-zip') {
                    array_push($allowed_files, 'application/zip');
                }
                if ($extension == '.exe') {
                    array_push($allowed_files, 'application/x-executable');
                    array_push($allowed_files, 'application/x-msdownload');
                    array_push($allowed_files, 'application/x-ms-dos-executable');
                }
                array_push($allowed_files, $_mime);
            }
        }
        $root_options = array(
            'driver' => 'LocalFileSystem',
            'path' => set_realpath('filemanager'),
            'URL' => site_url('filemanager') . '/',
            'uploadMaxSize' => config_item('max_file_size') . 'M',
            'accessControl' => 'access',
            'uploadDeny' => [
                'application/x-httpd-php',
                'application/php',
                'application/x-php',
                'text/php',
                'text/x-php',
                'application/x-httpd-php-source',
                'application/perl',
                'application/x-perl',
                'application/x-python',
                'application/python',
                'application/x-bytecode.python',
                'application/x-python-bytecode',
                'application/x-python-code',
                'wwwserver/shellcgi', // CGI
            ],
            'uploadAllow' => $allowed_files,
            'uploadOrder' => array(
                'allow',
                'deny'
            ),
            'attributes' => array(
                array(
                    'pattern' => '/.tmb/',
                    'hidden' => true
                ),
                array(
                    'pattern' => '/.quarantine/',
                    'hidden' => true
                )
            )
        );
        $client_info = $this->db->where('client_id', $this->session->userdata('client_id'))->get('tbl_client')->row();
        $user = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();
        $c_slug = slug_it($client_info->name);
        $path = set_realpath('filemanager/' . $c_slug);
        if (!is_dir($path)) {
            mkdir($path);
        }
        $c_path = set_realpath('filemanager/' . $c_slug . '/' . $user->media_path_slug);
        if (empty($user->media_path_slug)) {
            $this->db->where('user_id', $user->user_id);
            $slug = slug_it($user->username);
            $this->db->update('tbl_users', array(
                'media_path_slug' => $slug
            ));
            $user->media_path_slug = $slug;
            $c_path = set_realpath('filemanager/' . $c_slug . '/' . $user->media_path_slug);
        }
        if (!is_dir($c_path)) {
            mkdir($c_path);
        }
        if (!file_exists($c_path . '/index.html')) {
            fopen($c_path . '/index.html', 'w');
        }
        array_push($root_options['attributes'], array(
            'pattern' => '/.(' . $user->media_path_slug . '+)/', // Prevent deleting/renaming folder
            'read' => true,
            'write' => true,
            'locked' => true
        ));
        $root_options['path'] = $c_path;
        $root_options['URL'] = site_url('filemanager/' . $c_slug . '/' . $user->media_path_slug) . '/';
        $opts = array(
            'roots' => array(
                $root_options
            )
        );
        $this->load->library('elfinder_lib', $opts);
    }
}
