import {IRoute, routeGroup} from "./routes";

import Login from "@js/pages/customer/auth/login.vue";
import Register from "@js/pages/customer/auth/register.vue";
import Verification from "@js/pages/customer/auth/verification.vue";

import Home from "@js/pages/customer/home.vue";


import ShowProduct from "@js/pages/customer/products/show.vue";

import Favorites from "@js/pages/customer/favorites.vue";


import Carts from "@js/pages/customer/carts/index.vue";
import Checkout from "@js/pages/customer/carts/checkout.vue";
import Addresses from "@js/pages/customer/addresses/index.vue";

import NotFound from "@js/pages/customer/errors/not_found.vue";


import Orders from "@js/pages/customer/orders/index.vue";
import ShowOrder from "@js/pages/customer/orders/show.vue";
import ReviewOrder from "@js/pages/customer/orders/review.vue";


import Profile from "@js/pages/customer/auth/profile.vue";
import Wallets from "@js/pages/customer/wallets/index.vue";
import LoyaltyWallets from "@js/pages/customer/loyalty_wallets/index.vue";

import OrderInvoice from "@js/pages/customer/orders/invoice.vue";
import ShowShop from "@js/pages/customer/shops/show.vue";
import Search from "@js/pages/customer/search.vue";
import AddMoney from "@js/pages/customer/wallets/add_money/index.vue";
import PaymentSuccess from "@js/pages/customer/wallets/add_money/success.vue";


const auth: IRoute[] = [
    {
    path: "login/", component: Login, name: "login"
},{
    path: "register/", component: Register, name: "register"
},{
    path: "verification/", component: Verification, name: "verification"
},
];


const products: IRoute[] = [{
    path: "products/:slug?/:id/", component: ShowProduct, name: "products.show",
},];


const shops: IRoute[] = [
//     {
//     path: "shops/", component: Shops, name: "shops.index",
// },
    {
    path: "shops/:id/", component: ShowShop, name: "shops.show",
},];

const carts: IRoute[] = [
    {
        path: "carts/", component: Carts, name: "carts.index",
    }, {
        path: "checkout/", component: Checkout, name: "checkout",
    },
];

const orders: IRoute[] = [
    {
        path: "orders/", component: Orders, name: "orders.index",
    },
    {
        path: "orders/:id/", component: ShowOrder, name: "orders.show",
    },
    {
        path: "orders/:id/reviews/", component: ReviewOrder, name: "orders.reviews",
    },
    {
        path: "orders/:id/invoice/", component: OrderInvoice, name: "orders.invoice"
    },
];


const addresses: IRoute[] = [
    {
        path: "addresses/", component: Addresses, name: "addresses.index",
    }
];

const wallets: IRoute[] = [
    {
        path: "wallets/", component: Wallets, name: "wallets.index",
    },
    {
        path: "loyalty_wallets/", component: LoyaltyWallets, name: "loyalty_wallets.index",
    }, {
        path: "add_money/", component: AddMoney, name: "add_money.index",
    },{
        path: "payments/success", component: PaymentSuccess, name: "payments.success",
    },

];

const errors: IRoute[] = [
    {
        path: "not_found/", component: NotFound, name: "errors.not_found",
    },


];

const other: IRoute[] = [
    {
        path: "home/", component: Home, name: "home"
    }, {
        path: "favorites/", component: Favorites, name: "favorites.index"
    }, {
        path: "profile/", component: Profile, name: "profile"
    }, {
        path: "search/", component: Search, name: "search"
    }, {
        path: ":pathMatch(.*)*", component: Home, name: "fallback"
    }
];


const route: IRoute[] = [...auth, ...products, ...shops, ...carts, ...addresses, ...orders, ...wallets, ...errors, ...other];


export default routeGroup(route, '/customer/', 'customer');
