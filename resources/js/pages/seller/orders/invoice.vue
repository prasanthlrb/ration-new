<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('order_invoice')"/>

        <PageLoading :loading="page_loading">
            <Row justify-content="center">
                <Col id="printable_area" lg="8">
                    <Card class="print-shadow-none">

                        <CardBody>

                            <div class="position-absolute top-0 end-0 m-3 z-5">

                                <Button class="d-print-none" color="primary" flexed-icon variant="soft"
                                        @click="print">
                                    <Icon icon="print" size="sm" type="msr"/>
                                    <span class="ms-1-5">{{ $t('print') }}</span>
                                </Button>

                            </div>


                            <div class="d-flex">
                                <div class="w-50">
                                    <h3 class="card-title">{{ $t('invoice') }} <b class="ms-1">#{{
                                            order.id
                                        }}</b>
                                    </h3>
                                </div>
                                <div class="w-50">
                                    <div class="">
                                        <p class="font-13 mb-1"><strong>Order Date: </strong> &nbsp;&nbsp;&nbsp;
                                            {{ getFormattedDateTime(order.created_at) }}</p>
                                        <p class="font-13 mb-0"><strong>Order Status: </strong>&nbsp;&nbsp;&nbsp;
                                            <span
                                                class="badge bg-success">Paid</span></p>

                                    </div>
                                </div>
                            </div>

                            <div class="d-flex mt-4">
                                <div class="w-50">
                                    <span class="font-13 text-muted">{{ $t('customer_information') }}</span>
                                    <address>
                                        {{ order.customer.first_name }} {{ order.customer.last_name }}<br>
                                        <abbr title="Mobile Number">P:</abbr> {{ order.customer.mobile_number }}
                                    </address>
                                </div>
                                <div class="w-50">
                                    <span class="font-13 text-muted">{{ $t('shipping_address') }}</span>

                                    <address v-if="order.address">
                                        Address: {{ order.address.address }}<br/>
                                        {{ order.address.landmark }}<br v-if="order.address.landmark"/>
                                        {{ order.address.city }} - {{ order.address.pincode }}
                                    </address>
                                    <address v-else>{{ $t('on_shop') }}</address>
                                </div>
                            </div>


                            <Row>
                                <Col lg="12">
                                    <div class="table-responsive">
                                        <table class="table mt-4">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ $t('item') }}</th>
                                                <th>{{ $t('quantity') }}</th>
                                                <th>{{ $t('item_cost') }}</th>
                                                <th class="text-end">{{ $t('total') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <template v-for="(item, index) in order.items">
                                                <tr>
                                                    <td>{{ parseInt(index) + 1 }}</td>
                                                    <td>
                                                        <b>{{ item.product.name }}</b> <br>
                                                        {{ item.product.unit_title }} -
                                                        {{ item.product_option.option_value }}
                                                    </td>
                                                    <td>{{ item.quantity }}</td>
                                                    <td>
                                                        {{
                                                            getFormattedCurrency(item.product_option.calculated_price)
                                                        }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{
                                                            getFormattedCurrency(item.product_option.calculated_price * item.quantity)
                                                        }}
                                                    </td>
                                                </tr>

                                                <tr v-for="addon in item.addons" v-if="item.addons">
                                                    <td><i> - {{ $t('addon') }}</i></td>
                                                    <td>{{ addon.addon.name }}
                                                    </td>
                                                    <td>{{ addon.quantity }}</td>
                                                    <td>
                                                        {{
                                                            getFormattedCurrency(addon.price)
                                                        }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{
                                                            getFormattedCurrency(addon.price * addon.quantity)
                                                        }}
                                                    </td>
                                                </tr>

                                            </template>


                                            </tbody>
                                        </table>
                                    </div> <!-- end table-responsive-->
                                </Col>
                            </Row>

                            <div class="d-flex">
                                <div class="w-50">
                                    <div class="clearfix pt-3">
                                        <h6 class="text-muted">Notes:</h6>
                                        <small>
                                            {{ order.notes }}
                                        </small>
                                    </div>
                                </div> <!-- end col -->
                                <div class="w-50">
                                    <div class="float-end mt-3 mt-sm-0">
                                        <p><b>{{ $t('sub_total') }}:</b> <span
                                            class="float-end">{{
                                                getFormattedCurrency(order.order_amount)
                                            }}</span></p>
                                        <p v-if="order.delivery_charge"><b>{{ $t('delivery_charge') }}:</b>
                                            <span
                                                class="float-end">&nbsp;&nbsp; {{
                                                    getFormattedCurrency(order.delivery_charge)
                                                }}</span>
                                        </p>

                                        <p v-if="order.coupon_discount"><b>{{ $t('coupon_discount') }}:</b>
                                            <span
                                                class="float-end">&nbsp; &nbsp;  - {{
                                                    getFormattedCurrency(order.coupon_discount)
                                                }}</span>
                                        </p>
                                        <p><b>{{ $t('extra_charges') }}:</b> <span
                                            class="float-end">&nbsp;&nbsp; {{
                                                getFormattedCurrency(get_total_charges)
                                            }}</span>
                                        </p>
                                        <hr class="dashed"/>

                                        <p class="fw-semibold font-18">{{ $t('total') }}:
                                            <span class="font-20 float-end">
                                            {{ getFormattedCurrency(order.total) }}
                                             </span>
                                        </p>

                                    </div>
                                    <div class="clearfix"></div>
                                </div> <!-- end col -->
                            </div>

                        </CardBody>

                    </Card>
                </Col>
            </Row>
        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/seller/layouts/Layout.vue";
import Request from "@js/services/api/request";
import {sellerAPI} from "@js/services/api/request_url";
import {handleException} from "@js/services/api/handle_exception";
import Order, {IOrder} from "@js/types/models/order";
import OrderStatus from "@js/types/models/order_status";
import {defineComponent} from "vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IData} from "@js/types/models/data";
import {Components} from "@js/components/components";
import {IBreadcrumb} from "@js/types/models/models";


export default defineComponent({
    name: 'Order Invoice - Seller',
    components: {Layout, ...Components},
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            id: this.$route.params.id,
            order: null as IOrder,
        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('orders'),
                    route: 'seller.orders.index'
                },
                {
                    text: this.$t('invoice'),
                    active: true,
                },
            ];
        },
        get_total_charges() {
            return Order.getTotalCharges(this.order);
        },

    },
    methods: {
        async getOrders() {
            try {
                const response = await Request.getAuth<IData<IOrder>>(sellerAPI.orders.show(this.id));
                this.order = response.data.data;
                this.page_loading = false;

            } catch (error) {
                handleException(error);
            }
        },

        getPaymentFromText(order) {
            return Order.getPaymentTextFromOrder(order);
        },
        getTypeFromText(payment) {
            return Order.getTypeText(payment);
        },

        print() {

            try {

                let html = '<HTML lang="en">\n<HEAD>\n<title></title>';
                if (document.getElementsByTagName != null) {
                    const headTags = document.getElementsByTagName("head");
                    if (headTags.length > 0) html += headTags[0].innerHTML;
                    const footerTags = document.getElementsByTagName("footer-script");
                    if (footerTags.length > 0) html += footerTags[0].innerHTML;

                }

                html += "\n</HEAD>\n<BODY>\n";
                const printReadyElem = document.getElementById('printable_area');

                if (printReadyElem != null) html += printReadyElem.innerHTML;
                else {
                    alert("Error, no contents.");
                    return;
                }

                html += "\n</BODY>\n</HTML>";
                const printWin = window.open("", "print");
                printWin.document.open();
                printWin.document.write(html);
                printWin.document.close();

                printWin.print();
                printWin.onafterprint = (function () {
                    printWin.close();
                })

            } catch (e) {
                console.log("Here");
            }
        }

    },
    async mounted() {
        this.setTitle(this.$t('invoice'));
        await this.getOrders();
    }
});

</script>
