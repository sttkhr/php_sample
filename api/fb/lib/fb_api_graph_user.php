<?php
/**
 * API : graph/me
 * 
 * @see https://developers.facebook.com/docs/graph-api/reference/user
 */
class ApiGraphUser
{
  const API_URI = "https://graph.facebook.com";
  const AGU_ERROR_PARAMS_EMPTY    = "Api graph/user empty params.";
  const AGU_ERROR_VALID_USER      = "Api graph/user valid user.";
  const AGU_REQUIRED_ACCESS_TOKEN = "Api graph/user required access_token.";

  public static function getMe(ParamsGraphUser $params = null){
    return self::getUser($params , 'me');
  }

  public static function getUser(ParamsGraphUser $params = null , $user = null){
    $apiParams = array();
    if(is_null($params)){
      throw new Exception(self::AGU_ERROR_PARAMS_EMPTY);
    }elseif(is_null($user) || !(is_string($user) || is_int($user))){
      throw new Exception(self::AGU_ERROR_VALID_USER);
    }elseif("" === ($apiParams['access_token'] = $params->getAccessToken())){
      throw new Exception(self::AGU_REQUIRED_ACCESS_TOKEN);
    }
    $apiParams['locale'] = $params->getLocale();

    $uri = self::API_URI . "/{$user}?" . http_build_query($apiParams);
    $opts = array( 'http' => array( 'ignore_errors' => true ) );
    $context = stream_context_create( $opts );
    $response = file_get_contents($uri , false, $context );

    return json_decode($response);
  }
}

class ParamsGraphUser
{
  const VALID_ACCESS_TOKEN  = "Param graph/user valid access_token.";
  const VALID_LOCALE        = "Param graph/user valid locale.";

  private $accessToken = "";
  private $locale      = "ja_JP";
  
  public function getAccessToken(){
    return $this->accessToken;
  }

  public function setAccessToken($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_ACCESS_TOKEN);
    }
    $this->accessToken = $val;
  }

  public function getLocale(){
    return $this->locale;
  }

  public function setLocale($val = ""){
    if(!is_string($val)){
      throw new Exception(self::VALID_LOCALE);
    }
    $this->locale = $val;
  }
}
