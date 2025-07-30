<script setup lang="ts">
  import Comment from './Comment.vue'

  const props = defineProps({
    post: Object,
  })
</script>

<template>
  <div v-if="post" class="post">
    <div class="title">{{ post.title }}</div>
    <div v-for="comment in post.comments" class="comment">
      <Comment :comment="comment" />
    </div>
  </div>
</template>

<style scoped>
  .comment {
    border-bottom: 1px solid #aaa;
  }
</style>
