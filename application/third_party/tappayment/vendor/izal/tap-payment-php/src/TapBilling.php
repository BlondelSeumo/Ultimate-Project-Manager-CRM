<?php
namespace IZaL\Tap;

use GuzzleHttp\Client;
use IZaL\Tap\Exceptions\InvalidArgumentException;
use IZaL\Tap\Exceptions\KeyMissingException;

class TapBilling implements Billing
{
    protected $ApiKey;
    protected $MerchantID;
    protected $UserName;
    protected $Password;
    protected $ErrorURL;
	protected $PaymentURL = 'https://www.gotapnow.com/TapWebConnect/Tap/WebPay/PaymentRequest';
    protected $PaymentOption = 'ALL';
    protected $AutoReturn = 'Y';
    protected $CurrencyCode = 'KWD';
    protected $LangCode = 'AR';
    protected $TotalAmount = 0;

    protected $customerInfo = [];
    protected $productInfo = [];
    protected $gatewayInfo = [];
    protected $merchantInfo = [];

    public $response = '';

    protected $requiredConstructorKeys = ['ApiKey','MerchantID','UserName','Password'];

    public function __construct(array $options = [])
    {

        foreach($options as $key => $val) {
            $this->{$key} = $val;
        }

        foreach($this->requiredConstructorKeys as $requiredField) {
            if(empty($this->{$requiredField})) {
                throw new KeyMissingException($requiredField .' key is missing');
            }
        }

    }

    public function setCustomer(array $options)
    {

        $this->checkForMissingKeys(['Name','Email','Mobile'], $options);

        $this->customerInfo = $options;
    }

    public function setProducts(array $options)
    {

        foreach($options as $option) {
            $this->checkForMissingKeys(['Quantity','TotalPrice','UnitDesc','UnitName','UnitPrice'], $option);
        }

        $this->productInfo = $options;

    }

    public function setGateway(array $options)
    {
        $this->checkForMissingKeys(['Name'], $options);
        $this->gatewayInfo = $options;
    }

    public function setMerchant(array $options)
    {

        $this->merchantInfo = $options;

        if(!array_key_exists('ReferenceID',$options)) {
            $this->merchantInfo['ReferenceID'] = uniqid();
        }
    }

    private function setTotalAmount($amount)
    {
        $this->TotalAmount = $amount;
    }

    /**
     * @return array
     */
    public function getCustomerInfo()
    {
        return $this->customerInfo;
    }

    /**
     * @return array
     */
    public function getProductInfo()
    {
        return $this->productInfo;
    }

    /**
     * Calculates the Total Amount of The Transaction
     * @return mixed
     */
    public function getTotalAmount()
    {
        $sum = array_reduce($this->productInfo, function($i, $obj)
        {
            return $i + $obj['TotalPrice'];
        });

        return $sum;
    }

    /**
     * @return array
     */
    public function getMerchantInfo()
    {
        return $this->merchantInfo;
    }

    public function requestPayment()
    {
        $client = new Client();

        $merchantInfo = $this->buildMerchant();
        $gatewayInfo = $this->buildGateway();
        $customerInfo = $this->customerInfo;
        $productInfo = $this->productInfo;

        $payload = [
            'MerMastDC' => $merchantInfo,
            'CustomerDC' => $customerInfo,
            'lstProductDC' => $productInfo,
            'lstGateWayDC' => $gatewayInfo,
        ];

        $request = $client->request('POST', $this->PaymentURL,
            [
                'body'        => json_encode($payload),
                'headers' => [
                    'content-type' => 'application/json'
                ]
            ]);

        $this->response = new TapBillingResponse($request);

        return $this;
    }

    private function buildMerchant() {

        $hashString = $this->generateHash();

        $merchant = array_merge($this->merchantInfo,[
            'MerchantID' => $this->MerchantID,
            'UserName' => $this->UserName,
            'Password' => $this->Password,
            'AutoReturn' => $this->AutoReturn,
            'LangCode' => $this->LangCode,
            'ReferenceID' => $this->merchantInfo['ReferenceID'],
            'ReturnURL' => $this->merchantInfo['ReturnURL'],
            'HashString' => $hashString
        ]);

        return $merchant;
    }

    private function buildGateway() {

        if(!array_key_exists('Name',$this->gatewayInfo)) {
            $this->gatewayInfo['Name'] = $this->PaymentOption;
        }

        return $this->gatewayInfo;
    }


    private function generateHash()
    {
        $apiReqURL = '';

        $totalAmount = $this->getTotalAmount();

        $this->setTotalAmount($totalAmount);

        $apiConf = [
            'X_MerchantID' => $this->MerchantID,
            'X_UserName' =>$this->UserName,
            'X_ReferenceID' => $this->merchantInfo['ReferenceID'] ,
            'X_CurrencyCode' => $this->CurrencyCode,
            'X_Total' => $this->TotalAmount,
            'X_Mobile' => $this->customerInfo['Mobile'],
        ];

        foreach ($apiConf as $key => $val) {
            $apiReqURL .= $key.$val;
        }

        $hashedString = hash_hmac('sha256', $apiReqURL, $this->ApiKey);

        return $hashedString;
    }

    private function checkForMissingKeys(array $requiredKeys, array $options)
    {

        if(!is_array($options)) {
            throw new InvalidArgumentException('Parameters Passed should be an array');
        }

        foreach($requiredKeys as $requiredField) {
            if(!array_key_exists($requiredField,$options)) {
                throw new KeyMissingException($requiredField .' Key is missing from customer info');
            }
        }
    }

    public function setPaymentURL($url)
    {
        return $this->PaymentURL = $url;
    }
}