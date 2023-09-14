<?php

use App\Http\Controllers\Api\v1\Customer\AuthController;
use App\Http\Controllers\Api\v1\Customer\CartAddonController;
use App\Http\Controllers\Api\v1\Customer\CartController;
use App\Http\Controllers\Api\v1\Customer\CategoryController;
use App\Http\Controllers\Api\v1\Customer\CouponController;
use App\Http\Controllers\Api\v1\Customer\CustomerAddressController;
use App\Http\Controllers\Api\v1\Customer\CustomerFavoriteController;
use App\Http\Controllers\Api\v1\Customer\CustomerFavoriteProductController;
use App\Http\Controllers\Api\v1\Customer\CustomerFavoriteShopController;
use App\Http\Controllers\Api\v1\Customer\CustomerLoyaltyWalletController;
use App\Http\Controllers\Api\v1\Customer\CustomerWalletController;
use App\Http\Controllers\Api\v1\Customer\HomeLayoutController;
use App\Http\Controllers\Api\v1\Customer\InitController;
use App\Http\Controllers\Api\v1\Customer\NotificationController;
use App\Http\Controllers\Api\v1\Customer\OrderController;
use App\Http\Controllers\Api\v1\Customer\OrderDeliveryReviewController;
use App\Http\Controllers\Api\v1\Customer\OrderMediaController;
use App\Http\Controllers\Api\v1\Customer\OrderProductReviewController;
use App\Http\Controllers\Api\v1\Customer\ProductController;
use App\Http\Controllers\Api\v1\Customer\ProductReviewController;
use App\Http\Controllers\Api\v1\Customer\ProfileController;
use App\Http\Controllers\Api\v1\Customer\SearchController;
use App\Http\Controllers\Api\v1\Customer\ShopController;
use App\Http\Controllers\Api\v1\Customer\ShopReviewController;
use App\Http\Controllers\Api\v1\NavigationController;
use App\Http\Controllers\Api\v1\PaymentMethodController;
use App\Http\Controllers\Api\v1\SubscriberController;
use App\Http\Middleware\EnsureCustomerVerified;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1/customer'], function () {

    Route::post('subscribe', [SubscriberController::class, 'create']);


    Route::get('payments/data', [PaymentMethodController::class, 'public_data']);
    Route::get('payments/create_razorpay_checkout', [PaymentMethodController::class, 'createRazorpayCheckout']);
    Route::get('payments/create_cashfree_checkout', [PaymentMethodController::class, 'createCashfreeCheckout']);
    Route::get('payments/create_stripe_checkout', [PaymentMethodController::class, 'createStripeCheckout']);
    Route::get('payments/create_paypal_checkout', [PaymentMethodController::class, 'createPaypalCheckout']);
    Route::get('payments/create_flutterwave_checkout', [PaymentMethodController::class, 'createFlutterwaveCheckout']);
    Route::get('payments/create_paystack_checkout', [PaymentMethodController::class, 'createPaystackCheckout']);


    //---------------- Auth --------------------//
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send_otp', [AuthController::class, 'send_otp']);
    Route::post('login_via_otp', [AuthController::class, 'login_via_otp']);
    Route::post('google_login', [AuthController::class, 'google_login']);

    Route::get('orders/{id}/invoice/', [OrderController::class, 'invoice']);


    Route::group(['middleware' => ['guest_auth:customer-api']], function () {

        Route::get('init/', [InitController::class, 'index']);

        Route::get('home_layouts/', [HomeLayoutController::class, 'index']);


        Route::get('shops/{id}/products', [ShopController::class, 'products']);

        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::get('products/{id}/similar', [ProductController::class, 'similar']);
        Route::get('products/{id}/reviews', [ProductReviewController::class, 'index']);

        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('search', [SearchController::class, 'index']);

        Route::get('shops/', [ShopController::class, 'index']);
        Route::get('shops/{id}/', [ShopController::class, 'show']);

    });


    Route::group(['middleware' => ['auth:customer-api']], function () {

        Route::get('profile/verify_email/', [ProfileController::class, 'verify_email']);
        Route::post('profile/verify_mobile_number/', [ProfileController::class, 'verify_mobile_number']);
        Route::post('profile/delete_account/', [ProfileController::class, 'delete_account']);
        Route::post('profile/send_verification_email/', [ProfileController::class, 'send_verification_email']);
        Route::post('check_mobile_number', [AuthController::class, 'check_mobile_number']);
        Route::post('logout', [AuthController::class, 'logout']);


        Route::middleware([EnsureCustomerVerified::class])->group(function () {


            Route::get('notifications/', [NotificationController::class, 'index']);


            Route::get('profile/', [ProfileController::class, 'show']);
            Route::patch('profile/', [ProfileController::class, 'update']);
            Route::patch('profile/remove_avatar/', [ProfileController::class, 'remove_avatar']);


            Route::resource('carts', CartController::class);
            Route::resource('cart_addons', CartAddonController::class);

            Route::get('shops/{id}/coupons/', [ShopController::class, 'coupons']);
            Route::get('shops/{id}/carts/', [ShopController::class, 'carts']);
            Route::get('shops/{id}/reviews/', [ShopController::class, 'reviews']);


            Route::resource('customer_addresses', CustomerAddressController::class);
            Route::patch('customer_addresses/{id}/selected/', [CustomerAddressController::class, 'selected']);

            Route::resource('coupons', CouponController::class)->only(['index']);

            Route::resource('customer_favorite_products/', CustomerFavoriteProductController::class)->only(['index', 'store']);
            Route::resource('customer_favorite_shops/', CustomerFavoriteShopController::class)->only(['index', 'store']);
            Route::resource('customer_favorites/', CustomerFavoriteController::class)->only(['index']);

            Route::resource('orders', OrderController::class)->only(['index', 'store', 'show']);
            Route::post('orders/{id}/resubmit/', [OrderController::class, 'resubmit']);
            Route::post('orders/{id}/cancel/', [OrderController::class, 'cancel']);
            Route::post('orders/{id}/pickup/', [OrderController::class, 'pickup']);
            Route::post('orders/{id}/pay/', [OrderController::class, 'pay']);


            Route::resource('order_medias', OrderMediaController::class)->only(['destroy']);


            Route::resource('wallets', CustomerWalletController::class);
            Route::resource('loyalty_wallets', CustomerLoyaltyWalletController::class);
            Route::post('loyalty_wallets/convert_points', [CustomerLoyaltyWalletController::class, 'convert_points']);


            Route::get('orders/{id}/product_reviews', [OrderProductReviewController::class, 'index']);
            Route::post('orders/{id}/product_reviews', [OrderProductReviewController::class, 'store']);

            Route::get('orders/{id}/delivery_boy_reviews', [OrderDeliveryReviewController::class, 'index']);
            Route::post('orders/{id}/delivery_boy_reviews', [OrderDeliveryReviewController::class, 'store']);

            Route::post('shops/{id}/reviews/', [ShopReviewController::class, 'store']);
            Route::get('shops/{id}/reviews/me', [ShopReviewController::class, 'me']);

            Route::post('navigation/get_route/', [NavigationController::class, 'get_route']);

        });

    });
});


