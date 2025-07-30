<script setup lang="ts">
  import Post from './Post.vue'

  const props = defineProps({
    result: Array,
  })
</script>

<template>
    <div class="row">
       <div v-for="post in result">
          <div v-if="post" class="post">
            <Post :post="post" />
          </div>
        </div>
      </div>
</template>

<style scoped>
  .post {
    border-bottom: 1px solid #444;
    margin-top: 1em;
  }
  .post > .title {
    font-size: 1.5rem;
  }
</style>