<template>
    <Layout>
        <PageHeader :items="breadcrumb_items" :title="$t('edit_unit')"/>

        <PageLoading :loading="page_loading">
            <Row>
                <Col lg="6">
                    <Card>
                        <CardHeader :title="$t('general')" icon="edit_note" type="msr">
                            <div class="float-end">
                                <FormSwitch v-model="unit.active" :errors="errors"
                                            :label="$t('active')"
                                            name="active" no-spacing/>
                            </div>
                        </CardHeader>

                        <CardBody>

                            <Row>
                                <Col lg="6">
                                    <FormInput v-model="unit.type" :errors="errors"
                                               :label="$t('type')" :placeholder="$t('enter_unit_type')" name="type"
                                               required/>
                                </Col>
                                <Col lg="6">
                                    <FormInput v-model="unit.title" :errors="errors" :label="$t('title')"
                                               name="title" required>
                                        <template #label-suffix>
                                            <InfoTooltip
                                                tooltip="It should be unique, so that other unit can't conflict"/>
                                        </template>
                                    </FormInput>
                                </Col>
                            </Row>

                            <TextEditor v-model="unit.description" :label="$t('description')"/>


                            <div class="text-end mt-3">
                                <UpdateButton :loading="loading" @click="update"/>
                            </div>

                        </CardBody>
                    </Card>
                </Col>

            </Row>


        </PageLoading>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/admin/layouts/Layout.vue";
import FormMixin from "@js/shared/mixins/ValidationErrorMixin";
import Request from "@js/services/api/request";
import {adminAPI} from "@js/services/api/request_url";
import Response from "@js/services/api/response";
import {ToastNotification} from "@js/services/toast_notification";
import {handleException} from "@js/services/api/handle_exception";
import {defineComponent} from "vue";
import {Components} from "@js/components/components";
import {IData} from "@js/types/models/data";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {IBreadcrumb} from "@js/types/models/models";
import {IUnit} from "@js/types/models/unit";
import UpdateButton from "@js/components/buttons/UpdateButton.vue";


export default defineComponent({
    name: 'Edit Unit - Admin',
    components: {
        UpdateButton,
        ...Components, Layout
    },
    mixins: [FormMixin, UtilMixin],
    data() {

        return {
            id: this.$route.params.id,
            loading: false,
            page_loading: true,
            unit: {} as IUnit,

        }
    },
    computed: {
        breadcrumb_items(): IBreadcrumb[] {
            return [
                {
                    text: this.$t('unit'),
                    route: 'admin.units.index',
                },
                {
                    text: this.$t('edit'),
                    active: true,
                },
            ];
        }
    },
    methods: {

        async update() {
            this.loading = true;
            try {

                const response = await Request.patchAuth(adminAPI.units.update(this.id), {
                    ...this.unit
                });
                if (response.success()) {
                    ToastNotification.success(this.$t('unit_updated'));
                    this.$router.go(-1);
                }
            } catch (error) {
                if (Response.is422(error)) {
                    this.errors = error.response.data.errors;
                } else {
                    handleException(error)
                }
            }
            this.loading = false;
        },

    },
    async mounted() {
        this.setTitle(this.$t('edit_unit'))
        this.unit = (await Request.getAuth<IData<IUnit>>(adminAPI.units.show(this.id))).data.data;
        this.page_loading = false;
    }
});

</script>
