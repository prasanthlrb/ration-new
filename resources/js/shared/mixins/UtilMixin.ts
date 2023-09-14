import dateFormat from 'dateformat';
import { DateUtil } from "@js/shared/utils";
import {
    getCurrencyPosition,
    getCurrencySymbol,
    getDigitAfterDecimal,
    getTimeFormat
} from "@js/services/state/state_helper";
import NavigatorService from "@js/services/navigator_service";

export default {

    data() {
        return {
            /**
             * @deprecated since version 2.0.0
             */
            currencySign: getCurrencySymbol(),
        }
    }, computed: {
        /**
         * @deprecated since version 2.0.0
         */
        // currencySign() {
        //     return getCurrencySymbol();
        // }

    }, methods: {
        setTitle(title: string) {
            document.title = title + " | RATION";
        },
        $t(text: string) {
            const autoTrans = () => {
                let trans = text.replaceAll("_", " ");
                return (trans.charAt(0).toUpperCase() + trans.slice(1));
            }
            try {
                let trans = this.$i18n.t(text);
                return trans == text ? autoTrans() : trans;
            } catch (e) {
                return "";
            }
        },

        simplebar_scroll(simplebar, fast = false) {
            const maxPosition = simplebar.getContentElement().scrollHeight;
            const time = fast ? 6 : 12;

            let scrollPosition = simplebar.getScrollElement().scrollTop;
            const interval = setInterval(function () {
                scrollPosition += 2;
                simplebar.getScrollElement().scrollTop = scrollPosition;
                if (scrollPosition > maxPosition) clearInterval(interval);
            }, time);
        },

        getFormattedTimeFromTime(time: string, params?: { second?: boolean, minute?: boolean, hour?: boolean, meridiem?: boolean, separator?: string }): string {

            params = {
                ...{
                    second: true, minute: true, hour: true, meridiem: true, separator: ":"
                }, ...params,
            };
            if (time == null) return '';
            let times = time.split(":");


            let hour = "0", minute = "0", second = "0";


            if (times.length == 3) {
                hour = times[0];
                minute = times[1];
                second = times[2];
            } else if (times.length == 2) {
                hour = times[0];
                minute = times[1];
            } else {
                hour = times[0];
            }
            let meridiem = 'AM';
            if (params.meridiem) {
                if (parseInt(time) > 12) {
                    meridiem = 'PM';
                    hour = (parseInt(time) - 12).toString();
                }
            }

            let timeText = "";
            if (params.hour) timeText += hour;
            if (params.minute) {
                if (params.hour) timeText += params.separator
                timeText += minute
            }
            if (params.second) {
                if (params.minute) timeText += params.separator
                timeText += second;
            }
            if (params.meridiem) {
                timeText += (" " + meridiem);
            }


            return timeText;
        },
        getFormattedDateTime(date: String | Date, formatter?: { month?: string }) {
            if (date == null)
                return "-";
            if (!(date instanceof Date)) {
                date = new Date(date.toString());
            }

            const monthFormat = formatter?.month ?? "mmmm";

            const timeFormat = getTimeFormat();
            let format = "dd " + monthFormat + ", yyyy H:MM";
            if (timeFormat === '12h') {
                format = "dd " + monthFormat + ", yyyy h:MM TT";
            }
            return dateFormat(DateUtil.convertUTCDateToLocalDate(date), format);
        },
        getFormattedDate(date, params?: { date?: boolean | undefined, month?: boolean | undefined, year?: boolean | undefined, short?: boolean | undefined } | undefined) {
            try {
                if (!(date instanceof Date)) {
                    date = new Date(date.toString());
                }
                let formatter = "";
                const dateFormatter = params?.short ? "dd" : "dd";
                const monthFormatter = params?.short ? "mmm" : "mmmm";
                const yearFormatter = params?.short ? "yy" : "yyyy";
                if (params != null) {
                    if (params.date == null || params.date) {
                        formatter += (dateFormatter + " ");
                    }
                    if (params.month == null || params.month) {
                        formatter += (monthFormatter + ((params.year == null || params.year) ? ", " : ""));
                    }
                    if (params.year == null || params.year) {
                        formatter += (yearFormatter);
                    }
                } else {
                    formatter = dateFormatter + " " + monthFormatter + ", " + yearFormatter;
                }

                return dateFormat(DateUtil.convertUTCDateToLocalDate(date), formatter);
            } catch (e) {
                return "-";
            }
        },
        getFormattedTime(date) {
            if (!(date instanceof Date)) {
                date = new Date(date.toString());
            }
            const timeFormat = getTimeFormat();
            let format = "H:MM";
            if (timeFormat === '12h') {
                format = "h:MM TT";
            }
            return dateFormat(DateUtil.convertUTCDateToLocalDate(date), format);
        },
        getPrecise(number): number {
            const digits = 2;
            let parsed = parseFloat(number);
            for (let i = digits; i >= 0; i--) {
                let cur = parseFloat(parsed.toFixed(i));
                if (cur === parsed) {
                    return cur;
                }
            }
            return parseFloat(number.toFixed(digits));
        },
        getPreciseCurrency(currency): number {
            const digits = getDigitAfterDecimal();
            let parsed = parseFloat(currency);
            for (let i = digits; i >= 0; i--) {
                let cur = parseFloat(parsed.toFixed(i));
                if (cur === parsed) {
                    return cur;
                }
            }
            return parseFloat(currency.toFixed(digits));
        },
        getDifferenceDateTimeAgo(date: string): string {
            const diff = Math.abs(Date.parse(date) - Date.now()) / 1000;
            const days = Math.floor(diff / (60 * 60 * 24));

            if (days > 1) {
                return days + " " + this.$t("days_ago");
            }
            if (days > 0) {
                return days + " " + this.$t("day_ago");
            }
            const hours = Math.floor(diff / (60 * 60));

            if (hours > 1) {
                return hours + " " + this.$t("hours_ago");
            }
            if (hours > 0) {
                return hours + " " + this.$t("hour_ago");
            }
            const minutes = Math.floor(diff / (60));

            if (minutes > 1) {
                return minutes + " " + this.$t("minutes_ago");
            }
            if (minutes > 0) {
                return minutes + " " + this.$t("minute_ago");
            }

            if (diff > 20) {
                return diff + " " + this.$t("seconds_ago");
            }

            return this.$t('just_now');
        },
        getDurationInText(duration: number): string {
            const days = Math.floor(duration / (60 * 60 * 24));

            if (days > 1) {
                return days + " " + this.$t("days");
            }
            if (days > 0) {
                return days + " " + this.$t("day");
            }
            const hours = Math.floor(duration / (60 * 60));

            if (hours > 1) {
                return hours + " " + this.$t("hours");
            }
            if (hours > 0) {
                return hours + " " + this.$t("hour");
            }
            const minutes = Math.floor(duration / (60));

            if (minutes > 1) {
                return minutes + " " + this.$t("minutes");
            }
            if (minutes > 0) {
                return minutes + " " + this.$t("minute");
            }

            return duration + " " + this.$t("seconds");
        },
        getFormattedCurrency(amount?: number, option?: { currencySpace: boolean, default?: string }) {

            const sign = getCurrencySymbol();
            const position = getCurrencyPosition();

            let currencySpace = option?.currencySpace ?? true;
            if (amount == null) {
                return option?.default ?? "-";
            }


            return ((amount < 0) ? "- " : "") + (position == 'left' ? (sign + (currencySpace ? " " : "")) : "") + this.getPrecise(Math.abs(amount)) + (position == 'right' ? ((currencySpace ? " " : "") + sign) : "");

        },
        test() {
            const data = ["my_cart", "select_an_address", "search", "logout", "orders", "addresses", "wallet", "my_account", "select_address", "refresh", "add_address", "clear_cart", "checkout", "you_need_more", "products_to_checkout", "shopping_more", "home", "selected", "select", "reviews", "featured_products", "latest_products", "popular_products", "Filters", "categories", "ratings", "above", "show_all", "pricing", "to", "apply", "clear", "your_searched_items", "products", "shops", "there_is_no_product_in_this_filter", "clear_filter", "delivery_information", "delivery", "self_pickup", "coupon", "there_is_no_coupon_selected", "choose_coupon", "payment", "cash_on_delivery", "other", "notes", "Qty", "order_information", "place_order", "select_coupon", "expired_at", "not_applicable", "add_items_worth", "more_to_apply_this_offer", "admin_charges", "order_commission", "delivery_commission", "payment_charge", "total", "delivery_charges", "base_charge", "extra_charge", "KM", "charge_per_km", "tax", "taxes", "packaging_charge", "order", "govt._tax", "delivery_charge", "admin_charge", "track_order", null, "ecommerce", "grocery", "food", "pharmacy", "minimize", "dashboard", "management", "zones", "revenues", "POS", "shop_plans", "items", "sub_categories", "coupons", "addons", "manage_shops", "sellers", "payouts", "manage_customers", "customers", "home_layout", "setting", "manage_delivery_boys", "delivery_boys", "business", "setup", "system_settings", "commission_settings", "order_settings", "app_settings", "terms_settings", "info_settings", "home_banners", "file_manager", "units", "fcm_notifications", "color_scheme", "layout_mode", "topbar_color", "leftbar_color", "table_layout", "as_card", "bordered", "center", "full", "all", "view_all_transactions", "view_orders", "total_shops", "all_shops", "revenue_stats", "daily", "monthly", "shop_plan_stats", "category_trending", "all_category", "most_rated", "navigation", "my_address", "create_address", "address", "type", "landmark", "city", "pincode", "create", "create_new", "edit_address", "update", "search_by_place", "address_is_updated", "address_is_created", "login", "email_address", "password", "there_is_no_any_saved_address", "my_wallet", "current_balance", "add_money", "amount", "gateways", "razorpay", "paystack", "transactions", "your_cart_is_empty", "go_to_shopping", "their_is_no_any_orders", "variants", "edit_in_cart", "similar_products", "description", "there_is_no_review_yet", "view_all", "there_is_no_shop_in_this_filter", "closed", "days", "delivery_time", "shop_timing", "day", "time", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday", "pos", "select_shop_to_continue", "shop", "option_available", "edit", "order_amount", "addon_amount", "discount", "customer", "select_customer", "paid", "cash", "card", "change_amount", "paid_amount", "order_customize", "enter_a_tax", "tax_type", "enter_a_discount", "discount_type", "create_customer", "first_name", "last_name", "mobile_number", "email", "add_to_cart", "customer's_reviews", "product_remove_from_favorite", "product_added_to_favorite", "ID", "name", "open", "actions", "there_is_no_any_shops", "active", "not_approved", "find_shop", "add_new", "print", "download", "Excel", "CSV", "export", "previous", "next", "settings", "plans", "shop_information", "owner_information", "edit_owner", "bank_details", "bank_name", "account", "edit_shop", "general", "module", "select_owner", "meta", "logo", "square", "storage", "cover", "landscape", "cover_image", "pick_from_map", "location", "zone", "select_zone", "state", "country", "latitude", "enter_a_latitude_or_pick_from_map", "longitude", "enter_a_longitude_or_pick_from_map", "folders", "this_address_is_not_valid", "order_placed", "notification", "plan", "delivery_boy", "system", "manage_employee", "roles", "employees", "go_to_home", "edit_home_layout", "upgrade_to_best_plans", "go_to_premium", "current_plan", "manage_inventory", "product_selling", "highest", "product_rating", "latest_review", "edit_order", "order_details", "hide_products", "invoice", "image", "quantity", "effected_price", "sub_total", "extra_charges", "customer_detail", "OTP", "payment_details", "status", "set_paid", "accept_order", "you_can_accept_or_reject_this_order", "describe_if_you_reject_or_cancel_order", "reject", "cancel", "accept", "coupon_discount", "review", "past", "order_date", "there_is_no_any_orders", "completed", "PDF", "unpaid", "track", "order_invoice", "customer_information", "shipping_address", "Print", "item", "item_cost", "show", "order_review", "shop_review", "update_review", "delivery_review", "product_reviews", "review_updated", "product_review_added", "add_review", "shop_review_added", "product_review_updated", "order_cancelled", "shop_settings", "setups", "currently_open", "self_delivery", "take_away", "open_for_delivery", "order_options", "min_delivery_time", "max_delivery_time", "minutes", "hours", "delivery_time_type", "time_type", "min_order_amount", "delivery_options", "delivery_range", "in_meter", "delivery_range_in_meter", "minimum_delivery_charge", "delivery_charge_multiplier", "tax_info", "create_schedule_for", "open_at", "close_at", "add_time", "confirmation", "are_you_sure_to_delete_time_for", "remove_time", "show_reviews", "added_to_cart", "show_cart", "there_is_no_any_zone", "inactive", "title", "price", "there_is_no_any_plans", "no_offer", "options", "need_prescription", "you_will_save", "with_this_code", "delivery_free", "upload_prescription", "prescription", "active_layout", "save_&_preview", "save", "selected_banners", "Banners", "home_layout_is_updated", "selected_products", "selected_shops", "there_is_no_any_customers", "Delivery", "Self pickup", "product_removed_from_cart", "category", "rating", "selling", "there_is_no_any_products", "showing_all_shop", "edit_product", "variant", "product", "connect_with_any_your_product", "attach_this_product_with_some_other_product", "product_name", "sub_category", "select_sub_category", "select_addon_if_you_want", "there_is_no_addons_in_the_shop", "images", "availability", "not_set", "unit_type", "select_unit_type_if_you_want", "unit_title", "new", "option", "stock", "SKU", "unique_sku", "barcode", "unique_barcode_(if_you_want)", "set_out_of_stock", "set_availability_for_this_product", "available_from", "available_to", "product_is_updated", "there_is_no_any_active_order", "edit_category", "select_module", "inactive_alert", "there_is_few_other_things_also_become_inactive", "connected_to", "related_sub_categories", "related_products", "there_is_no_any_category", "there_is_no_any_sub_categories", "edit_sub_category", "create_category", "category_is_edited", "code", "duration", "min_order", "max_discount", "limit", "there_is_no_any_coupons", "favorites", "there_is_no_product_in_favorites", "there_is_no_shop_in_favorites", "shop_added_to_favorite", "shop_removed_from_favorite", "log_in", "autofill", "create_product", "main_product", "blank", "LOG_IN", "customer+login", "customer_login", "don't_have_an_account", "register", "looking_for_admin_panel", "customer_register", "referral", "please_check_recaptcha", "customer_verification", "you_need_to_verify_account_first", "send", "send_verification_link", "email_sent", "check_your_inbox_and_verify", "create_an_account", "employee", "seller", "self_registration", "log_in_seller", "log_in_customer", "store_registration", "select_login", "dummy_sellers", "shop_registration", "create_shop", "info", "password_(if_you_need_to_change)", "identity", "avatar", "license_number", "you_can_set_fssai_etc", "you_cant_change_in_the_future", "shop_plan", "you_pay_charges_bases_on_plan", "pick_location_from_map_or_manually_write", "log_in_admin", "seller_panel", "upgrade_plan", "select_plan", "upgrade_to", "started_at", "ended_at", "there_is_no_any_revenue", "there_is_no_any_addons", "shop_reviews", "date_&_time", "there_is_no_any_reviews", "create_addon", "enter_addon_name", "employee_roles", "permissions", "there_is_no_any_roles", "edit_role", "create_role", "delivery boys", "file_managers", "admins", "role", "there_is_no_any_admin", "super_admin", "edit_employee", "edit_seller", "edit_admin", "select_role", "there_is_no_any_seller", "owner", "be_careful", " you_will_receive_all_payments_in_this_bank", "enter_a_bank_name", "account_number", "enter_a_account_number", "seller_image_deleted", "admin_image_deleted", "create_employee", "create_admin", "create_seller", "manage_employees", "home_layouts", "business_setups", "role_is_updated", "business_settings", "modules", "add_new_shop", "files", "information", "upload_images", "upload_zip", "note", "only_upload_zip_with_contains_images", "only_zip_allowed", "please_upload_zip_file", "revenue", "order_id", "admin_revenues", "extracted", "veg", "non_veg", "vegan", "edit_customer", "enter_a_password_(if_you_need_to_change)", "set_mobile_number_as_verified", "set_email_as_verified", "edit_zones", "edit_zone", "coordinates", "draw_a_zone", "related_shops", "related_addons", "create_zones", "create_zone", "polygon_is_created", "zone_is_updated", "edit_shop_plan", "sub_title", "admin_commission", "admin_commission_type", " product_is_created", "shop_plan_updated", "you_need_to_upgrade_plan", "upgrade", "there_is_no_any_sellers", "not_assigned", "edit_employees", "seller_will_receive_all_payments_in_this_bank", "create_owner", "you_can_set_fssai_number_etc", "shop_revenues", "payout", "there_is_no_any_shop_revenues", "not_paid", "shop_payouts", "pay_to_shop", "take_from_shop", "till_date", "there_is_no_any_payouts", "create_shop_payout", "filter", "payout_entry", "pay", "take", "total_take_from_shop", "shop_revenue", "total_pay_to_shop", "delete_confirmation", "are_you_sure_to_delete_this_review", "delete", "preview", "select_type", "food_type", "create_shop_employee", "for_shop", "is_owner", "create_shop_owner", "customer_setting", "customer_settings", "email_verification", "mobile_number_verification", "customer_referral_amount", "customer_wallet", "wallet_transactions", "customer_transaction", "customer_transactions", "payment_method", "there_is_no_any_transactions", "active_for_delivery", "earning", "there_is_no_any_delivery_boy", "delivery_based", "global", "create_delivery_boy", "salary_based", "identity_type", "select_identity_type", "identity_number", "vehicle_number", "identity_image", "delivery_boy_will_receive_all_payments_in_this_bank", "edit_delivery_boy", "delivery_boy_is_updated", "sub_category_is_edited", "edit_coupon", "first_order", "select_shop", "coupon_is_edited", "edit_addon", "edit_addons", "addon_updated", "seller_is_updated", "there_is_no_owner_allocated", "shop_is_updated", "settings_are_updated", "customer_is_edited", "customer_settings_is_updated", "system_setting", "business_setup", "can_shop_self_register", "currency", "left", "right", "currency_position", "12h", "24h", "time_format", "digit_after_decimal", "app_setting", "commission_setting", "order_commission_type", "delivery_commission_type", "payment_charge_type", "commission_settings_is_updated", "system_setting_is_updated", "customer_app", "android_app_min_version", "android_app_url", "ios_app_min_version", "ios_app_url", "delivery_boy_app", "seller_app", "info_setting", "be_careful: ", "this_information_are_publicly_available", "contact_details", "social_media", "instagram", "whatsapp", "facebook", "twitter", "linkedin", "pinterest", "company_location", "about_us", "order_setting", "admin", "cancel_by_seller", "order_reject", "order_accept", "order_processing", "estimate_time_change", "delivery_boy_accept", "order_ready", "out_for_delivery", "order_deliver", "assign_order", "new_order", "order_payment_done", "cancel_by_customer", "order_resubmit", "delivery_boy_reject", "order_delivered", "enable_admin_order_notification", "delivery_setting", "can_delivery_boy_reject_order", "enable_delivery_otp_verification", "max_order_assign_to_delivery_boy", "term_setting", "terms_&_privacy", "privacy_policies", "terms_conditions", "banners", "there_is_no_any_banners", "edit_banner", "edit_home_banner", "select_product", "banner_is_created", "delivery_boy_reviews", "there_is_no_any_reviews_yet", "delivery_boy_revenues", "delivery_boy_payouts", "pay_to_delivery_boy", "take_from_delivery_boy", "payed_at", "there_is_no_any_payout", "admin_is_created", "admin_is_edited", "there_is_no_any_units", "unit", "edit_unit", "enter_unit_type", "unit_updated", "fcm_notification", "body", "notifications", "documentation", "there_is_no_any_modules", "E-Commerce", "Grocery", "Food", "Pharmacy", "edit_module", "module_updated", "lowest", "pay_to_admin", "delivery_type", "set_estimate_time", "update_est_time", "show_products", "all_products_are_hidden", "set_order_to_ready", "plan_is_upgraded", "hour", "make_it_active", "self_delivery_is_not_active", "shop_is_edited", "option_updated", "pinned", "there_is_no_any_categories", "there_is_no_any_sub_category", "shop_payout", "taken_from_admin", "paid_to_admin", "seller_is_created", "update_profile", "change_password", "confirm_password", "update_password", "profile_is_updated", "both_password_are_not_same", "do_shopping_first", "create_units", "create_plan", "create_shop_plans", "not_activate_any_plan", "add_item_to_cart", "please_choose_shop", "please_enter_amount", "review_removed", "please_choose_customer", "add_to_wallet", "used", "added", "please_enter_amount_more_than", "money_added_to_wallet", "create_delivery_boy_payout", "total_take_from_delivery_boy", "delivery_boy_revenue", "please_choose_delivery_boy", "order_settings_is_updated", "app_settings_is_updated", "terms_settings_is_updated", "info_settings_is_updated", "seller_is_edited", "product_removed_from_favorite", "order_is_paid", "wait_for_payment", "wait_until_customer_confirm_payment_process", "order_rejected", "shop_review_updated", "update_review"];
            console.info(data)
            let d = "";
            for (const datum of data) {
                if (datum != null) {
                    let trans = datum.replaceAll("_", " ");
                    d += '"' + datum + '": "' + (trans.charAt(0).toUpperCase() + trans.slice(1)) + '",\n';
                }
            }

            console.info(d);
        }
    }
};

