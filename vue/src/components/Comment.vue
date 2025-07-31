<script setup lang="ts">
  const props = defineProps({
    comment: Object
  })
</script>

<template>
  <div v-if="comment" class="comment">
  <h6 class="name">{{ comment.name }}</h6>
  <p class="body">{{ comment.body }}</p>
  <p class="email">{{ comment.email }}</p>
  </div>
</template>

<style scoped>
.comment {
  margin-top: 0.5em;
  margin-left: 1em;
  padding-left: 1em;
  border-bottom: 1px solid #7e8286;
  width: calc(100% - 3em);

  & p {
    padding: 0;
  }

  & .name {
    font-size: 1.1em;
    margin-bottom: 0.5em;
    color: #bfc6cd;
  }

  & .body {
    font-size: 1em;
    margin-bottom: 0.5em;
    color: #a1a1a1;
    overflow: auto;
    text-overflow: ellipsis;
    white-space: normal;
  }

  & .email {
    font-size: 0.8em;
    color: #919191;
  }
}
</style>
