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
 
        $profileRequest = $fb->get('/me?fields=email',$_SESSION['facebook_access_token']);
        
        $requestFriends = $fb->get('/me/taggable_friends?fields=name&limit=20');

		$fbUserProfile = $profileRequest->getGraphNode()->asArray();

		$friends = $requestFriends->getGraphEdge();
		

                   
    } catch(FacebookResponseException $e) {    	
        echo 'Graph returned an error: ' . $e->getMessage();
        session_destroy();
        header("Location: ./");
        exit;
    } catch(FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
  
  $output = json_encode($fbUserProfile['email']);

  $output1 = json_decode($friends);

  foreach($output1['data'] as $item){
    echo "<p>".$item['name']."</p>";
  }

  }
    
}else{

}
?>

<html>
<head>
<title>Facebook app</title>
 <script src="html2canvas.js"></script> 
 <link rel="stylesheet" href="./public/css/bootstrap.min.css">
<style type="text/css">
/*
    .loader{
    
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 1s linear 3;
    animation: spin 1s linear 3;
    position:relative;
    top:130px;
    left:350px;
  
    }
    .loader2{
    
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 1s linear 3;
    animation: spin 1s linear 3;
    position:relative;
    top:-35px;
    left:900px;
    
    
    }*/
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
    
    @-webkit-keyframes spin {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
 
    </style>

    <script>
    var hidden = false;


setTimeout(function(){


document.getElementById("you").style.visibility='hidden';
document.getElementById("cross").style.visibility='hidden';
document.getElementById("blackboard").style.visibility='hidden';
document.getElementById("content").style.visibility='hidden';
},1);


setTimeout(function(){


document.getElementById("you").style.visibility='visible';
document.getElementById("cross").style.visibility='visible';
document.getElementById("blackboard").style.visibility='visible';
document.getElementById("content").style.visibility='visible';
},3000);

</script>
 
</head>
<body>
    <div class="contact">
      <h4>Here are some contacts</h4>
    </div>

    <div class="list" align="middle">
      <?php echo 
      foreach($output1['data'] as $item){
        echo "<p>".$item['name']."</p>";
      }; 
      ?>    
    </div>
    <div class="footer">
        <p>Using OAuth in a Sample Application | IT15010636</p>
    </div>
    </body>
</html>
