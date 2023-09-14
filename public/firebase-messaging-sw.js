var window = self;

importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.2.0/firebase-messaging-compat.js');

// importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
// importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object


const app = firebase.initializeApp({
    apiKey: "AIzaSyDvcsDZ2ot3NDRHMVxDCmN_oPmMh6GGE9Q",
  authDomain: "ration-app-ad0d7.firebaseapp.com",
  projectId: "ration-app-ad0d7",
  storageBucket: "ration-app-ad0d7.appspot.com",
  messagingSenderId: "118676766225",
  appId: "1:118676766225:web:8d974fea2bbcb6f6c57bb0",
  measurementId: "G-XHBN7YT30K"
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.info(payload);
    if (clients != null) {
        clients
            .matchAll({
                type: "window", includeUncontrolled: true
            })
            .then(windowClients => {
                for (let i = 0; i < windowClients.length; i++) {
                    const windowClient = windowClients[i];
                    windowClient.postMessage({
                        "from": "background",
                        "payload": payload
                    });
                }
            });
    }
    // if (self.fcmHandler) {
    //     self.fcmHandler(payload);
    // }
});
// messaging.onMessage((payload) => {
//     if (self.fcmHandler) {
//         self.fcmHandler(payload);
//     }
// });
