<?php

namespace IZaL\Tap;

interface Billing
{
    /**
     * Set Merchant Information
     * @param array $options
     * @return mixed
     * for more info check (https://www.tap.company/developers/)
     */
    public function setMerchant(array $options);

    /**
     * Set Customer Information
     * @param array $options
     * @return mixed
     * for more info check (https://www.tap.company/developers/)
     */
    public function setCustomer(array $options);

    /**
     * Set Customer Information
     * @param array $options
     * @return mixed
     * for more info check (https://www.tap.company/developers/)
     */
    public function setProducts(array $options);

    /**
     * Set Gateway Information
     * @param array $options
     * @return mixed
     * for more info check (https://www.tap.company/developers/)
     */
    public function setGateway(array $options);

    /**
     * Perform Payment
     * for more info check (https://www.tap.company/developers/)
     */
    public function requestPayment();


    public function getCustomerInfo();

    public function setPaymentURL($url);

}