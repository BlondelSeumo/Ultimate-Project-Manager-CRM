<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proposals extends Client_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposal_model');
        $this->load->library('gst');

        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function index($action = NULL, $id = NULL, $item_id = NULL)
    {

        $data['page'] = lang('sales');
        $data['breadcrumbs'] = lang('proposals');
        if (!empty($item_id)) {
            $data['item_info'] = $this->proposal_model->check_by(array('proposals_items_id' => $item_id), 'tbl_proposals_items');
        }
        if ($action == 'client' || $action == 'leads') {
            $data['module_id'] = $id;
            $data['module'] = $action;
            $data['active'] = 2;
        } else {
            $data['active'] = 1;
        }
        if (!empty($id)) {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $client_id = client_id();
            if ($data['proposals_info']->module != 'client') {
                redirect('client/proposals');
            } else {
                if ($client_id != $data['proposals_info']->module_id) {
                    redirect('client/proposals');
                }
            }

        }
        // get all client
        $this->proposal_model->_table_name = 'tbl_client';
        $this->proposal_model->_order_by = 'client_id';
        $data['all_client'] = $this->proposal_model->get();
        // get permission user
        $data['permission_user'] = $this->proposal_model->all_permission_user('14');

        if ($action == 'proposals_details') {
            $data['title'] = lang('proposals') . ' ' . lang('details'); //Page title
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                set_message('error', 'No data Found');
                redirect('client/proposals');
            }
            $subview = 'proposals_details';
        } elseif ($action == 'proposals_history') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "proposals History"; //Page title
            $subview = 'proposals_history';
        } elseif ($action == 'email_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "Email proposals"; //Page title
            $subview = 'email_proposals';
            $data['editor'] = $this->data;
        } elseif ($action == 'pdf_proposals') {
            $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
            if (empty($data['proposals_info'])) {
                redirect('client/proposals');
            }
            $data['title'] = "proposals PDF"; //Page title
            $this->load->helper('dompdf');
            $viewfile = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
            pdf_create($viewfile, slug_it('proposals  # ' . $data['proposals_info']->reference_no));
        } else {
            $data['title'] = lang('proposals'); //Page title
            $subview = 'proposals';
        }
        $data['subview'] = $this->load->view('client/proposals/' . $subview, $data, TRUE);
        $this->load->view('client/_layout_main', $data); //page load
    }

    public function proposalsList($filterBy = null)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_proposals';
            $this->datatables->column_order = array('reference_no', 'status', 'proposal_date', 'due_date');
            $this->datatables->column_search = array('reference_no', 'status', 'proposal_date', 'due_date');
            $this->datatables->order = array('proposals_id' => 'desc');
            $where_in = null;
            $where = array('status !=' => 'draft', 'module' => 'client', 'module_id' => $this->session->userdata('client_id'));
            // get all invoice
            if ($filterBy == 'last_month' || $filterBy == 'this_months') {
                if ($filterBy == 'last_month') {
                    $month = date('Y-m', strtotime('-1 months'));
                } else {
                    $month = date('Y-m');
                }
                $where = array('status !=' => 'draft', 'module' => 'client', 'module_id' => $this->session->userdata('client_id'), 'proposal_month' => $month);

            } else if ($filterBy == 'expired') {
                $where = array('status' => 'pending', 'module' => 'client', 'module_id' => $this->session->userdata('client_id'), 'UNIX_TIMESTAMP(due_date) <' => strtotime(date('Y-m-d')));

            } else if (!empty($filterBy)) {
                $where = array('module' => 'client', 'module_id' => $this->session->userdata('client_id'), 'status' => $filterBy);
            }
            $fetch_data = $this->datatables->get_client_proposals($filterBy);

            $data = array();
            foreach ($fetch_data as $_key => $v_proposals) {
                $action = null;
                if ($v_proposals->status == 'pending') {
                    $label = "info";
                } elseif ($v_proposals->status == 'accepted') {
                    $label = "success";
                } else {
                    $label = "danger";
                }

                $sub_array = array();
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'client/proposals/index/proposals_details/' . $v_proposals->proposals_id . '">' . $v_proposals->reference_no . '</a>';
                if ($v_proposals->convert == 'Yes') {
                    if ($v_proposals->convert_module == 'invoice') {
                        $c_url = base_url() . 'client/invoice/manage_invoice/invoice_details/' . $v_proposals->convert_module_id;
                        $text = lang('invoiced');
                    } else {
                        $text = lang('estimated');
                        $c_url = base_url() . 'client/estimates/index/estimates_details/' . $v_proposals->convert_module_id;
                    }
                    if (!empty($c_url)) {
                        $name .= '<p class="text-sm m0 p0"><a class="text-success" href="' . $c_url . '">' . $text . '</a></p>';
                    }
                }
                $sub_array[] = $name;
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_proposals->proposal_date));
                $overdue = null;
                if (strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == 'pending' || strtotime($v_proposals->due_date) < strtotime(date('Y-m-d')) && $v_proposals->status == ('draft')) {
                    $overdue .= '<span class="label label-danger ">' . lang("expired") . '</span>';
                }
                $sub_array[] = strftime(config_item('date_format'), strtotime($v_proposals->due_date)) . ' ' . $overdue;
                $sub_array[] = display_money($this->proposal_model->proposal_calculation('total', $v_proposals->proposals_id), client_currency($v_proposals->module_id));
                $sub_array[] = "<span class='label label-" . $label . "'>" . lang($v_proposals->status) . "</span>";

                $sub_array[] = $action;
                $data[] = $sub_array;
            }

            render_table($data, $where);
        } else {
            redirect('client/dashboard');
        }
    }

    public function pdf_proposals($id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $client_id = client_id();
        if ($data['proposals_info']->module != 'client') {
            redirect('client/proposals');
        } else {
            if ($client_id != $data['proposals_info']->module_id) {
                redirect('client/proposals');
            }
        }
        $data['title'] = "proposals PDF"; //Page title
        $this->load->helper('dompdf');
        $viewfile = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
        pdf_create($viewfile, slug_it('proposals  # ' . $data['proposals_info']->reference_no));
    }

    public function change_status($action, $id)
    {
        $where = array('proposals_id' => $id);
        if ($action == 'hide') {
            $data = array('show_client' => 'No');
        } elseif ($action == 'show') {
            $data = array('show_client' => 'Yes');
        } elseif ($action == 'sent') {
            $data = array('emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()), 'status' => 'sent');
        } elseif (!empty($action)) {
            $data = array('status' => $action);
        } else {
            $data = array('show_client' => 'Yes');
        }
        $this->proposal_model->set_action($where, $data, 'tbl_proposals');
        // messages for user
        $type = "success";
        $message = lang('proposals_status', $action);
        set_message($type, $message);
        redirect('client/proposals/index/proposals_details/' . $id);

    }

    public function send_proposals_email($proposals_id, $row = null)
    {
        if (!empty($row)) {
            $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
            $email_template = email_templates(array('email_group' => 'proposal_email'));
            if ($proposals_info->module == 'client') {
                $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
                $client = $client_info->name;
                $currency = $this->proposal_model->client_currency_symbol($proposals_info->module_id);
                $email_template = email_templates(array('email_group' => 'proposal_email'), $proposals_info->module_id);
            } else if ($proposals_info->module == 'leads') {
                $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
                $client = $client_info->lead_name;
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            } else {
                $client = '-';
                $currency = $this->proposal_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
            }

            $amount = $this->proposal_model->proposal_calculation('total', $proposals_info->proposals_id);
            $currency = $currency->code;

            $message = $email_template->template_body;
            $ref = $proposals_info->reference_no;
            $subject = $email_template->subject;
        } else {
            $message = $this->input->post('message', TRUE);
            $ref = $this->input->post('ref', TRUE);
            $subject = $this->input->post('subject', TRUE);
            $client = $this->input->post('client_name', TRUE);
            $amount = $this->input->post('amount', true);
            $currency = $this->input->post('currency', TRUE);
        }

        $client_name = str_replace("{CLIENT}", $client, $message);
        $Ref = str_replace("{PROPOSAL_REF}", $ref, $client_name);
        $Amount = str_replace("{AMOUNT}", $amount, $Ref);
        $Currency = str_replace("{CURRENCY}", $currency, $Amount);
        $link = str_replace("{PROPOSAL_LINK}", base_url() . 'client/proposals/index/proposals_details/' . $proposals_id, $Currency);
        $message = str_replace("{SITE_NAME}", config_item('company_name'), $link);

        $this->send_email_proposals($proposals_id, $message, $subject); // Email proposals

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-envelope',
            'value1' => $ref
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('client/proposals/index/proposals_details/' . $proposals_id);
    }

    function send_email_proposals($proposals_id, $message, $subject)
    {
        $proposals_info = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if ($proposals_info->module == 'client') {
            $client_info = $this->proposal_model->check_by(array('client_id' => $proposals_info->module_id), 'tbl_client');
            $email = $client_info->email;
        } else if ($proposals_info->module == 'leads') {
            $client_info = $this->proposal_model->check_by(array('leads_id' => $proposals_info->module_id), 'tbl_leads');
            $email = $client_info->email;
        } else {
            $email = '-';
        }
        $recipient = $email;

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message
        );
        $params['resourceed_file'] = 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';
        $params['resourcement_url'] = base_url() . 'uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf';

        $this->attach_pdf($proposals_id);

        $this->proposal_model->send_email($params);
        //Delete estimate in tmp folder
        if (is_file('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf')) {
            unlink('uploads/' . lang('proposal') . '_' . $proposals_info->reference_no . '.pdf');
        }
    }

    public function attach_pdf($id)
    {
        $data['page'] = lang('proposals');
        $data['sortable'] = true;
        $data['typeahead'] = true;
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $data['title'] = lang('proposals'); //Page title
        $this->load->helper('dompdf');
        $html = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);
        $result = pdf_create($html, slug_it(lang('proposal') . '_' . $data['proposals_info']->reference_no), 1, null, true);
        return $result;
    }

    function proposals_email($proposals_id)
    {
        $data['proposals_info'] = $this->proposal_model->check_by(array('proposals_id' => $proposals_id), 'tbl_proposals');
        if (empty($data['proposals_info'])) {
            redirect('client/proposals');
        }
        $proposals_info = $data['proposals_info'];
        $client_info = $this->proposal_model->check_by(array('client_id' => $data['proposals_info']->client_id), 'tbl_client');

        $recipient = $client_info->email;

        $message = $this->load->view('client/proposals/proposals_pdf', $data, TRUE);

        $data['message'] = $message;

        $message = $this->load->view('email_template', $data, TRUE);
        $params = array(
            'recipient' => $recipient,
            'subject' => '[ ' . config_item('company_name') . ' ]' . ' New proposals' . ' ' . $data['proposals_info']->reference_no,
            'message' => $message
        );
        $params['resourceed_file'] = '';

        $this->proposal_model->send_email($params);

        $data = array('status' => 'sent', 'emailed' => 'Yes', 'date_sent' => date("Y-m-d H:i:s", time()));

        $this->proposal_model->_table_name = 'tbl_proposals';
        $this->proposal_model->_primary_key = 'proposals_id';
        $this->proposal_model->save($data, $proposals_id);

        // Log Activity
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'proposals',
            'module_field_id' => $proposals_id,
            'activity' => 'activity_proposals_sent',
            'icon' => 'fa-envelope',
            'value1' => $proposals_info->reference_no
        );
        $this->proposal_model->_table_name = 'tbl_activities';
        $this->proposal_model->_primary_key = 'activities_id';
        $this->proposal_model->save($activity);

        $type = 'success';
        $text = lang('proposals_email_sent');
        set_message($type, $text);
        redirect('client/proposals/index/proposals_details/' . $proposals_id);
    }

}
