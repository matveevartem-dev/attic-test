<script setup lang="ts">
  import { reactive, watch, ref } from 'vue'
  import debounce from 'lodash/debounce'
  import axios from 'axios'
  import Result from './Result.vue'

  const result = ref([])

  const form = reactive({
      searchQuery: null,
      submitButton: "Search"
  })
  
  const update = debounce(() => {
    if (
      form.searchQuery !== null && 
      !(form.searchQuery.length < import.meta.env.VITE_MIN_SEARCH_LENGTH)
    ) {
      axios.get(import.meta.env.VITE_API_URL + '/search', {
        params: {
          need: form.searchQuery,
        }
      }).
      then(response => {
        result.value = response.data
      })
    }
  }, 500)

  watch(form, debounce(() => {
      update();
  }, 500))
</script>

<template>
  <div :class="$style.searchForm">
    <input type="text" id="searchQuery" placeholder="Enter text here" :class="$style.searchInput" v-model="form.searchQuery"/>
    <input type="button" id="submitButton" value="Search" class="submitButton" @click="update(form.searchQuery)" v-model="form.submitButton"/>
  </div>
  <div class="row">
    <Result :result="result" />
  </div>
</template>

<style module>
</style>
