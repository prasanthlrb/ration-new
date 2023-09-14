<template xmlns="http://www.w3.org/1999/html">
    <Layout id="customer-home">


        <PageLoading :loading="page_loading">
            <div v-if="getBannerLayout" class="mb-3">
                <swiper :freeMode="false" :loop="true" :modules="modules" :navigation="true" :slideToClickedSlide="true"
                    :slidesPerView="2" :spaceBetween="24" :watchSlidesProgress="true" class="mySwiper">
                    <swiper-slide v-for="banner in getBannerLayout.items" :key="banner.id">
                        <router-link :to="banner.product != null ? { name: 'customer.products.show', params: { id: banner.product.id } } :
                            { name: 'customer.shops.show', params: { id: banner.shop.id } }">
                            <NetworkImage :src="banner.image" class="img-fluid" height="320" object-fit="cover" rounded
                                width="100%" />
                        </router-link>
                    </swiper-slide>
                </swiper>
            </div>

            <Card :class="[{ 'pinned': isCategoryPinned }]" class="category-container ">
                <div class="d-flex">
                    <template v-for="category in categories">
                        <VItem align-items="center" class="cursor-pointer" display="flex" flex="column" style="width: 100px"
                            @click="goToFilterCategory(category)">
                            <NetworkImage :size="60" :src="category.image" circular />
                            <div>
                                <p class="mb-0 fw-medium mt-0-5 text-center">{{ category.name }}</p>
                            </div>
                        </VItem>
                    </template>
                </div>
            </Card>


            <Row>

                <Col lg="12">
                <template v-for="layout in getLayouts" :key="layout.id">


                    <template v-if="isShopLayout(layout)">

                        <swiper :freeMode="false" :loop="true" :modules="modules" :navigation="true"
                            :slideToClickedSlide="true" :slidesPerView="3" :spaceBetween="24" :watchSlidesProgress="true"
                            class="mySwiper">
                            <swiper-slide v-for="shop in layout.items" :key="shop.id">
                                <router-link :to="{ name: 'customer.shops.show', params: { id: shop.id } }"
                                    class="nav-link">
                                    <Card class="overflow-hidden shadow-none">
                                        <NetworkImage :src="shop.cover_image" height="240" width="100%"
                                            object-fit="cover" />
                                        <CardBody class="p-2 px-2-5">

                                            <div class="d-flex align-items-center">
                                                <NetworkImage :src="shop.logo" object-fit="cover" circular size="40" />
                                                <div class="ms-2-5">
                                                    <h5 class="fw-medium mt-0 mb-0-5">{{ shop.name }}</h5>
                                                    <div>
                                                        <StarRating :rating="shop.rating" :size="14" star-spacing="1" />
                                                        <span class="ms-1 font-13"> ({{
                                                            getPrecise(shop.rating)
                                                        }} - {{ shop.ratings_count }} {{
    this.$t('reviews')
}})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </CardBody>
                                    </Card>
                                </router-link>
                            </swiper-slide>

                        </swiper>
                    </template>

                    <template v-if="isProductLayout(layout)">

                        <Card class="shadow-none">
                            <CardBody>
                                <PageHeader :title="getTitleFromLayout(layout)"></PageHeader>

                                <swiper :freeMode="false" :loop="false" :modules="modules" :navigation="true"
                                    :slideToClickedSlide="true" :slidesPerView="5" :spaceBetween="24"
                                    :watchSlidesProgress="true" class="mySwiper">
                                    <swiper-slide v-for="product in layout.items" :key="product.id">
                                        <router-link :to="{ name: 'customer.products.show', params: { id: product.id } }"
                                            class="nav-link">
                                            <div class="border border-dashed rounded p-2-5">
                                                <div class="text-center">
                                                    <NetworkImage :src="getProductImage(product)" class="img-fluid"
                                                        height="120" object-fit="contain" rounded />
                                                </div>

                                                <h6 class="text-muted mt-2-5 mb-1 fw-medium">{{
                                                    product.sub_category?.name
                                                }}</h6>
                                                <h5 class="max-lines mb-0 fw-medium mt-0">{{ product.name }}</h5>
                                                <div class="d-flex align-items-center justify-content-between mt-0-5">
                                                    <div>
                                                        <StarRating :rating="product.rating" :size="12" />
                                                        <span class="font-13"> ({{ getRatingText(product) }})</span>
                                                    </div>
                                                    <span class="font-15 fw-medium"> {{
                                                        getFormattedCurrency(getMinPrice(product))
                                                    }}</span>

                                                </div>

                                            </div>
                                        </router-link>
                                    </swiper-slide>
                                </swiper>
                            </CardBody>
                        </Card>

                        <!--                            <swiper-->
                        <!--                                :freeMode="false"-->
                        <!--                                :loop="false"-->
                        <!--                                :modules="modules"-->
                        <!--                                :navigation="true"-->
                        <!--                                :slideToClickedSlide="true"-->
                        <!--                                :slidesPerView="5"-->
                        <!--                                :spaceBetween="24"-->
                        <!--                                :watchSlidesProgress="true"-->
                        <!--                                    class="mySwiper"-->
                        <!--                                >-->
                        <!--                                    <swiper-slide v-for="product in layout.items" :key="product.id">-->
                        <!--                                        <router-link-->
                        <!--                                            :to="{name:'customer.products.show', params: {id: product.id}}"-->
                        <!--                                            class="nav-link">-->
                        <!--                                            <Card>-->
                        <!--                                                <CardBody>-->
                        <!--                                                    <div class="text-center">-->
                        <!--                                                        <NetworkImage-->
                        <!--                                                            :src="getProductImage(product)"-->
                        <!--                                                            class="img-fluid"-->
                        <!--                                                            object-fit="contain"-->
                        <!--                                                            height="120" rounded/>-->
                        <!--                                                    </div>-->

                        <!--                                                    <h6-->
                        <!--                                                        class="text-muted fw-medium mt-2-5">{{-->
                        <!--                                                            product.sub_category?.name-->
                        <!--                                                        }}</h6>-->
                        <!--                                                    <h5-->
                        <!--                                                        class="max-lines mb-1 fw-medium">{{ product.name }}</h5>-->
                        <!--                                                    <div>-->
                        <!--                                                        <StarRating :rating="product.rating" :size="14"/>-->
                        <!--                                                        <span class="font-13"> ({{ getRatingText(product) }} - {{-->
                        <!--                                                                product.ratings_count-->
                        <!--                                                            }} {{-->
                        <!--                                                                this.$t('reviews')-->
                        <!--                                                            }})</span>-->
                        <!--                                                    </div>-->
                        <!--                                                    <p class="mb-0 mt-1">By-->
                        <!--                                                        <router-link-->
                        <!--                                                            :to="{name:'customer.shops.show', params: {id: product.shop?.id}}">-->
                        <!--                                                            {{ product.shop?.name }}-->
                        <!--                                                        </router-link>-->
                        <!--                                                    </p>-->
                        <!--                                                </CardBody>-->
                        <!--                                            </Card>-->
                        <!--                                        </router-link>-->
                        <!--                                    </swiper-slide>-->
                        <!--                                </swiper>-->


                    </template>


                </template>
                </Col>
            </Row>

        </PageLoading>


        <div class="module-selector">
            <div id="accordionPanelsStayOpenExample" class="accordion bg-transparent">
                <div class="accordion-item border-0">
                    <div id="module-selector-accordion" class="accordion-collapse collapse">
                        <div class="accordion-body p-0 pt-1">
                            <template v-for="module in remain_module">
                                <div class="d-flex justify-content-end align-items-center gap-3 cursor-pointer"
                                    @click="onSelectModule(module)">
                                    <span class="font-17 text-white text-uppercase fw-medium">{{ module.title }}</span>
                                    <VItem class="mb-1  p-2-5  rounded-circle bg-card shadow">
                                        <img :alt="module.title" :src="module.image" width="36">
                                    </VItem>
                                </div>
                            </template>

                        </div>
                    </div>

                    <h2 class="accordion-header p-0">
                        <div class="accordion-button p-0 shadow-none arrow-none d-flex
                            justify-content-end align-items-center gap-3 cursor-pointer"
                            data-bs-target="#module-selector-accordion" data-bs-toggle="collapse">
                            <span class="font-17 text-white text-uppercase fw-medium">{{
                                selected_module.title
                            }}</span>
                            <div class="mb-1 p-2-5  rounded-circle bg-card shadow-lg border border-primary">
                                <img :alt="selected_module.title" :src="selected_module.image" width="36">
                            </div>
                            <!--                                <img :alt="selected_module.title" :src="selected_module.image" width="34">-->
                        </div>
                    </h2>
                </div>
            </div>
        </div>

    </Layout>
</template>

<script lang="ts">

import Layout from "@js/pages/customer/layouts/Layout.vue";
import { defineComponent } from "vue";
import Request from "@js/services/api/request";
import { IData } from "@js/types/models/data";
import { customerAPI } from "@js/services/api/request_url";
import { handleException } from "@js/services/api/handle_exception";
import { HomeLayout, IHomeLayout } from "@js/types/models/home_layout";
import { Components } from "@js/components/components";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import { ICategory } from "@js/types/models/category";
import Product, { IProduct } from "@js/types/models/product";
import { IShop } from "@js/types/models/shop";
import StarRating from "@js/components/shared/StarRating.vue";
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import "swiper/css/free-mode"
import "swiper/css/thumbs"
import { Controller, FreeMode, Navigation, Thumbs, Zoom } from 'swiper';
import { IModule, Module } from "@js/types/models/module";
import { Collapse } from "bootstrap";
import { useCustomerDataStore } from "@js/services/state/states";

export default defineComponent({
    components: { StarRating, Layout, Swiper, SwiperSlide, ...Components },
    mixins: [UtilMixin],
    data() {
        return {
            page_loading: true,
            modules: [Zoom, FreeMode, Navigation, Controller, Thumbs],
            layouts: [] as IHomeLayout[],
            categories: [] as ICategory[],
            isCategoryPinned: false,
            selected_module: {} as IModule,
            collapse_accordion: {} as Collapse
        }
    },
    methods: {
        async getData() {
            this.page_loading = true;

            try {
                const categoryResponse = await Request.getAuth<IData<ICategory[]>>(Request.addCustomerModule(customerAPI.categories.get));
                this.categories = categoryResponse.data.data;
                const response = await Request.getAuth<IData<IHomeLayout[]>>(Request.addCustomerModule(customerAPI.home_layouts.get));
                this.layouts = response.data.data;

            } catch (error) {
                handleException(error);
            } finally {
                this.page_loading = false;
            }
        },
        getTitleFromLayout(layout: IHomeLayout) {
            return this.$t(layout.type);
        },
        isShopLayout(layout: IHomeLayout) {
            return HomeLayout.isShop(layout.type);
        },
        isProductLayout(layout: IHomeLayout) {
            return HomeLayout.isProduct(layout.type);
        },

        getMinPrice(product: IProduct) {
            return Product.getMinPrice(product);
        },
        getProductImage(product: IProduct) {
            return Product.getImageUrl(product);
        },
        getFloorRating(item: IProduct | IShop) {
            return Math.floor(this.getRating(item));
        },
        getRatingText(item: any): string {
            return this.getRating(item).toFixed(1);
        },
        getRating(item: any): number {
            return item.rating;

        },
        goToFilterCategory(category: ICategory) {
            this.$router.push({ name: 'customer.search', query: { categories: category.id } });
        },
        addBackdrop() {
            if (this.backdrop_element == null) {
                this.backdrop_element = document.createElement('div');
                this.backdrop_element.classList.add(...['offcanvas-backdrop', 'fade', 'show', 'opacity-80']);
                const self = this;
                this.backdrop_element.addEventListener('click', () => {
                    self.closeAccordion();
                })
            }

            document.body.appendChild(this.backdrop_element);
        },
        removeBackdrop() {
            if (this.backdrop_element != null)
                this.backdrop_element.remove();
        },
        closeAccordion() {
            this.collapse_accordion?.hide();
        },
        onSelectModule(module: IModule) {
            if (this.selected_module.id != module.id) {
                this.selected_module = module;
                useCustomerDataStore().updateModuleId(module.id);
            }
            this.getData();
            this.closeAccordion();
        }

    },
    computed: {
        remain_module() {
            const self = this
            return Module.getCachedModules().filter((module) => {
                return self.selected_module.id != module.id;
            })
        },
        getBannerLayout(): IHomeLayout | null {
            for (const layout of this.layouts) {
                if (layout.type === 'home_banner')
                    return layout;
            }
            return null;
        },
        getLayouts(): IHomeLayout[] {
            return (this.layouts ?? []).filter(function (layout) {
                if (layout.type !== 'other') {
                    return layout;
                }
            });
        }
    },
    mounted() {
        this.setTitle(this.$t('home'))
        this.getData();
        const self = this;
        document.defaultView.onscroll = () => {
            self.isCategoryPinned = window.scrollY > 350;
        }

        let store = useCustomerDataStore();
        this.selected_module = Module.getModuleFromId(store.module_id);

        this.collapse_accordion = new Collapse('#module-selector-accordion', {
            toggle: false
        });
        const collapseElement = document.getElementById('module-selector-accordion')

        if (this.collapse_accordion != null && collapseElement != null) {
            collapseElement.addEventListener('show.bs.collapse', () => {
                self.addBackdrop();
            });
            collapseElement.addEventListener('hide.bs.collapse', () => {
                self.removeBackdrop();
            });
        }
    }
});

</script>

<style scoped>
.category-container {
    padding: 1rem;
    margin-top: 1px;
    position: sticky;
    top: 71px;
    align-items: center;
    z-index: 999;
    transition: all 0.1s ease-in-out;
}

.category-container.pinned {
    margin-left: -140px;
    margin-right: -140px;
}
</style>

