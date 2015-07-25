<?php
/**
 *  Yahoo! API を使ってアクセストークンを取得するサンプル
 * 
 *  　※ SDKは使わずにOAuth2を利用したサンプル
 */

/** 各環境にあわせて変更してくさい. START */
define('APP_ID', 'hogehogehoge');
define('APP_SECRET', 'hogesecret');
define('REDIRECT_URI', 'http://sample.com');
/** 各環境にあわせて変更してくさい. END */

include_once("lib/ya_api_authorization.php");
include_once("lib/ya_api_access_token.php");

$paramsYaAuthorization = new ParamsYaAuthorization();
$paramsYaAuthorization->setResponseType('code');
$paramsYaAuthorization->setClientId(APP_ID);
$paramsYaAuthorization->setRedirectUri(REDIRECT_URI);
$paramsYaAuthorization->setScope('openid profile');
$paramsYaAuthorization->setBail(1);

try {
  if(ResponseYaAuthorization::isResponse()){
    $responseYaAuthorization = new ResponseYaAuthorization();
    $responseYaAuthorization->setResponse();
    if('' !== ($error = $responseYaAuthorization->getError())){
      echo $error;
    }else{
      $code = $responseYaAuthorization->getCode();
      
      $paramsYaAccessToken = new ParamsYaAccessToken();
      /** Authorization: Basic */
      $paramsYaAccessToken->setAppId(APP_ID);
      $paramsYaAccessToken->setAppSecret(APP_SECRET);

      /** API パラメータ */
      $paramsYaAccessToken->setGrantType('authorization_code');
      $paramsYaAccessToken->setCode($code);
      $paramsYaAccessToken->setRedirectUri(REDIRECT_URI);

      $asscessToken = YaApiAccessToken::getAccessToken($paramsYaAccessToken);
      if($asscessToken->isError()){
        echo $asscessToken->getError();
        echo '<hr>' . $asscessToken->getErrorDescription();
      }else{
        echo $asscessToken->getAccessToken();
      }
    }
    exit;
  }
  $loginUrl = YaApiAuthorization::getAuthorizationUri($paramsYaAuthorization);
} catch (Exception $e) {
  echo $e->getMessage();
  echo '<hr>' . $e->getTraceAsString();
  exit;
}
header('Content-type: text/html; charset=utf-8');
echo sprintf('<a href="%s">OAuth Link</a>' , $loginUrl);