<?php
/**
 * API : dialog/oauth
 * 
 * @see https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/v2.3
 */
class ApiDialogOauth
{
  const API_URI = "http://www.facebook.com/dialog/oauth";

  const ADO_ERROR_PARAMS_EMPTY    = "Api dialog/oauth empty params.";
  const ADO_REQUIRED_CLIENT_ID    = "Api dialog/oauth required client_id.";
  const ADO_REQUIRED_REDIRECT_URI = "Api dialog/oauth required redirect_uri.";

  public static function getDialogOAuthUri(ParamsDialogOauth $params = null){
    $apiParams = array();

    if(is_null($params)){
      throw new Exception(self::ADO_ERROR_PARAMS_EMPTY);
    }elseif("" === ($apiParams['client_id'] = $params->getClientId())){
      throw new Exception(self::ADO_REQUIRED_CLIENT_ID);
    }elseif("" === ($apiParams['redirect_uri'] = $params->getRedirectUri())){
      throw new Exception(self::ADO_REQUIRED_REDIRECT_URI);
    }
    $apiParams['response_type'] = $params->getResponseType();
    $apiParams['scope'] = $params->getScope();
    $apiParams = array_filter($apiParams);

    $uri = self::API_URI . '?' . http_build_query($apiParams);
    return $uri;
  }
}

class ParamsDialogOauth
{
  const VALID_CLIENT_ID     = "Param dialog/oauth valid client id.";
  const VALID_REDIRECT_URL  = "Param dialog/oauth valid redirect url.";
  const VALID_SCOPE         = "Param dialog/oauth valid scope.";
  const VALID_RESPONSE_TYPE = "Param dialog/oauth valid response type.";
  const VALID_STATE         = "Param dialog/oauth valid state.";


  private $clientId     = "";
  private $redirectUri  = "";
  private $scope        = "";
  private $responseType = "";
  private $state        = "";

  public function setParams($clientId = "", $redirectUri = "" ,
                    $scope = "" , $responseType = "" , $state = ""){
    $this->setClientId($clientId);
    $this->setRedirectUri($redirectUri);
    $this->setScope($scope);
    $this->setResponseType($responseType);
    $this->setState($state);
  }

  /**
   * client_id
   */ 
  public function getClientId(){
    return $this->clientId;
  }

  public function setClientId($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_CLIENT_ID);
    }
    $this->clientId = $val;
  }

  /**
   *  redirect_uri
   */
  public function getRedirectUri(){
    return $this->redirectUri;
  }

  public function setRedirectUri($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_REDIRECT_URL);
    }
    $this->redirectUri = $val;
  }

  /**
   *  scope
   */
  public function getScope(){
    return $this->scope;
  }

  public function setScope($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_SCOPE);
    }
    $this->scope = $val;
  }

  /**
   *  response_type
   */
  public function getResponseType(){
    return $this->responseType;
  }

  public function setResponseType($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_RESPONSE_TYPE);
    }
    $this->responseType = $val;
  }

  /**
   *  state
   */
  public function getState(){
    return $this->state;
  }

  public function setState($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_STATE);
    }
    $this->responseType = $val;
  }
}

class ResponseDialogOauth
{
  const VALID_RESPONSE_FORMAT = "Response dialog/oauth valid format.";

  private $state            = "";
  private $accessToken      = "";
  private $expiresIn        = "";
  private $code             = "";
  private $error            = "";
  private $errorDescription = "";

  public static function isResponse(){
    if(empty($_GET['access_token']) && empty($_GET['code']) && empty($_GET['error'])){
      return false;
    }else{
      return true;
    }
  }

  public function setResponse(){
    if(! self::isResponse()){
      throw new Exception(self::VALID_RESPONSE_FORMAT . ":required valid.");
    }
    foreach(
      array('status' , 'accessToken' , 'expiresIn' , 
        'code' , 'error' , 'errorDescription') as $camelKey){
 
      $getMethodKey = trim(strtolower(preg_replace("/([A-Z])/u", "_$0", $camelKey)) , '_');
      if(! array_key_exists($getMethodKey , $_GET)){
        continue;
      }
      $val = $_GET[$getMethodKey];
      if(!is_string($val)){
        throw new Exception(self::VALID_RESPONSE_FORMAT . ":{$getMethodKey}");
      }
      $this->{$camelKey} = $val;
    }
  }

  public function getStatus(){
    return $this->status;
  }

  public function getAcessToken(){
    return $this->accessToken;
  }

  public function getExpiresIn(){
    return $this->expiresIn;
  }

  public function getCode(){
    return $this->code;
  }

  public function getError(){
    return $this->error;
  }

  public function getErrorDescription(){
    return $this->errorDescription;
  }
}