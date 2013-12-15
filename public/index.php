<?php

require_once("../vendor/autoload.php");

// Get your keys and secrets at dev.twitter.com. Otherwise make a nice oauth thingie to do it from the web...
define('CONSUMER_KEY', 'YOUR-CONSUMER-KEY');
define('CONSUMER_SECRET', 'YOUR-CONSUMER-SECRET');
$twitter = new Twitter(CONSUMER_KEY, CONSUMER_SECRET, 'YOUR-ACCESS-TOKEN', 'YOUR-ACCESS-TOKEN-SECRET');

// Load me and get my friends list
$me = $twitter->load();
$friends = $twitter->cachedRequest('friends/ids', array('screen_name' => $me[0]->user), null);

// Bulk load my friends per 100. Much friendlier and won't hit your rate limits (easily)
foreach (array_chunk($friends->ids, 100) as $friends_slice) {
    $friends = $twitter->request('users/lookup', 'POST', array('user_id' => join(",", $friends_slice)));
    foreach ($friends as $friend) {
        $tweets[$friend->status->id_str] = $friend;
    }
}

// Sort by id_str, oldest first
ksort($tweets);


// Warning: High skilled frontend work next:
print "<table border=1>";
$i = 0;
foreach ($tweets as $tweet) {
    $i++;
    print "<tr><td>#".$i."</td><td><img src='".$tweet->profile_image_url."'><td>@".$tweet->screen_name."</td><td>".$tweet->name."</td><td>".$tweet->friends_count."</td><td>".$tweet->status->created_at."</td><td>".$tweet->status->text."</td></tr>";
}
print "</table>";
