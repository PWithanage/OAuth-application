<html>

<head>
    <title>Facebook App</title>
    <link rel="stylesheet" href="./public/css/bootstrap.min.css">
    <style type="text/css">
        .link {
            background-image: url("login.png");
            background-size: 400px 120px;
            width: 400px;
            height: 120px;
            display: block;
            background-repeat: no-repeat;
            position: relative;
            margin-top: 200px;
        }
        
        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #cdced0;
            color: black;
            text-align: center;
        }
        
        .login {
            position: absolute;
            top: 50%;
            left: 28%;
            margin: -150px 0 0 -150px;
            width: 1000px;
            height: 300px;
        }
        
        .login h4 {
            color: #030397;
            text-align: center;
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <a class='nav-link active' href='index.php'>Sample App</a>
    </nav>
    <div class="login">
        <h4>Sample app retrieves your information</h4>
    </div>
    <div class="footer">
        <p>Using OAuth in a Sample Application | IT15010636</p>
    </div>

</body>

</html>

<?php
session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '313412172774079',
  'app_secret' => 'd48b2115dd7fcf4796efae9309076a23',
  'default_graph_version' => 'v2.9',
  ]);
$helper = $fb->getRedirectLoginHelper();

$permissions =  array("email","user_friends");  
try {
  if (isset($_SESSION['facebook_access_token'])) {
    $accessToken = $_SESSION['facebook_access_token'];
  } else {
      $accessToken = $helper->getAccessToken();
  }
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
 }
if (isset($accessToken)) {
  if (isset($_SESSION['facebook_access_token'])) {
    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
    header('Location: http://localhost/SampleApp/result.php');
  } else {
    $_SESSION['facebook_access_token'] = (string) $accessToken;

    $oAuth2Client = $fb->getOAuth2Client();

    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
    $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
  }

  if (isset($_GET['code'])) {   
    header('Location: ./');
  }

} else {

  $loginUrl = $helper->getLoginUrl('http://localhost/SampleApp/index.php', $permissions);

  echo '<center><a class="link" href="' . $loginUrl . '"></a></center>';

}

?>