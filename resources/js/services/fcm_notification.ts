import {ToastNotification} from "@js/services/toast_notification";
import {array_remove} from "@js/shared/array_utils";
import order from "@js/types/models/order";
import {Notification} from "@js/types/models/notification";

export class FcmNotification {

    private static orderListener: ((order_id: number) => void)[] = [];

    static handler = (payload: any) => {
        let notification = Notification.fromFCM(payload);
        ToastNotification.showFCM(notification);
        if (notification.data.type === 'order') {
            let order_id = payload.data.order_id;
            if (order_id != null) {
                this.broadcastOrderChange(order_id);
            }
        }
    }

    static subscribeOrderChangeListener(listener: (order_id: number) => void) {
        this.orderListener.push(listener);
    }

    static unsubscribeOrderChangeListener(listener) {
        this.orderListener = array_remove(this.orderListener, listener);
    }


    private static broadcastOrderChange(order_id: number) {
        for (const call of this.orderListener) {
            call(order_id);
        }


    }
}
