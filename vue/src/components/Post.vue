<script setup lang="ts">
  import Comment from './Comment.vue'

  const props = defineProps({
    post: Object,
  })
</script>

<template>
  <div v-if="post" class="post">
    <div class="email">{{ post.email }}</div>
    <div class="title">{{ post.title }}</div>
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

  & .email {
    font-size: 0.8rem;
    font-weight: 200;
    color: #f4f4f4;
    margin-bottom: 0;
    text-align: end;
  }

  & .title {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1.5em;
    font-weight: bold;
  }

  & .row:last-child {
    .comment {
      border-bottom: none;
    }
  }
}
</style>
