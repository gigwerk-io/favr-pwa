<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/fcm/send");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, "https://askfavr.com/favr-pwa");
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n  \"notification\": {\n    \"title\": \"FAVR\",\n    \"body\": \"A new request has been posted in the marketplace. Be the first to complete!\",\n    \"icon\": \"https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png\",\n    \"click_action\": \"http://localhost:1234/favr-pwa/components/notifications/firebase/1\"\n  },\n  \"to\": \"fzjv8ICNSYE:APA91bEmm1oAUGghiqk_vdF1GDX9yEXl3T2NPbNHyK0iT7T16y27DgRTTU-ypEj0FostsAFhaXQvQloybxHQK6NeuKIKHNg5i9-rSDNqt6Skc4xyLqIYFToxsqxR2aRdYhcHbnPMMDA6\"\n}");
curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = "Authorization: key=AAAAXaruOsk:APA91bEsGAqHjZBsOdP-StRfYfsc4tq4-zym7HPveLzATIO2uzZ0TC6Z3K2WL_-YMQ3lFd6I9J81fPz9cZ4JKs9EkZJE1hZBoLtFIPruoPKc-28n5O9ZO9JBPuj9HFpvcot07OWvT9v3";
$headers[] = "Content-Type: application/json";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);

//curl -X POST -H "Authorization: key=AAAAXaruOsk:APA91bEsGAqHjZBsOdP-StRfYfsc4tq4-zym7HPveLzATIO2uzZ0TC6Z3K2WL_-YMQ3lFd6I9J81fPz9cZ4JKs9EkZJE1hZBoLtFIPruoPKc-28n5O9ZO9JBPuj9HFpvcot07OWvT9v3" -H "Content-Type: application/json" -d '{
//  "notification": {
//    "title": "FAVR",
//    "body": "A new request has been posted in the marketplace. Be the first to complete!",
//    "icon": "https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png",
//    "click_action": "https://askfavr.com/favr-pwa"
//  },
//  "to": "fzjv8ICNSYE:APA91bEmm1oAUGghiqk_vdF1GDX9yEXl3T2NPbNHyK0iT7T16y27DgRTTU-ypEj0FostsAFhaXQvQloybxHQK6NeuKIKHNg5i9-rSDNqt6Skc4xyLqIYFToxsqxR2aRdYhcHbnPMMDA6"
//}' "https://fcm.googleapis.com/fcm/send"
