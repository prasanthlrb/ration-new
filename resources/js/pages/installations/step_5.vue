<template>
    <div>

        <PageLoading :loading="page_loading" class="mt-5">
            <Row justify-content="center">
                <Col lg="5">
                    <Card>
                        <CardHeader icon="category" title="Almost done">
                            <div class="float-end">
                                Step 5
                            </div>
                        </CardHeader>
                        <CardBody class="">

                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <FormCheckbox v-model="confirmed" :errors="errors" label="I follow all the steps"
                                              name="confirmed" no-spacing></FormCheckbox>
                                <Button color="bluish-purple" flexed-icon @click="finish">
                                    Finish ->
                                </Button>
                            </div>

                            <div class="fw-medium d-flex align-items-center justify-content-center mt-4">
                                <Icon class="me-1-5" color="success" icon="check_circle" size="18"></Icon>
                                Installation is completed
                            </div>


                            <p class="mt-5 text-center fw-medium">Click on finished. Then check all the system setting
                                and confirm. </p>


                            <div :class="[{'opacity-65':!finished}]" >
                                <div class="border border-dashed p-1-5 mb-2 px-3">
                                    <router-link :to="{name: 'admin.setups.info_settings'}" target="_blank">
                                        - Information Settings
                                    </router-link>
                                </div>
                                <div class="border border-dashed p-1-5 px-3 mb-3">
                                    <router-link :to="{name: 'admin.setups.app_settings'}" target="_blank" >
                                        - App Settings
                                    </router-link>
                                </div>
                            </div>


                        </CardBody>


                    </Card>
                </Col>
            </Row>

        </PageLoading>

    </div>
</template>

<script lang="ts">

import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IIStep5} from "@js/types/models/installation";
import {DocsNavigation} from "@js/services/navigator_service";
import ValidationErrorMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {installationAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";

export default defineComponent({
    components: {
        ...Components,
    },
    mixins: [ValidationErrorMixin],
    data() {
        return {
            loading: false,
            page_loading: true,
            step: {} as IIStep5,
            confirmed: false,
            finished: false
        }
    },

    methods: {
        goToDocs() {
            DocsNavigation.goToBackend();
        },
        async finish() {
            this.clearAllError();
            if(!this.confirmed){
                this.addError('confirmed', "Check this field");
                return;
            }
            this.loading = true;
            try {
                const response = await Request.postAuth<IIStep5>(installationAPI.step_5.update, {});
                if (response.success()) {
                    this.finished = true;
                    ToastNotification.success("Now you can use admin panel and apps");
                }
                this.clearAllError();
            } catch (error) {
                this.clearAllError();
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error);
                }
            }
            this.loading = false;
        },
        async next() {

        }
    },
    async mounted() {
        try {
            const response = await Request.getAuth<IIStep5>(installationAPI.step_5.get);
            this.step = response.data;
        } catch (error) {
            if (Response.isInstallationFallback(error)) {
                const step = error.response.data.fallback;
                this.$router.replace({name: 'installations.' + step})

            }
        }
        this.page_loading = false;
    }
})

</script>
