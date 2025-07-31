<script setup lang="ts">
  import Comment from './Comment.vue'

  const props = defineProps({
    post: Object,
  })
</script>

<template>
  <div v-if="post" class="post">
    <h5 class="title">{{ post.title }}</h5>
    <div v-for="comment in post.comments" class="row">
      <Comment :comment="comment" />
    </div>
  </div>
</template>

<style scoped>
.post {
  background-color: #424242;
  margin-top: 0.5rem;
  padding: 1em;
  border-radius: 10px;

  & .title {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.5em;
    font-weight: bold;
  }
}
</style>
