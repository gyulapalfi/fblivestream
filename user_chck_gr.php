<?php
// Check The User In The GetResponse Campaign
// If it is a member of this campaign he has right to the access tis service.

include_once 'GetResponseAPI3.class.php'; //GetResponse API Class

$user_email = "gyula.palfi@gmail.com"; //From Facebook login
$campaign_id = "MDL9"; //GetResponse Campaign ID

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
echo "$get_name";
$get_email = reset($result)->email;
$get_campaignid = reset($result)->campaignId;

if ($get_email == $user_email) {
    $search_result = 1;
} else {
    $search_result = 0;
}

echo " A keresés eredménye $search_result";

if (empty($get_email)) {
    echo "You have no right";
    echo "$get_name";
} else {
    echo "User name: $get_name";
}

echo "<pre>";
//var_dump($result);
print_r($result);




?>