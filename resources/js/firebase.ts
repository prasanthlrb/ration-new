import { initializeApp, } from 'firebase/app';
import { getAuth, GoogleAuthProvider, signInWithPopup, User, signInWithPhoneNumber, RecaptchaVerifier, ApplicationVerifier } from "firebase/auth";

import { deleteToken, getMessaging, getToken, onMessage, } from "firebase/messaging";
import { FcmNotification } from "@js/services/fcm_notification";

let fcmMessaging = null;

export class Firebase {
    static initialized = false;

    static init() {
        const firebaseConfig = {
            apiKey: "AIzaSyDvcsDZ2ot3NDRHMVxDCmN_oPmMh6GGE9Q",
            authDomain: "ration-app-ad0d7.firebaseapp.com",
            projectId: "ration-app-ad0d7",
            storageBucket: "ration-app-ad0d7.appspot.com",
            messagingSenderId: "118676766225",
            appId: "1:118676766225:web:8d974fea2bbcb6f6c57bb0",
            measurementId: "G-XHBN7YT30K"
        };

        const app = initializeApp(firebaseConfig);
        fcmMessaging = getMessaging();

        onMessage(fcmMessaging, (ons: any) => {
            FcmNotification.handler(ons);
        })

        if (navigator.serviceWorker != null) {
            navigator.serviceWorker.addEventListener('message', (event) => {

                if (event?.data?.from === 'background') {
                    FcmNotification.handler(event?.data.payload);
                }
            });
        }
        requestNotificationPermission().then();
        this.initialized = true;
    }

    static getFCMToken = async () => {
        if (fcmMessaging == null) {
            return;
        }
        await requestNotificationPermission();
        await this.deleteFCMToken();
        try {
            let currentToken = await getToken(fcmMessaging,);

            if (currentToken) {
                return currentToken;
            } else {
                return null;
            }
        } catch (err) {
            console.warn('An error occurred while retrieving token. ', err);
            return null;
        }


    }

    static deleteFCMToken = async () => {
        const fcmMessaging = getMessaging();
        if (fcmMessaging == null) {
            return;
        }
        try {
            await deleteToken(fcmMessaging);
        } catch (e) {

        }
        return true;
    }

    static googleSignIn = async (): Promise<User> => {
        const auth = getAuth();
        const provider = new GoogleAuthProvider();
        try {
            const result = await signInWithPopup(auth, provider)
            return result.user;
        } catch (e) {
            console.info(e);
            return null;
        }

    }


}

const requestNotificationPermission = async () => {
    try {
        const status = await Notification.requestPermission();
        if (status === 'granted')
            return true;
    } catch (e) {
        return false;
    }
}


//In Dev
// class Verifier implements ApplicationVerifier{
//
//     readonly type = "recaptcha";
//     token;
//
//     constructor(token: string) {
//         this.token = token;
//     }
//
//     async verify() {
//         return await this.token;
//     }
// }









