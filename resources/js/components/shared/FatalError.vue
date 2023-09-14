<template>
    <div class="text-center error">
    <i class="fas fa-bomb"></i>
        <h1 class="mt-4">We've encountered a problem</h1>
        <h2>Unfortunately, we can't proceed further</h2>
    </div>
</template>

<script lang="ts">
export default {
    name: "FatalError"
}
</script>

<style scoped>
.error{
    color: #999;
    font-size: 10rem;
}
</style>
