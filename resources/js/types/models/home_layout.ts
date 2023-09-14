import {IProduct} from "./product";
import {IShop} from "./shop";
import {ILocalFile} from "./models";
import Request from "../../services/api/request";
import {IIdentifierModel} from "@js/types/models/models";
import {IHomeBanner} from "@js/types/models/home_banners";
import i18n from "@js/services/i18n";

export type IHomeLayoutType = 'featured_shops' | 'featured_products' | 'popular_products' | 'latest_products' | 'home_banner'| 'other';

export interface IHomeLayout extends IIdentifierModel {
    id: number,
    type: IHomeLayoutType,
    items?: IProduct[] | IShop[] | IHomeBanner[],
    active: boolean,
    priority: number,
    images?: string[]
}


export class HomeLayout {


    static getTextFromType(type: IHomeLayoutType): string {
        return i18n.global.t(type);
    }

    static isProduct(type: IHomeLayoutType): boolean {
        return type === 'featured_products' || type === 'popular_products' || type === 'latest_products';
    }

    static isShop(type: IHomeLayoutType): boolean {
        return type === 'featured_shops';
    }

    static isFeaturedProduct(type: IHomeLayoutType) {
        return type === 'featured_products';
    }
    static isLatestProduct(type: IHomeLayoutType) {
        return type === 'latest_products';
    }
    static isPopularProduct(type: IHomeLayoutType) {
        return type === 'popular_products';
    }

    static isOther(type: IHomeLayoutType) {
        return type === 'other';
    }

    static isBanner(type: IHomeLayoutType) {
        return type === 'home_banner';
    }


    static toJSON(params?: { layouts: IHomeLayout[], images?: ILocalFile[] }) {
        let list = [];
        let layouts = params?.layouts;
        layouts.forEach(function (layout) {
            if (HomeLayout.isShop(layout.type) ||HomeLayout.isBanner(layout.type) || HomeLayout.isFeaturedProduct(layout.type)) {
                let ids = [];
                layout.items?.forEach(function (item) {
                    ids.push(item.id);
                })
                list.push({
                    'type': layout.type, 'active': layout.active, 'ids': ids,
                });
            } else if (HomeLayout.isOther(layout.type)) {
                let images = [];
                params?.images?.forEach(function (image) {
                    images.push(Request.getImageData(image.dataURL));
                });
                list.push({
                    'type': layout.type, 'active': layout.active, 'images': images,
                });
            } else {
                list.push({
                    'type': layout.type, 'active': layout.active
                });
            }

        });

        return list;
    }
}
