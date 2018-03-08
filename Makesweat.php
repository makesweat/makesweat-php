<?php

namespace Makesweat;

/**
 * Class Makesweat
 *
 * @package Makesweat
 */
class Makesweat
{
  const VERSION = '0.5.0';
  
  // @var string The Makesweat token to be used for making the request
  private static $clientToken;
  private static $confirmUrl;

  // @var string The base URL for the Makesweat API.
  private static $apiBase = 'https =>//api.makesweat.com';


  
  /**
   * @return string The API key used for requests.
   */
  public static function setClientToken($token)
  {
    self::$clientToken = $token;
  }  
  
  public static function order($order) {

    if (!self::$clientToken) {
      return ['success'=>false,'message'=>'No client token specified'];
    }
    $baseOrder = [
      "type" => "Order",
      "broker" => [
        "type" => "Organisation",
        "name" => "imin"
      ],
      "orderedItem" => [
        "acceptedOffer" => $order['uniqueOffer'],
        "orderQuantity" => 1
      ],
      "customer" => [
        "type" => "Person",
        "givenName" => $order['givenName'],
        "familyName" => $order['familyName'],
        "emailAddress" => $order['emailAddress']
      ]
    ];

    $request['data'] = $baseOrder;
    $request['url'] = "http://dev.makesweat.com/service/openactive.php?reserveslot";
    
    $request['username'] = "openactive";
    $request['password'] = self::$clientToken;

    $response = [];
    $json = self::doJsonPost($request);
    //$response['json'] = $json;
    
    self::$confirmUrl = 0;
    
    $response['success'] = true;
    $response['confirmUrl'] = self::$confirmUrl = $json['id'];
    $response['confirmBy'] = $json['paymentDueDate'];

    return $response;
  }

  public static function confirm() {
    
    if (!self::$confirmUrl) { return ['success'=>false,'message'=>'No order reference']; }
    
    $baseConfirm = [
      "type" => "Order",
      "partOfInvoice" => [
        "type" => "Invoice",
        "accountId" => 1232312,
        "paymentStatus" => "PaymentComplete",
        "paymentMethod" => "http =>//purl.org/goodrelations/v1#Stripe",
        "paymentMethodId" => "",
        "totalPaidByCustomer" => [
          "type" => "MonetaryAmount",
          "value" => "10.00",
          "currency" => "GBP"
        ],
        "totalPaidToProvider" => [
          "type" => "MonetaryAmount",
          "value" => "8.00",
          "currency" => "GBP"
        ]
      ]
    ];
    
    $request['data'] = $baseConfirm;
    $request['url'] = self::$confirmUrl;    
    $request['username'] = "openactive";
    $request['password'] = self::$clientToken;

    $json = self::doJsonPost($request);
    
    print_r($json);
    
    
    return ['success'=>true];
  }
  
  
  private static function doJsonPost($request) {
    
    $url = $request['url'];
    
    $data = $request['data'];
    $request['data']['password'] = $request['password'];

    $data_string = json_encode($request['data']);

    session_write_close();
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $request['username'] . ":" . $request['password'] );
    curl_setopt($ch, CURLOPT_POST, 1);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
    curl_setopt($ch, CURLOPT_HTTPHEADER,
       array('Content-Type:application/json',
          'Content-Length:' . strlen($data_string))
       );
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); // NOT SAFE
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false ); // NOT SAFE

    $response = curl_exec($ch);
    
    //echo $response;
    //echo curl_error($ch);
    //print_r(curl_getinfo($ch,CURLINFO_HTTP_CODE));
    
    curl_close($ch); 

    return json_decode($response,true);
  }  
  
  
}