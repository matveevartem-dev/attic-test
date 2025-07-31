<script setup lang="ts">
  import Post from './Post.vue'

  const props = defineProps({
    result: Array,
    post: Object
  })
</script>

<template>
  <div v-for="post in result" class="row">
    <div v-if="post">
      <Post :post="post" />
    </div>
  </div>
</template>
