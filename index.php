<?php

$ref=@$_SERVER[HTTP_REFERER]; //A meghívó oldal url-je
$url=parse_url($ref, PHP_URL_HOST); //A meghívó hostja

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
//echo "<p>Referrer of this page= $ref</p>"; //teszt
//echo "<p>Referrer host is $url</p>"; // teszt

if ($url=="www.22s.com") {
    //echo "Yes"; //itt kellene lefutnia a Facebook Live kódnak
    echo "
        <head>
        
        <link rel='icon' type='image/png' href='favicon-32x32.png' sizes='32x32' />
        <link rel='icon' type='image/png' href='favicon-16x16.png' sizes='16x16' />
        <link rel='stylesheet' href='/css/main_style.css'>
        
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
        <div class='center'>
            <div class='head'>
                <div class='head-image'>
                    <img src='profile-4-mobile-icon.jpg' style='width:75px;height:75px;'>
                </div>
                <div class='head-right'>
                <h1>Online On Einstein-way<br>
                Live Stream To Facebook</h1>
                </div>
            </div>

            <h2>Don't forget allow the pop-up in this page!</h2>
            <p><button id='liveButton' class='button huge green-gradient'>Create Live Stream To Facebook</button></p>
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
            <p class='footer'>© Copyright 2016, Klára Sajben & Gyula Pálfi<br>All Right Reserved</p>
        </div>
        </body>
    ";
} else {
    //echo "No"; //mondja el, mit kell csinálnia
    echo "
        <head>
        <link rel='icon' type='image/png' href='favicon-32x32.png' sizes='32x32' />
        <link rel='icon' type='image/png' href='favicon-16x16.png' sizes='16x16' />
        <link rel='stylesheet' href='/css/main_style.css'>
        </head>
        
        <body>
        <div class='center'>
            <div class='head'>
                <div class='head-image'>
                    <img src='profile-4-mobile-icon.jpg' style='width:75px;height:75px;'>
                </div>
                <div class='head-right'>
                <h1>Online On Einstein-way<br>
                Live Stream To Facebook</h1>
                </div>
            </div>
            
            <h2>You Have No Right To Access This Service!</h2>
            
            <div class='box-member'>
                <p>If you <b>have a live subscription</b>, use the <u>original URL only</u>!<br>
                Click this button and try again!</p>

                <p><a href='https://www.22s.com/021ufh' target='_blank'>
                <button type='button' class='button huge red-gradient'>Click For The Right URL</button>
                </a>
            </div>
            <p>
            <div class='box-nonmember'>
                <p>If you <b>aren't a member</b> of our subscription, click the 
                button and learn how could you stream Live video from <u>your desktop</u> 
                on your <b>own Timeline</b>, on a <b>friend's Timeline</b>, in a <b>group</b> or on a <b>Page</b> you manage.</p>
                
                <p><a href='https://www.22s.com/021ufh' target='_blank'>
                <button type='button' class='button huge green-gradient'>Learn More</button>
                </a>

            </div>
            <p class='footer'>© Copyright 2016, Klára Sajben & Gyula Pálfi<br>All Right Reserved</p>

        </div>
        </body>
    ";
    /*
    echo "
        <script type='text/javascript'>
            alert('Nincs hozzáférési jogod!');
        </script>
        ";
    */
}
?>