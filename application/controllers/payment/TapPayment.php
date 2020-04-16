<?php
require_once(APPPATH . 'third_party/tappayment/vendor/autoload.php');

class TapPayment extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_model');
    }

    public function pay($invoice_id = null)

    {
        $data['title'] = lang('tapPayment');
        $data['breadcrumbs'] = lang('tapPayment');
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $client_info = $this->db->where('client_id', $invoice_info->client_id)->get('tbl_client')->row();
        $invoice_due = $this->invoice_model->calculate_to('invoice_due', $invoice_id);
        if ($invoice_due <= 0) {
            $invoice_due = 0.00;
        }
        $posted = [];
        //$paymentURL = $response->PaymentURL;
        foreach ($this->input->post() as $key => $value) {
            $posted[$key] = $value;
        }
        $data['action_url'] = $this->uri->uri_string();
        $data['invoice_id'] = $invoice_id;
        $data['amount'] = $invoice_due;
        if ($this->input->post()) {
            $allow_customer_edit_amount = config_item('allow_customer_edit_amount');
            if (!empty($allow_customer_edit_amount) && $allow_customer_edit_amount == 'Yes') {
                $data['amount'] = $posted['amount'];
            } else {
                $data['amount'] = $invoice_due;
            }
            $data['currency'] = $posted['currency'];
            // $total_amount = convert_currency($data['currency'], $data['amount']);

            $config =
                [
                    'ApiKey' => config_item('tap_api_key'),
                    'UserName' => config_item('tap_user_name'),
                    'Password' => config_item('tap_password'),
                    'MerchantID' => config_item('tap_merchantID'),
                    'ErrorURL' => base_url('404'),// optional. default(NULL)
                    'PaymentOption' => 'ALL', // optional. default (ALL)
                    'AutoReturn' => 'Y', // optional. default (Y)
                    'CurrencyCode' => 'KWD', // optional. default (KWD)
                    'LangCode' => 'EN',// optional. default(AR)
                ];

            if (!empty($client_info)) {
                $client_email = $client_info->email;
                $client_name = $client_info->name;
                $address = $client_info->address;
                $city = $client_info->city;
                $zipcode = $client_info->zipcode;
                $country = $client_info->country;
                $phone = $client_info->phone;
            } else {
                $client_email = '-';
                $client_name = '-';
                $address = '-';
                $city = '-';
                $zipcode = '-';
                $country = '-';
                $phone = '-';
            }
            $customer =
                [
                    'Email' => $client_email,
                    "Mobile" => $phone,
                    "Name" => $client_name,
                    "Nationality" => $country,
                    "Street" => $address,
                    "Area" => $city,
                ];

            $products =
                [
                    [
                        "CurrencyCode" => 'KWD',
                        "Quantity" => 1,
                        "TotalPrice" => $data['amount'],
                        "UnitDesc" => $invoice_info->reference_no,
                        "UnitID" => $invoice_info->invoices_id,
                        "UnitName" => $invoice_info->reference_no,
                        "UnitPrice" => $data['amount'],
                        "VndID" => ""
                    ]
                ];
            $gateway = ['Name' => 'ALL'];

            $merchant =
                [
                    'ReturnURL' => base_url('payment/TapPayment/PaymentStatus/'),
                    'ReferenceID' => $invoice_info->invoices_id,
                ];

// request for payment url
            try {
                $billing = new IZaL\Tap\TapBilling($config);

                $billing->setProducts($products);
                $billing->setCustomer($customer);
                $billing->setGateway($gateway);
                $billing->setMerchant($merchant);

                $paymentRequest = $billing->requestPayment();
                $response = $paymentRequest->response->getRawResponse();
                $paymentURL = $response->PaymentURL;
                if (!empty($paymentURL)) {
                    redirect($paymentURL);
                } else {
                    set_message('error', $response->ResponseMessage);
                    redirect($_SERVER['HTTP_REFERER']);
                }

            } catch (\Exception $e) {
                set_message('error', $e->getMessage());
                if (!empty($invoice_info)) {
                    $url = 'client/invoice/manage_invoice/invoice_details/' . $invoice_info->invoices_id;
                } else {
                    $url = 'client/dashboard';
                }
                if (!empty(client_id())) {
                    redirect($url);
                } else {
                    redirect('frontend/view_invoice/' . url_encode($invoice_info->invoices_id));
                }
//            var_dump($e->getMessage());
                // do something with the error
            }
        }
        $data['subview'] = $this->load->view('payment/tappayment', $data, FALSE);
        $this->load->view('client/_layout_modal', $data);

    }


    public function PaymentStatus()
    {
        $invoice_id = $_GET['trackid'];
        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        $amt = $_GET['amt'];
        $total_amount = convert_currency($invoice_info->currency, $amt);
        $ref = $_GET['ref'];
        $result = $_GET['result'];
        if ($result == 'SUCCESS') {
            $result = $this->tap_gateway->addPayment($invoice_id, $total_amount, $ref, '109');
            if ($result['type'] == 'success') {
                set_message($result['type'], $result['message']);
            } else {
                set_message($result['type'], $result['message']);
            }
        } else {
            set_message('warning', 'Thank You. Your transaction status is ' . $result);
        }

        $invoice_info = $this->invoice_model->check_by(array('invoices_id' => $invoice_id), 'tbl_invoices');
        if (!empty($invoice_info)) {
            $url = 'client/invoice/manage_invoice/invoice_details/' . $invoice_id;
        } else {
            $url = 'client/dashboard';
        }
        if (!empty($client_id)) {
            redirect($url);
        } else {
            redirect('frontend/view_invoice/' . url_encode($invoice_id));
        }
        if (!empty($client_id)) {
            redirect('client/dashboard');
        } else {
            redirect('frontend/view_invoice/' . url_encode($invoice_id));
        }
    }

}
