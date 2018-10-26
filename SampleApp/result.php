<?php
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

session_start();
require_once __DIR__ . '/Facebook/autoload.php';
$fb = new Facebook([
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
    
    // Retrieve user facebook profile information
    try {
 
        $profileRequest1 = $fb->get('/me?fields=email',$_SESSION['facebook_access_token']);
        $profileRequest2 = $fb->get('/me?fields=name');
        $profileRequest3 = $fb->get('/me/picture?redirect=false&height=210&width=200');

		    $UserEmail = $profileRequest1->getGraphNode();
        $UserName = $profileRequest2->getGraphNode();
        $UserPicture = $profileRequest3->getGraphNode();
        
		                
    } catch(FacebookResponseException $e) {    	
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
  
  $email = json_encode($UserEmail['email']);
  $name = json_encode($UserName['name']);

  }  
else{

}
?>

<html>
<head>
<title>Facebook app</title>
 <script src="html2canvas.js"></script> 
 <link rel="stylesheet" href="./public/css/bootstrap.min.css">
<style type="text/css">
      .contact {
      position: absolute;
      top: 30%;
      left: 28%;
      margin: -150px 0 0 -150px;
      width: 1000px;
      height: 300px;
    }
    .contact h4 {
          color: #030397;
          text-align: center;
      }

      .list {
      position: absolute;
      top: 40%;
      left: 28%;
      margin: -150px 0 0 -150px;
      width: 1000px;
      height: 300px;
      color: #030397;
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
 
    </style>
 
</head>
<body>
    <div class="contact">
      <h4>User Profile Information</h4>
    </div>

    <div class="list" align="middle">
     <?php echo 
    "<img src='".$UserPicture['url']."' class='you' id='you' />
    <h5><p>Name: $name</p></h5>"; ?>  
    </div>
    <div class="footer">
        <p>Using OAuth in a Sample Application | IT15010636</p>
    </div>
</body>
</html>
