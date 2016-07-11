<?php

require_once __DIR__ . '/vendor/autoload.php';


//A browser beállított nyelvei
$languages=explode(',',$_SERVER[HTTP_ACCEPT_LANGUAGE]);
if (in_array("hu",$languages)) {
    // echo "<p>Ért magyarul.</p>";
    //ide kell betülteni a magyar nyelvi fájlt.
    include_once 'languages/lang_hu.php';
} else {
    include_once 'languages/lang_en.php';
}

//echo $lang['PAGE_TITEL'];

echo '
    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/png" href="/images/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="/images/favicon-16x16.png" sizes="16x16" />
        <link rel="stylesheet" href="/css/main_style.css" />
    </head>
    
    <body>
        <div class="center">
            
            <img src="profile-4-mobile-icon.jpg" style="width:75px;height:75px;">
                
            <h1>' .$lang['PAGE_TITEL']. '<br>'
            .$lang['PAGE_SUBTITLE']. '</h1>
    ';
    

session_start();

/* --- Initial Variables ---*/

$access = 0;    //Access or Not to this service
$service_url = "https://www.22s.com/022n3j";
$ref_url = "www.22s.com";   //The payed user from
$campaign_id = "nWwBm"; // facebook_live Getresponse Campaign ID
$loggedIn = 0;  // Logged is by Facebook?

/*1st condition - check the referral url.
* If the url is equal 22s.com, the access is enabled. */

$ref=@$_SERVER[HTTP_REFERER];       //A meghívó oldal url-je
$url=parse_url($ref, PHP_URL_HOST); //A meghívó hostja

//echo "<pre><p>Referrer of this page= $ref<br>"; //teszt
//echo "Referrer host is $url</p></pre>"; // teszt



if ($url==$ref_url) {
    $access = 1;
    
} else {    // Ha az URL nem megfelelő, akkor regisztráltassa a Facebookkal és ellenőrizze a jogosultságát
   
   // Facebook Login
   
   // Include the required dependencies.
    //require( __DIR__.'/facebook-php-sdk-v5/autoload.php' );

    // Initialize the Facebook PHP SDK v5.
    $fb = new Facebook\Facebook([
        'app_id' => '1710308295910621',
        'app_secret' => 'ed45c3115b32156244bf8a5fb1dd8783',
        'default_graph_version' => 'v2.6',
        ]);

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email']; // optional
	
    try {
	    if (isset($_SESSION['facebook_access_token'])) {
            $accessToken = $_SESSION['facebook_access_token'];
	    } else {
            $accessToken = $helper->getAccessToken();
	    }
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
 	    echo 'Graph returned an error: ' . $e->getMessage();

  	    exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
 	    // When validation fails or other local issues
	    echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	    exit;
    }

    if (isset($accessToken)) {
	    if (isset($_SESSION['facebook_access_token'])) {
		    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	    } else {
		    // getting short-lived access token
		    $_SESSION['facebook_access_token'] = (string) $accessToken;

	  	    // OAuth 2.0 client handler
		    $oAuth2Client = $fb->getOAuth2Client();

		    // Exchanges a short-lived access token for a long-lived one
		    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);

		    $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;

		    // setting default access token to be used in script
		    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	    }

	    // redirect the user back to the same page if it has "code" GET variable
	    if (isset($_GET['code'])) {
		    header('Location: ./');
	    }

	    // getting basic info about user
	    try {
		    $profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
		    $profile = $profile_request->getGraphNode()->asArray();
	    } catch(Facebook\Exceptions\FacebookResponseException $e) {
		    // When Graph returns an error
		    echo 'Graph returned an error: ' . $e->getMessage();
		    session_destroy();
		    // redirecting user back to app login page
		    header("Location: ./");
		    exit;
	    } catch(Facebook\Exceptions\FacebookSDKException $e) {
		    // When validation fails or other local issues
		    echo 'Facebook SDK returned an error: ' . $e->getMessage();
		    exit;
	    }
	    
	    $loggedIn = 1;
	    
	    // Add user's email to GetResponse query
	    $user_email = $profile['email'];
	
	    // printing $profile array on the screen which holds the basic info about user
	    //echo "<pre>";
	    //echo '<p>UserName is: ' .$profile['name']. '<br>'; //The name of the user
	    //echo 'Email is: ' .$user_email. '</p></pre>'; //The email address of the user
	    
	    //print_r($profile);
	    
	    
       // Check The User In The GetResponse Campaign
    
        include_once 'GetResponseAPI3.class.php'; //GetResponse API Class

        $getresponse = new GetResponse('966007bad70ce2e775249f7c536a4b41'); 

        //Serach contact by email in the campaign
        $result = $getresponse->getContacts(array(
            'query' => array(
                'email' => $user_email,
                'campaignId' => $campaign_id,
            ),
            'fields' => 'name,email,campaign'
        ));
    
        //A lekérdezés eredménye...
        $get_name = reset($result)->name;
        //echo "$get_name";
        $get_email = reset($result)->email;
        $get_campaignid = reset($result)->campaignId;
    
        if ($get_email == $user_email) {
            $access = 1;
        }

    
        //echo '<pre>A GetResponse lekérdezés eredménye:';
        //print_r($result);

	    
	    

  	    // Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
    } else {
	    // replace your website URL same as added in the developers.facebook.com/apps e.g. if you used http instead of https and you used non-www version or www version of your website then you must add the same here
	    $loginUrl = $helper->getLoginUrl('https://fblivestream.herokuapp.com/', $permissions);
	    echo '
    	    
    	        <h2>' .$lang['LOGIN_NOTE']. '</h2>
	            <p><a href="' . $loginUrl . '">
                    <button type="button" class="button huge blue-gradient">' .$lang['LOGIN_BUTTON']. '</button>
	            </a></p>
            	       
	    ';
    }
    

}

//Ceate Live Streaming to Facebook
if ($access==1) {
    echo "
        <head>
        <script type='text/javascript'>
            window.fbAsyncInit = function() {
                FB.init({
                    appId: '1710308295910621',
                    xfbml: true,
                    version: 'v2.6'
                });
            };
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s);
                js.id = id;
                js.src = '//connect.facebook.net/en_US/sdk.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        </head>
        
        <body>
            <h2>" .$lang['STREAM_POPUP']. "</h2>
            <p><button id='liveButton' class='button huge blue-gradient'>" .$lang['STREAM_BUTTON']. "</button></p>
            <p>
            <script type='text/javascript'>
                document.getElementById('liveButton').onclick = function() {
                    FB.ui({
                        display: 'popup',
                        method: 'live_broadcast',
                        phase: 'create',
                }, function(response) {
                    if (!response.id) {
                        alert('dialog canceled');
                        return;
                    }
                    alert('stream url:' + response.secure_stream_url);
                    FB.ui({
                        display: 'popup',
                        method: 'live_broadcast',
                        phase: 'publish',
                        broadcast_data: response,
                    }, function(response) {
                    alert('video status:' + response.status);
                    });
                });
                };
            </script></p>
            
            <div class='box-member'>
                <p>" .$lang['POPUP_HELP']. "</p>
                <a href='https://support.google.com/chrome/answer/95472' target='_blank'>
                    <img src='images/chrome-logo.png' width='50px'>
                </a>
                <a href='https://support.mozilla.org/en-US/kb/pop-blocker-settings-exceptions-troubleshooting' target='_blank'>
                    <img src='images/firefox-logo.png' width='50px'>
                </a>
                <a href='https://help.opera.com/Windows/12.10/en/popups.html' target='_blank'>
                    <img src='images/opera-logo.png' width='50px'>
                </a>
                <a href='https://support.microsoft.com/en-us/help/17479/windows-internet-explorer-11-change-security-privacy-settings' target='_blank'>
                    <img src='images/ie-logo.png' width='50px'>
                </a>
                <a href='https://discussions.apple.com/thread/4271925?start=0&tstart=0' target='_blank'>
                    <img src='images/safari-logo.png' width='50px'>
                </a>

            </div>
    ";
} else {        //Logged in but no have got access!
    if ($loggedIn == 1) {
        echo '
            <body>
                <h2>' . $lang['LOGIN_AS'] . $get_name. ' ' .$get_email. '<p>'.$lang['NO_ACCES_WARNING']. '</h2>
                <div class="box-member">
                    <p>' .$lang['HAVE_SUBSCRIPTION']. '</p>
                    <p><a href="' .$service_url. '" target="_blank">
                        <button type="button" class="button huge red-gradient">' .$lang['BUTTON_TO22S']. '</button>
                    </a>
                </div>
                <p>
                <div class="box-nonmember">
                    <p>' .$lang['NO_SUBSCRIPTION']. '</p>
                
                    <p><a href="' .$service_url. '" target="_blank">
                        <button type="button" class="button huge green-gradient">' .$lang['BUTTON_NO_SUBSC']. '</button>
                    </a>

                </div>
        ';
    }
}
//Page footer
echo '
    <pre>
        <p class="footer">' .$lang['FOOTER_COPY']. '</p>
    </pre>
    </div>
    <div class="footer_shadow">
        <img src="https://i.imgur.com/edP1xLi.png" width="400px">
    </div>
    </body>
    ';
?>