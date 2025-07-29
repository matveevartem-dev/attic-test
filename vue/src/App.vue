<script setup lang="ts">
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import Search from './components/Search.vue'
</script>

<template>
  <div class="search">
    <Search />
  </div>
</template>

<style scoped>
.form > input[type="text"] {
  width: 25em;
  margin-right: 1.5rem;
}

.form > input[type="button"] {
  width: auto;
}
</style>