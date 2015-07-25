<?php
/**
 * API : Tokenエンドポイント
 * 
 * @see http://developer.yahoo.co.jp/yconnect/server_app/explicit/token.html
 */
class YaApiAccessToken
{
  const API_URI = "https://auth.login.yahoo.co.jp/yconnect/v1/token";

  const YA_ACCESS_TOKEN_PARAMS_EMPTY   = "Api ya/access_token empty params.";
  const YA_ACCESS_TOKEN_VALID_REQUIRED = "Api ya/access_token valid required.";

  public static function getAccessToken(ParamsYaAccessToken $params = null){
    $apiParams = array();

    if(is_null($params)){
      throw new Exception(self::YA_ACCESS_TOKEN_PARAMS_EMPTY);
      return;
    }

    /** 必須パラメータ */
    if("" === ($appId = $params->getAppId())){
      throw new Exception(self::YA_ACCESS_TOKEN_VALID_REQUIRED . ':appID');
    }elseif("" === ($appSecret = $params->getAppSecret())){
      throw new Exception(self::YA_ACCESS_TOKEN_VALID_REQUIRED . ':appSecret');
    }else{
      if("" === ($apiParams['grant_type'] = $params->getGrantType())){
        throw new Exception(self::YA_ACCESS_TOKEN_VALID_REQUIRED . ':grant_type');
      }elseif("" === ($apiParams['code'] = $params->getCode())){
        throw new Exception(self::YA_ACCESS_TOKEN_VALID_REQUIRED . ':code');
      }elseif("" === ($apiParams['redirect_uri'] = $params->getRedirectUri())){
        throw new Exception(self::YA_ACCESS_TOKEN_VALID_REQUIRED . ':redirect_uri');
      }
    }
    $apiParams = array_filter($apiParams);

    $header = array(
      "Content-Type: application/x-www-form-urlencoded",
      "Authorization: Basic ". base64_encode($appId . ':' . $appSecret)
    );

    $opts = array( 
      'http' => array( 
        'ignore_errors' => true,
        'method' => 'POST',
        'header' => implode("\r\n", $header),
        'content' => http_build_query($apiParams)
      ) 
    );
    $context = stream_context_create( $opts );
    $response = file_get_contents(self::API_URI , false, $context );

    $responseYaAccessToken = new ResponseYaAccessToken();

var_dump($response);
echo '<hr>';

    $responseYaAccessToken->setResponse($response);

    return $responseYaAccessToken;
  }
}

class ParamsYaAccessToken
{
  const VALID_PARAM_FORMAT = "Param ya/access_token valid format.";

  /** Authorization: Basic */
  private $appId = "";
  private $appSecret = "";

  /** API パラメータ */
  private $grantType   = "";
  private $code        = "";
  private $redirectUri = "";

  public function getAppId(){
    return $this->appId;
  }

  public function setAppId($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":appId");
    }
    $this->appId = $val;
  }

  public function getAppSecret(){
    return $this->appSecret;
  }

  public function setAppSecret($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":appSecret");
    }
    $this->appSecret = $val;
  }

  public function getGrantType(){
    return $this->grantType;
  }

  public function setGrantType($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":grant type");
    }
    $this->grantType = $val;
  }

  public function getCode(){
    return $this->code;
  }

  public function setCode($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":code");
    }
    $this->code = $val;
  } 

  public function getRedirectUri(){
    return $this->redirectUri;
  }

  public function setRedirectUri($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_PARAM_FORMAT . ":redirect uri");
    }
    $this->redirectUri = $val;
  }
}

class ResponseYaAccessToken
{
  const VALID_RESPONSE_FORMAT = "Response ya/access_token valid format.";

  private $isError = false;

  private $accessToken      = "";
  private $tokenType        = "";
  private $expiresIn        = "";
  private $refreshToken     = "";
  private $idToken          = "";
  private $error            = "";
  private $errorDescription = "";

  public function setResponse($val){
    $output = json_decode($val , true);

    foreach($this as $fieldKey => $fieldVal){
      $responseKey = trim(strtolower(preg_replace("/([A-Z])/u", "_$0", $fieldKey)) , '_');

      if(! array_key_exists($responseKey , $output)){
        continue;
      }

      $val = $output[$responseKey];
      if(!is_string($val)){
        throw new Exception(self::VALID_RESPONSE_FORMAT . ":{$responseKey}");
      }
      $this->{$fieldKey} = $val;
    }
    if('' !== $this->getError()){
      $this->isError = true;
    }
  }

  public function isError(){
    return $this->isError;
  }

  public function getAccessToken(){
    return $this->accessToken;
  }

  public function getTokenType(){
    return $this->tokenType;
  }

  public function getExpiresIn(){
    return $this->expiresIn;
  }

  public function getRefreshToken(){
    return $this->refreshToken;
  }

  public function getIdToken(){
    return $this->idToken;
  }

  public function getError(){
    return $this->error;
  }

  public function getErrorDescription(){
    return $this->errorDescription;
  }
}
