<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('send_notification')"/>
        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="6" md="8">
                    <Card>
                        <CardHeader :title="$t('notification')" icon="stay_current_portrait" type="msr"></CardHeader>

                        <CardBody>
                            <FormInput v-model="notification.title" :errors="errors"
                                       :label="$t('title')"
                                       name="title" type="text"/>

                            <FormInput v-model="notification.body" :errors="errors"
                                       :label="$t('body')"
                                       name="body" type="text"/>

                            <Row>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_customers" :label="$t('all_customers')"
                                                  name="all_customers"/>
                                </Col>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_sellers" :label="$t('all_sellers')"
                                                  name="all_sellers"/>
                                </Col>
                                <Col lg="4">
                                    <FormCheckbox v-model="notification.all_delivery_boys"
                                                  :label="$t('all_delivery_boys')"
                                                  name="all_delivery_boys"/>
                                </Col>
                            </Row>

                            <div class="text-end">
                                <LoadingButton :loading="loading" @click="send" flexed-icon icon="send">{{ $t('send') }}</LoadingButton>
                            </div>


                        </CardBody>

                    </Card>

                </Col>
            </Row>
        </PageLoading>


    </Layout>
</template>


<script lang="ts">

import Layout from "../layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import Card from "@js/components/Card.vue";
import CardBody from "@js/components/CardBody.vue";
import Badge from "@js/components/Badge.vue";
import PageLoading from "@js/components/PageLoading.vue";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IManualNotification} from "@js/types/models/manual_notification";

export default defineComponent({
    components: {
        PageLoading,
        Badge,
        CardBody,
        Card,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {
        return {
            loading: false,
            page_loading: false,
            notification: {} as IManualNotification,

        }
    },
    computed: {
        breadcrumb_items() {
            return [
                {
                    text: this.$t("manual_notifications"),
                    route: 'admin.manual_notifications.index',
                },
                {
                    text: this.$t('create'),
                    active: true,
                },
            ];
        }
    },
    methods: {
        async send() {
            this.loading = true;
            this.clearAllError();
            try {
                const response = await Request.postAuth(adminAPI.manual_notifications.create, {
                    ...this.notification
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('notification_sent'));
                } else {
                    ToastNotification.unknownError();
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('manual_notification'));
    }
});

</script>
