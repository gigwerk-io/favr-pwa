// Import and configure the Firebase SDK
// These scripts are made available when the app is served or deployed on Firebase Hosting
// If you do not serve/host your project using Firebase Hosting see https://firebase.google.com/docs/web/setup
importScripts('https://www.gstatic.com/firebasejs/5.4.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/5.4.1/firebase-messaging.js');
//importScripts('/__/firebase/init.js');

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
