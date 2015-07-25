<?php
/**
 * API : Authorizationエンドポイント
 * 
 * @see http://developer.yahoo.co.jp/yconnect/server_app/explicit/authorization.html
 */
class YaApiAuthorization
{
  const API_URI = "https://auth.login.yahoo.co.jp/yconnect/v1/authorization";

  const YAUTH_PARAMS_EMPTY   = "Api ya/authorization empty params.";
  const YAUTH_VALID_REQUIRED = "Api ya/authorization valid required.";

  public static function getAuthorizationUri(ParamsYaAuthorization $params = null){
    $apiParams = array();

    if(is_null($params)){
      throw new Exception(self::YAUTH_PARAMS_EMPTY);
      return;
    }

    /** 必須パラメータ */
    if("" === ($apiParams['response_type'] = $params->getResponseType())){
      throw new Exception(self::YAUTH_VALID_REQUIRED . ':response_type');
    }elseif("" === ($apiParams['client_id'] = $params->getClientId())){
      throw new Exception(self::YAUTH_VALID_REQUIRED . ':client_id');
    }elseif("" === ($apiParams['redirect_uri'] = $params->getRedirectUri())){
      throw new Exception(self::YAUTH_VALID_REQUIRED . ':redirect_uri');
    }

    /** オプションパラメータ */
    $apiParams['state']   = $params->getState();
    $apiParams['display'] = $params->getDisplay();
    $apiParams['prompt']  = $params->getPrompt();
    $apiParams['scope']   = $params->getScope();
    $apiParams['nonce']   = $params->getNonce();
    $apiParams['bail']    = $params->getBail();

    $apiParams = array_filter($apiParams);
    $uri = self::API_URI . '?' . http_build_query($apiParams);
    return $uri;
  }
}

class ParamsYaAuthorization
{
  const VALID_PARAM_FORMAT = "Param ya/authorization valid format.";

  private $responseType = "";
  private $clientId     = "";
  private $redirectUri  = "";
  private $state        = "";
  private $display      = "";
  private $prompt       = "";
  private $scope        = "";
  private $nonce        = "";
  private $bail         = "";

  /**
   * response_type
   */
  public function getResponseType(){
    return $this->responseType;
  }

  public function setResponseType($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":request_type");
    }
    $this->responseType = $val;
  }

  /**
   * client_id
   */ 
  public function getClientId(){
    return $this->clientId;
  }

  public function setClientId($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":client_id");
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
      throw new Exception(self::VALID_PARAM_FORMAT . ":redirect_uri");
    }
    $this->redirectUri = $val;
  }

  /**
   *  state
   */
  public function getState(){
    return $this->state;
  }

  public function setState($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":state");
    }
    $this->state = $val;
  }

  /**
   *  display
   */
  public function getDisplay(){
    return $this->display;
  }

  public function setDisplay($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":display");
    }
    $this->display = $val;
  }

  /**
   *  prompt
   */
  public function getPrompt(){
    return $this->prompt;
  }

  public function setPrompt($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":prompt");
    }
    $this->prompt = $val;
  }

  /**
   *  scope
   */
  public function getScope(){
    return $this->scope;
  }

  public function setScope($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":scope");
    }
    $this->scope = $val;
  }

  /**
   *  nonce
   */
  public function getNonce(){
    return $this->nonce;
  }

  public function setNonce($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":nonce");
    }
    $this->nonce = $val;
  }

  /**
   *  bail
   */
  public function getBail(){
    return $this->bail;
  }

  public function setBail($val = null){
    if(!is_int($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":bail");
    }
    $this->bail = $val;
  }
}

class ResponseYaAuthorization
{
  const VALID_RESPONSE_FORMAT = "Response ya/authorization valid format.";

  private $state            = "";
  private $code             = "";
  private $error            = "";
  private $errorDescription = "";

  public static function isResponse(){
    if(empty($_GET['code']) && empty($_GET['error'])){
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
      array('status' , 'code' , 'error' , 'errorDescription') as $camelKey)
    {
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