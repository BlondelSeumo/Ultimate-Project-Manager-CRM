<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Frontend extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('job_circular_model');
        $this->load->model('invoice_model');
        $this->load->model('estimates_model');
        $this->load->model('proposal_model');
        $this->load->model('kb_model');
        $this->load->helper('string');
        $this->load->library('gst');
    }

    function index()
    {
        $data['title'] = lang('job_posted_list');
        $data['subview'] = $this->load->view('frontend/job_vacancy', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function circular_details($id)
    {
        $data['title'] = lang('view_circular_details');

        //get all training information
        $data['circular_details'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $data['subview'] = $this->load->view('frontend/circular_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function apply_jobs($id)
    {
        $data['title'] = lang('view_circular_details');

        //get all training information
        $data['circular_info'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $data['subview'] = $this->load->view('frontend/apply_jobs', $data, false);
        $this->load->view('admin/_layout_modal_lg', $data);
    }

    public function save_job_application($id)
    {
        $data = $this->job_circular_model->array_from_post(array('name', 'email', 'mobile', 'cover_letter'));
        // Resume File upload
        if (!empty($_FILES['resume']['name'])) {
            $val = $this->job_circular_model->uploadFile('resume');
            $val == TRUE || redirect('frontend/circular_details/' . $id);
            $data['resume'] = $val['path'];
        }
        $data['job_circular_id'] = $id;

        $this->job_circular_model->_table_name = 'tbl_job_appliactions';
        $this->job_circular_model->_primary_key = 'job_appliactions_id';
        $job_appliactions_id = $this->job_circular_model->save($data);

        $circular_info = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $job_circular_email = config_item('job_circular_email');
        if (!empty($circular_info->designations_id)) {
            $design_info = $this->db->where('designations_id', $circular_info->designations_id)->get('tbl_designations')->row();
            $dept_head_id = $this->db->where('departments_id', $design_info->departments_id)->get('tbl_departments')->row();
            $user_info = $this->job_circular_model->check_by(array('user_id' => $dept_head_id->department_head_id), 'tbl_users');
            if (!empty($user_info)) {
                if (!empty($job_circular_email) && $job_circular_email == 1) {
                    $email_template = email_templates(array('email_group' => 'new_job_application_email'), $dept_head_id->department_head_id, true);

                    $message = $email_template->template_body;
                    $subject = $email_template->subject;
                    $name = str_replace("{NAME}", $data['name'], $message);
                    $job_title = str_replace("{JOB_TITLE}", $circular_info->job_title, $name);
                    $email = str_replace("{EMAIL}", $data['email'], $job_title);
                    $mobile = str_replace("{MOBILE}", $data['mobile'], $email);
                    $cover_letter = str_replace("{COVER_LETTER}", $data['cover_letter'], $mobile);
                    $Link = str_replace("{LINK}", base_url() . 'admin/job_circular/view_jobs_application/' . $job_appliactions_id, $cover_letter);
                    $message = str_replace("{SITE_NAME}", config_item('company_name'), $Link);
                    $data['message'] = $message;
                    $message = $this->load->view('email_template', $data, TRUE);

                    $params['subject'] = $subject;
                    $params['message'] = $message;
                    $params['resourceed_file'] = '';
                    $params['recipient'] = $user_info->email;
                    $this->job_circular_model->send_email($params);
                }
                $notifyUser = array($user_info->user_id);
            }
            if (!empty($notifyUser)) {
                foreach ($notifyUser as $v_user) {
                    add_notification(array(
                        'to_user_id' => $v_user,
                        'description' => 'not_new_job_application',
                        'icon' => 'globe',
                        'link' => 'admin/job_circular/view_jobs_application/' . $job_appliactions_id,
                        'value' => lang('by') . ' ' . $data['name'],
                    ));
                }
            }
            if (!empty($notifyUser)) {
                show_notification($notifyUser);
            }
        }
        // messages for user
        $type = "success";
        $message = lang('job_application_submitted');
        set_message($type, $message);
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('frontend');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }

    }

    public function jobs_posted_pdf($id)
    {
        $data['job_posted'] = $this->db->where('job_circular_id', $id)->get('tbl_job_circular')->row();

        $this->load->helper('dompdf');
        $view_file = $this->load->view('admin/job_circular/jobs_posted_pdf', $data, true);
        pdf_create($view_file, slug_it(lang('jobs_posted') . '- ' . $data['job_posted']->job_title));
    }

    public function view_invoice($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        if (!empty($data['invoice_info'])) {
            $data['client_info'] = $this->invoice_model->check_by(array('client_id' => $data['invoice_info']->client_id), 'tbl_client');

            $lang = $this->invoice_model->all_files();
            foreach ($lang as $file => $altpath) {
                $shortfile = str_replace("_lang.php", "", $file);
                //CI will record your lang file is loaded, unset it and then you will able to load another
                //unset the lang file to allow the loading of another file
                if (isset($this->lang->is_loaded)) {
                    $loaded = sizeof($this->lang->is_loaded);
                    if ($loaded < 3) {
                        for ($i = 3; $i <= $loaded; $i++) {
                            unset($this->lang->is_loaded[$i]);
                        }
                    } else {
                        for ($i = 0; $i <= $loaded; $i++) {
                            unset($this->lang->is_loaded[$i]);
                        }
                    }
                }
                if (!empty($data['client_info']->language)) {
                    $language = $data['client_info']->language;
                } else {
                    $language = 'english';
                }
                $data['language_info'] = $this->lang->load($shortfile, $language, TRUE, TRUE, $altpath);
            }
        } else {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }

        $data['subview'] = $this->load->view('frontend/invoice/invoice_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function estimates($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['estimates_info'] = $this->estimates_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        $data['client_info'] = $this->estimates_model->check_by(array('client_id' => $data['estimates_info']->client_id), 'tbl_client');
        if (empty($data['estimates_info'])) {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }
        $lang = $this->invoice_model->all_files();
        foreach ($lang as $file => $altpath) {
            $shortfile = str_replace("_lang.php", "", $file);
            //CI will record your lang file is loaded, unset it and then you will able to load another
            //unset the lang file to allow the loading of another file
            if (isset($this->lang->is_loaded)) {
                $loaded = sizeof($this->lang->is_loaded);
                if ($loaded < 3) {
                    for ($i = 3; $i <= $loaded; $i++) {
                        unset($this->lang->is_loaded[$i]);
                    }
                } else {
                    for ($i = 0; $i <= $loaded; $i++) {
                        unset($this->lang->is_loaded[$i]);
                    }
                }
            }
            if (!empty($data['client_info']->language)) {
                $language = $data['client_info']->language;
            } else {
                $language = 'english';
            }
            $data['language_info'] = $this->lang->load($shortfile, $language, TRUE, TRUE, $altpath);
        }

        $data['subview'] = $this->load->view('frontend/estimate/estimates_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function proposals($id)
    {
        $data['title'] = lang('invoice_details');
        $id = url_decode($id);
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            set_message('error', 'No data Found');
            redirect('frontend/');
        }
        $data['subview'] = $this->load->view('frontend/proposals/proposals_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data);
    }

    public function pdf_invoice($id)
    {
        $data['title'] = "Invoice PDF"; //Page title
        // get all invoice info by id
        $data['invoice_info'] = $this->invoice_model->check_by(array('invoices_id' => $id), 'tbl_invoices');
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/invoice/invoice_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('Invoice  # ' . $data['invoice_info']->reference_no));
    }

    public function pdf_estimates($id)
    {
        $data['estimates_info'] = $this->invoice_model->check_by(array('estimates_id' => $id), 'tbl_estimates');
        $data['title'] = "Estimates PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/estimate/estimates_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('estimate') . '# ' . $data['estimates_info']->reference_no));
    }

    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        $data['title'] = lang('proposal') . "PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('frontend/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it(lang('proposals') . '# ' . $data['proposals_info']->reference_no));
    }

    public function knowledgebase()
    {
        $data['title'] = lang('knowledgebase');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        $data['subview'] = $this->load->view('frontend/kb/kb_list', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data); //page load
    }

    public function kb_details($type, $id)
    {
        $data['title'] = lang('articles') . ' ' . lang('details');
        $data['all_kb_category'] = get_result('tbl_kb_category', array('type' => 'kb', 'status' => 1));
        if ($type == 'articles') {
            $this->kb_model->increase_total_view($id);
            $data['articles_info'] = $this->kb_model->get_kb_info('articles', $id, true);
        } else {
            $data['articles_by_category'] = $this->kb_model->get_kb_info('category', $id, true);
        }
        $data['subview'] = $this->load->view('frontend/kb/articles_details', $data, TRUE);
        $this->load->view('frontend/_layout_main', $data); //page load
    }

    public function kb_download($id, $fileName)
    {
        if (!empty($fileName)) {
            $this->load->helper('download');
            if ($id) {
                $down_data = file_get_contents('uploads/' . $fileName); // Read the file's contents
                force_download($fileName, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('frontend');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        }
    }

    function get_article_suggestion()
    {
        $search = $this->input->post("search", true);
        if ($search) {
            $result = $this->kb_model->get_suggestions($search, true);
            echo json_encode($result);
            exit();
        }
    }


}
