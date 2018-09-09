Firebase Cloud Messaging Quickstart
===================================

The Firebase Cloud Messaging quickstart demonstrates how to:
- Request permission to send app notifications to the user.
- Receive FCM messages using the Firebase Cloud Messaging JavaScript SDK.

Introduction
------------

[Read more about Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging/)

Getting Started
---------------

1. Create your project on the [Firebase Console](https://console.firebase.google.com).
1. You must have the [Firebase CLI](https://firebase.google.com/docs/cli/) installed. If you don't have it install it with `npm install -g firebase-tools` and then configure it with `firebase login`.
1. On the command line run `firebase use --add` and select the Firebase project you have created.
1. On the command line run `firebase serve -p 8081` using the Firebase CLI tool to launch a local server.
1. Open [http://localhost:8081](http://localhost:8081) in your browser.
4. Click **REQUEST PERMISSION** button to request permission for the app to send notifications to the browser.
5. Use the generated Instance ID token (IID Token) to send an HTTP request to FCM that delivers the message to the web application, inserting appropriate values for [`YOUR-SERVER-KEY`](https://console.firebase.google.com/project/_/settings/cloudmessaging) and `YOUR-IID-TOKEN`.

### HTTP
```
POST /fcm/send HTTP/1.1
Host: fcm.googleapis.com
Authorization: key=YOUR-SERVER-KEY
Content-Type: application/json

{
  "notification": {
    "title": "Portugal vs. Denmark",
    "body": "5 to 1",
    "icon": "firebase-logo.png",
    "click_action": "http://localhost:8081"
  },
  "to": "YOUR-IID-TOKEN"
}
```

### Fetch
```js
var key = 'YOUR-SERVER-KEY';
var to = 'YOUR-IID-TOKEN';
var notification = {
  'title': 'Portugal vs. Denmark',
  'body': '5 to 1',
  'icon': 'firebase-logo.png',
  'click_action': 'http://localhost:8081'
};

fetch('https://fcm.googleapis.com/fcm/send', {
  'method': 'POST',
  'headers': {
    'Authorization': 'key=' + key,
    'Content-Type': 'application/json'
  },
  'body': JSON.stringify({
    'notification': notification,
    'to': to
  })
}).then(function(response) {
  console.log(response);
}).catch(function(error) {
  console.error(error);
})
```

### cURL
```
curl -X POST -H "Authorization: key=AAAAXaruOsk:APA91bEsGAqHjZBsOdP-StRfYfsc4tq4-zym7HPveLzATIO2uzZ0TC6Z3K2WL_-YMQ3lFd6I9J81fPz9cZ4JKs9EkZJE1hZBoLtFIPruoPKc-28n5O9ZO9JBPuj9HFpvcot07OWvT9v3" -H "Content-Type: application/json" -d '{
  "notification": {
    "title": "FAVR",
    "body": "A new request has been posted in the marketplace. Be the first to complete!",
    "icon": "https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png",
    "click_action": "http://localhost:8081"
  },
  "to": "fzjv8ICNSYE:APA91bEmm1oAUGghiqk_vdF1GDX9yEXl3T2NPbNHyK0iT7T16y27DgRTTU-ypEj0FostsAFhaXQvQloybxHQK6NeuKIKHNg5i9-rSDNqt6Skc4xyLqIYFToxsqxR2aRdYhcHbnPMMDA6"
}' "https://fcm.googleapis.com/fcm/send"
```

### App focus
When the app has the browser focus, the received message is handled through
the `onMessage` callback in `index.html`. When the app does not have browser
focus then the `setBackgroundMessageHandler` callback in `firebase-messaging-sw.js`
is where the received message is handled.

The browser gives your app focus when both:

1. Your app is running in the currently selected browser tab.
2. The browser tab's window currently has focus, as defined by the operating system.

Support
-------

https://firebase.google.com/support/

License
-------

© Google, 2016. Licensed under an [Apache-2](../LICENSE) license.
