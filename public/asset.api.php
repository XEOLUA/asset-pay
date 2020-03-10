<?php
//phpinfo();

require_once "dd.php";

class AssetPayments
{
    ///merchant secret key
    var $secretKey;
    ///merchant guid
    var $guid;
    ///protocol
    var  $protocol;
    ///host
    var  $host;
    //port
    var $port;
    ///api server url
    var $serverUrl;

    //sends request to app server
    protected function SendRequest($jsonData, $uri)
    {
        $curl = curl_init();
        if (!$curl)
            return false;

//dd::outdata($this->serverUrl.$uri,0);

//dd::outdata($jsonData,0);

        curl_setopt($curl, CURLOPT_URL, $this->serverUrl.$uri);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,array('Expect: ', 'Content-Type: application/json; charset=UTF-8', 'Content-Length: '.strlen($jsonData)));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
        $response = curl_exec($curl);
        curl_close($curl);
//        dd::outdata($response,0);
        return $response;
    }

    //Returns merchant information with payment systems
    public function GetMerchantInfo()
    {
        $sign = strtoupper($this->guid .';'. $this->secretKey.';');
        //var_dump($sign);
        $request = array(
            'MerchantGuid' => $this->guid,
            'Signature' => $this->hashSignature($sign)
        );
        $response = $this->SendRequest(json_encode($request), 'GetMerchantInformation');
        return json_decode($response, true);
    }

    ///Hash signature
    public function hashSignature ($signature) {
        return hash_hmac('md5',$signature,strtoupper( $this->secretKey));
    }

    //Constructor
    public function AssetPayments($merchantSecretKey, $merchantGuid)
    {
        $this->protocol = 'https://';
        $this->host = 'api.assetpayments.us';            
        $this->port = '';
            
        $this->serverUrl = $this->protocol . $this->host . ($this->port != '' ? ':' . $this->port : '') . '/api/payment/';
        $this->guid = $merchantGuid;
        $this->secretKey = strtoupper($merchantSecretKey);
    }

    //Create payment request
    function CreatePayment($request)
    {        
        $signature = $this->guid .':' .
            $request['TransactionId'] . ':' .
            strtoupper($this->secretKey);

        $request['Signature'] = $this->hashSignature($signature);
        $response = $this->SendRequest(json_encode($request), 'Create');
        return json_decode($response, true);
    }

    //Get status response
    function getStatusResponse($requestPostRawDataString)
    {
        $request = json_decode($requestPostRawDataString);
        $signature = $this->guid .':' .
            $request['TransactionId'] . ':' .
            strtoupper($this->secretKey);
        $signature = hashSignature($signature);

        if ($request['SignatureEx'] == $signature){
            return $request;
        } else {
            throw new Exception('Invalid signature! Request: '.$requestPostRawDataString);
        }
    }

}

    //Sample create payment
	
	$merchantGuid = '03e5515f-7cd8-49ce-9284-5d78ff1390d9';
	$mechantSecretkey = 'b6e6b617-88b0-4102-8f8f-704327fa6d9f'; 
	
	//Catch form data 	
	$form_name = $_POST['form_name'];
	$form_phone = '+'.$_POST['form_phone'];
	$form_email = $_POST['form_email'];
	$form_address = $_POST['form_address'];
	$form_description = $_POST['form_description'];
	$form_sum = number_format($_POST['form_sum'], 2, '.', '');	
	//$form_paysystem = $_POST['form_paysystem'];	
	$currency = $_POST['form_currency'];	
	$processingid = $_POST['form_processingid'];	

    $asset = new AssetPayments($mechantSecretkey,$merchantGuid);

//Creates payment request
    $requestCreatePayment = Array(
		'ProcessingId' => $processingid, // Required, 
		'TemplateId' => 0,		        
		'OperationMode' => 'Iframe',
		'TransactionType' => 'Sale', //Required
		'MerchantInternalOrderId' => '155',
                'MerchantInternalUserId' => '12',
		'FirstName' => $form_name,
                'LastName' => 'Surname',
                'Phone' => $form_phone,
		'Email' => $form_email,  // Required				
		'Address' => $form_address,
		'Zone' => 'Zone',
		'City' => 'City',
		'Region' => 'Region',
                'State' => 'State',
                'ZIP' => '41341',
		'CountryIso' => 'UA', // Required
		'ConvertText' => 0,
		'StatusUrl' => 'http://xeol.com.ua/callback.php',
                'ReturnUrl' => 'http://xeol.com.ua/return.php',
		'DynamicDescriptor' => 'test payment',
		'CustomMerchantInfo' => $form_description,
		'Amount' => $form_sum, // Required
		'Currency' => $currency,  // Required
                'AssetPaymentsKey' => $merchantGuid,  // Required               
		'IpAddress' => '10.10.10.10',
			);
    $result = $asset->CreatePayment($requestCreatePayment);

//    echo "<pre>";
//    print_r($result);
//    echo "</pre>";
//    exit();

	$externalForm  = $result['htmlIframeForm']; // External form to redirect user. This form should be showed on user web page
    $OrderId = $result['transactionId']; //Order uniq id

    echo $externalForm;

