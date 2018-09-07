// Import and configure the Firebase SDK
// These scripts are made available when the app is served or deployed on Firebase Hosting
// If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup
importScripts('/__/firebase/5.0.0/firebase-app.js');
importScripts('/__/firebase/5.0.0/firebase-messaging.js');
importScripts('/__/firebase/init.js');

var config = {
  apiKey: "AIzaSyChPH0J3U7mrS_fYxKJehQJBQpBhlCFob8",
  authDomain: "favr-pwa.firebaseapp.com",
  databaseURL: "https://favr-pwa.firebaseio.com",
  projectId: "favr-pwa",
  storageBucket: "",
  messagingSenderId: "402299697865"
};
firebase.initializeApp(config);

var messaging = firebase.messaging();

messsaging.requestPermission()
.then(function(){
  console.log('Have permission');
  return messaging.getToken();
})
.then(function(token){
  console.log(token)
})
.catch(function(err){
  console.log('Error Occured');
})

messaging.onMessage(function(payload){
  console.log('onMessage:', payload);
});
/**
 * Here is is the code snippet to initialize Firebase Messaging in the Service
 * Worker when your app is not hosted on Firebase Hosting.

 // [START initialize_firebase_in_sw]
 // Give the service worker access to Firebase Messaging.
 // Note that you can only use Firebase Messaging here, other Firebase libraries
 // are not available in the service worker.
 importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js');
 importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js');

 // Initialize the Firebase app in the service worker by passing in the
 // messagingSenderId.
 firebase.initializeApp({
   'messagingSenderId': 'YOUR-SENDER-ID'
 });

 // Retrieve an instance of Firebase Messaging so that it can handle background
 // messages.
 const messaging = firebase.messaging();
 // [END initialize_firebase_in_sw]
 **/


// If you would like to customize notifications that are received in the
// background (Web app is closed or not in browser focus) then you should
// implement this optional method.
// [START background_handler]
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  var notificationTitle = 'Background Message Title';
  var notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  return self.registration.showNotification(notificationTitle,
    notificationOptions);
});
// [END background_handler]
