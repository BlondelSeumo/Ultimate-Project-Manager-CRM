<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App_gateway
{
    /**
     * Hold Codeigniter instance
     * @var object
     */
    protected $ci;

    /**
     * Stores the gateway id
     * @var alphanumeric
     */
    protected $id = '';

    /**
     * Gateway name
     * @var mixed
     */
    protected $name = '';

    /**
     * All gateway settings
     * @var array
     */
    protected $settings = [];

    /**
     * Must be called from the main gateway class that extends this class
     * @param alphanumeric $id Gateway id - required
     * @param mixed $name Gateway name
     */
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Set gateway name
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Return gateway name
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set gateway id
     * @param string alphanumeric $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Return gateway id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add payment based on payment method
     * @param array $data payment data
     * Params
     * amount - Required
     * invoiceid - Required
     * transactionid - Optional but recommended
     * paymentmethod - Optional
     * note - Optional
     */
    public function addPayment($invoices_id, $amount, $trans_id = null, $gateway = null)
    {
        $this->ci->load->model('payments_model');
        return $this->ci->payments_model->addPayment($invoices_id, $amount, $trans_id, $gateway);
    }

}

