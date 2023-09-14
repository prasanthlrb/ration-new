import {IIdentifierModel, IModel} from "@js/types/models/models";


export interface IManualNotificationData extends IModel {
    type: 'order',
    order_id?: number
}


export interface IManualNotification extends IIdentifierModel {
    data: IManualNotificationData,
    title: string,
    body: string,
    all_customers: boolean,
    all_sellers: boolean,
    all_delivery_boys: boolean,
    created_at?: string,
    schedule_at?: string,

}
