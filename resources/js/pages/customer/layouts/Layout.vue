<template>
    <div id="wrapper">

        <TopBar :hide-search="hideSearch" />



        <div class="content-page  mt-3">
            <div class="content">
                <div class="container-fluid">
                    <slot />
                </div>
            </div>

        </div>
        <Footer />
    </div>
</template>

<script lang="ts">


import TopBar from "./TopBar.vue";
import { defineComponent } from "vue";
import {
    ILayout, useCustomerDataStore,

    useLayoutStore
} from "@js/services/state/states";
import { IModule, Module } from "@js/types/models/module";
import Icon from "@js/components/Icon.vue";
import VItem from "@js/components/VItem.vue";
import Request from "@js/services/api/request";
import { IData } from "@js/types/models/data";
import { customerAPI } from "@js/services/api/request_url";
import { handleException } from "@js/services/api/handle_exception";
import Footer from "@js/components/Footer.vue";

export default defineComponent({
    components: { Footer, VItem, Icon, TopBar },
    props: {
        hideSearch: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            layout: {} as ILayout,
            selected_module: {} as IModule,
        }
    },
    computed: {
        remain_module() {
            const self = this
            return Module.getCachedModules().filter((module) => {
                return self.selected_module.id != module.id;
            })
        }
    },
    methods: {
        adjustLayout() {
            let html = document.getElementsByTagName('html')[0];
            let theme = this.layout.theme;
            if (this.layout.theme === 'auto') {
                theme = this.getSystemTheme();
            }
            html.setAttribute('data-theme', theme);
            html.setAttribute('data-topbar-color', theme);
            html.removeAttribute('data-layout-mode');
        },
        getSystemTheme() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                return 'dark';
            }
            return 'light';
        },
        watch() {
            const self = this;
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    self.adjustLayout();
                });
            }
        },
        async getInitData() {
            const store = useCustomerDataStore();
            if (store.loaded)
                return;

            try {
                const response = await Request.getAuth<IData<any>>(customerAPI.init.get);
                if (response.success()) {
                    const data = response.data;

                    if (data['customer'] != null) {
                        store.updateUserData(data['customer'])
                    }
                    if (data['customer_addresses'] != null) {
                        store.updateAddresses(data['customer_addresses'])
                    }
                    if (data['carts'] != null) {
                        store.replaceAllCart(data['carts'])
                    }

                }
                store.setLoaded();
            } catch (error) {
                handleException(error);
            } finally {
            }


        },


    },
    mounted() {
        let layoutStore = useLayoutStore();
        this.layout = layoutStore.layout;
        const self = this;
        this.adjustLayout();
        layoutStore.$subscribe((mut, state) => {
            self.layout = state.layout;
            self.adjustLayout();
        });
        this.watch();
        let store = useCustomerDataStore();
        this.selected_module = Module.getModuleFromId(store.module_id);


    },
    created() {
        this.getInitData();
    }

})
</script>

<style scoped>
.content-page {
    margin-left: 120px;
    margin-right: 120px;
    margin-top: 24px;
    padding-left: 0px !important;
}

.collapsed span {
    visibility: hidden;
}
</style>
