<template>
    <img :alt="alt" :class="{'rounded-circle': circular, 'rounded': rounded, 'cursor-pointer': canShowFullImage}" :src="getSrc"
         :style="[{'height': getHeight},{'width': getWidth}, {'object-fit':objectFit},]"
         class="d-inline img-fluid" @click="onImageClick"/>

    <VModal v-if="canShowFullImage" v-model="show_preview" class="rounded overflow-hidden">
        <img :alt="alt" :src="getSrc"
             class="img-fluid"/>
    </VModal>
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";
import VModal from "@js/components/VModal.vue";

export default defineComponent({
    components: {VModal},
    inheritAttrs: true,

    props: {
        src: {
            type: [String, null]
        },
        alt: {
            type: String,
            default: 'Image'
        },
        style: {
            type: Object,
            default: {}
        },
        width: [Number, String],
        height: [Number, String],
        size: [Number, String],
        rounded: {
            type: Boolean,
            default: false
        },
        circular: {
            type: Boolean,
            default: false
        },
        showFullImage: {
            type: Boolean,
            default: false
        },
        objectFit: {
            type: String as PropType<'contain' | 'cover' | 'fill' | 'none' | 'revert' | 'inherit' | 'initial'>,
            default: 'cover'
        }

    },
    data() {
        return {
            show_preview: false
        }
    },

    methods: {
        onImageClick(e) {
            if (this.canShowFullImage) {
                e.preventDefault();
                this.show_preview = true;
            }
        },
        getDimensions(dim: string){
            return dim == null ? dim : dim.toString().includes('%') || dim.toString().includes('px') ? dim : dim + "px";
        }
    },
    computed: {
        canShowFullImage(){
         return this.showFullImage && this.src!=null;
        },
        getHeight() {
            return this.getDimensions(this.size) ?? this.getDimensions(this.height);
        },
        getWidth() {
            return this.getDimensions(this.size) ?? this.getDimensions(this.width);
        },
        getSrc() {
            return this.src ?? '/assets/images/placeholder/any.png';
        }
    },

});
</script>

<style scoped>

</style>
