<?php
/**
 * API : oauth/access_token
 * 
 * @see https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/v2.3
 * @see https://developers.facebook.com/docs/facebook-login/access-tokens
 */
class ApiOauthAccessToken
{
  const API_URI = "https://graph.facebook.com/oauth/access_token";
  
  const AOA_ERROR_PARAMS_EMPTY     = "Api oauth/access_token empty params.";
  const AOA_REQUIRED_CLIENT_ID     = "Api oauth/access_token required client_id.";
  const AOA_REQUIRED_REDIRECT_URI  = "Api oauth/access_token required redirect_uri.";
  const AOA_REQUIRED_CLIENT_SECRET = "Api oauth/access_token required client_secret.";
  const AOA_REQUIRED_CODE          = "Api oauth/access_token required code.";

  public static function getAccessToken(ParamsOauthAccessToken $params = null){
    $apiParams = array();
    if(is_null($params)){
      throw new Exception(self::AOA_ERROR_PARAMS_EMPTY);
    }elseif("" === ($apiParams['client_id'] = $params->getClientId())){
      throw new Exception(self::AOA_REQUIRED_CLIENT_ID);
    }elseif("" === ($apiParams['redirect_uri'] = $params->getRedirectUri())){
      throw new Exception(self::AOA_REQUIRED_REDIRECT_URI);
    }elseif("" === ($apiParams['client_secret'] = $params->getClientSecret())){
      throw new Exception(self::AOA_REQUIRED_CLIENT_SECRET);
    }elseif("" === ($apiParams['code'] = $params->getCode())){
      throw new Exception(self::AOA_REQUIRED_CODE);
    }

    $uri = self::API_URI . '?' . http_build_query($apiParams);
    $opts = array( 'http' => array( 'ignore_errors' => true ) );
    $context = stream_context_create( $opts );
    $response = file_get_contents($uri , false, $context );

    $responseOauthAccessToken = new ResponseOauthAccessToken();
    $responseOauthAccessToken->setResponse($response);

    return $responseOauthAccessToken;
  }
}

class ParamsOauthAccessToken
{
  const VALID_CLIENT_ID     = "Param oauth/access_token valid client id.";
  const VALID_REDIRECT_URL  = "Param oauth/access_token valid redirect url.";
  const VALID_CLIENT_SECRET = "Param oauth/access_token valid client_secret.";
  const VALID_CODE          = "Param oauth/access_token valid code.";

  private $clientId     = "";
  private $redirectUri  = "";
  private $clientSecret = "";
  private $code         = "";

  public function setParams($clientId = "", $redirectUri = "" , $clientSecret = "" , $code){
    $this->setClientId($clientId);
    $this->setRedirectUri($redirectUri);
    $this->setClientSecret($clientSecret);
    $this->setCode($code);
  }

  public function getClientId(){
    return $this->clientId;
  }

  public function setClientId($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_CLIENT_ID);
    }
    $this->clientId = $val;
  }

  public function getRedirectUri(){
    return $this->redirectUri;
  }

  public function setRedirectUri($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_REDIRECT_URL);
    }
    $this->redirectUri = $val;
  }

  public function getClientSecret(){
    return $this->clientSecret;
  }

  public function setClientSecret($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_CLIENT_SECRET);
    }
    $this->clientSecret = $val;
  }

  public function getCode(){
    return $this->code;
  }

  public function setCode($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_CODE);
    }
    $this->code = $val;
  } 
}

class ResponseOauthAccessToken
{
  const VALID_RESPONSE_FORMAT = "Response oauth/access_token valid format.";

  private $isError = false;
  private $error   = array('message' => '' , 'type' => '' , 'code' => null);
  private $accessToken = "";
  private $expires = null;

  public function setResponse($val){
    if($this->isJson($val)){
      $arrVal = json_decode($val , true);
      if(array_key_exists('error' , $arrVal)){
        $this->error = $arrVal['error'];
        $this->isError = true;
      }
    }else{
      parse_str($val, $output);
      foreach(array('accessToken' , 'expires') as $camelKey){
        $responseKey = trim(strtolower(preg_replace("/([A-Z])/u", "_$0", $camelKey)) , '_');
        if(! array_key_exists($responseKey , $output)){
          continue;
        }
        $val = $output[$responseKey];
        if(!is_string($val)){
          throw new Exception(self::VALID_RESPONSE_FORMAT . ":{$responseKey}");
        }
        $this->{$camelKey} = $val;
      }
    }
  }

  public function isError(){
    return $this->isError;
  }

  public function getError(){
    return $this->error;
  }

  public function getAccessToken(){
    return $this->accessToken;
  }

  public function getExpires(){
    return $this->expires;
  }

  private function isJson($val) {
    json_decode($val);
    return (json_last_error() == JSON_ERROR_NONE);
  } 
}

