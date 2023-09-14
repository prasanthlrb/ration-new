import {IModel} from "@js/types/models/models";


export interface INotificationData extends IModel {
    type: 'order',
    order_id?: number
}


export interface INotification extends IModel {
    data: INotificationData,
    title: string,
    body: string,
    created_at?: string,

}

export class Notification {
    static fromFCM(data: any): INotification {
        return {
            title: data.notification.title,
            body: data.notification.body,
            data: data.data
        }
    }
}
