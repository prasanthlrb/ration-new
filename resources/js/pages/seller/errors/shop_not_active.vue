<template>
    <Layout>

        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="error-text-box">
                    <svg viewBox="0 0 600 200">
                        <!-- Symbol-->
                        <symbol id="s-text">
                            <text dy=".35em" text-anchor="middle" x="50%" y="50%">311!</text>
                        </symbol>
                        <!-- Duplicate symbols-->
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="mt-0 mb-2">You shop is closed by admin</h3>


                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
    </Layout>
</template>
<script lang="ts">
import Layout from "@js/pages/seller/layouts/Layout.vue";
import {defineComponent} from "vue";

export default defineComponent({
    name: 'Shop not active - Seller',
    components: {Layout}
})
</script>
