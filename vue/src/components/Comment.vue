<script setup lang="ts">
  const props = defineProps({
    comment: Object,
  })
</script>

<template>
  <p class="name">{{ comment.name }}</p>
  <p class="body">{{ comment.body }}</p>
  <p class="email">{{ comment.email }}</p>
</template>

<style scoped>
  .comment > p {
    margin-top: 1em;
    margin-left: 1em;
  }
  .comment > .name {
    font-size: 0.9rem;
    padding-top: 0.5em;
    margin-bottom: 0.5em;
    color: #000;
  }
  .comment > .body {
    font-size: 0.7rem;
    padding-top: 0.5em;
    margin-bottom: 0.5em;
    color: #000;
  }
  .comment > .email {
    font-size: 0.8rem;
    padding-top: 0.5em;
    margin-bottom: 0.5em;
    color: #ce00ff;
  }
</style>
