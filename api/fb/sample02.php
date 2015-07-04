<?php
/**
 *  Facebook API を使ってユーザ情報を取得するサンプル
 * 
 *  　※ SDKは使わずにOAuth2を利用したサンプル
 */

/** 各環境にあわせて変更してくさい. START */
define('APP_ID', 'hoehoge');
define('APP_SECRET', 'hogesecret');
define('REDIRECT_URI', 'http://sample.com'));
/** 各環境にあわせて変更してくさい. END */

include_once("lib/fb_api_dialog_oauth.php");
include_once("lib/fb_api_oauth_access_token.php");
include_once("lib/fb_api_graph_user.php");

$paramsDialogOauth = new ParamsDialogOauth();
$paramsDialogOauth->setParams(APP_ID , REDIRECT_URI);
$paramsDialogOauth->setScope('email,user_birthday');

if(ResponseDialogOauth::isResponse()){
  $responseDialogOauth = new responseDialogOauth();
  try {
    $responseDialogOauth->setResponse();
    if('' !== ($error = $responseDialogOauth->getError())){
      echo $error;
    }else{
      $code = $responseDialogOauth->getCode();

      /* アクセストークン取得 */
      $paramsOauthAccessToken = new ParamsOauthAccessToken();
      $paramsOauthAccessToken->setParams(
        APP_ID , REDIRECT_URI , APP_SECRET , $code
      );
      $asscessTokenObj = ApiOauthAccessToken::getAccessToken($paramsOauthAccessToken);
      
      /* ユーザ情報取得 */
      $paramsGraphUser = new ParamsGraphUser();
      $paramsGraphUser->setAccessToken($asscessTokenObj->getAccessToken());
      $userInfo = ApiGraphUser::getMe($paramsGraphUser);

      var_dump($userInfo);
    }
  } catch (Exception $e) {
    echo $e->getMessage();
  }
  exit;
}

header('Content-type: text/html; charset=utf-8');
echo sprintf('<a href="%s">OAuth Link</a>' , ApiDialogOauth::getDialogOAuthUri($paramsDialogOauth));
