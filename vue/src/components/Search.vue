

<script setup lang="ts">
  import { reactive, watch, ref } from 'vue'
  import debounce from 'lodash/debounce'
  import axios from 'axios'
  
  const list = ref([])

  const form = reactive({
      searchQuery: null,
      submitButton: "Search"
  })
  
  const update = debounce((need) => {
    axios.get('/api/search', {
      params: {
        need: need,
      }
    }).
    then(response => {
      list.value = response.data
    })
  }, 500)

  watch(form, debounce(() => {
    if (!(form.searchQuery.length < 3))
      update(form.searchQuery)
  }, 500))
</script>

<template>
    <div class="form">
        <input type="text" id="searchQuery" placeholder="Enter text here" v-model="form.searchQuery">
        <input type="button" id="submitButton" value="Search" @click="update(form.searchQuery)" v-model="form.submitButton">
    </div>
    <div class="row">
       <div v-for="post in list">
          <div v-if="post" class="post">
            <div class="title">{{ post.title }}</div>
            <div v-for="comment in post.comments" class="comment">
              <p class="name">{{comment.name}}</p>
              <p class="body">{{comment.body}}</p>
              <p class="email">{{comment.email}}</p>
            </div>
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

  .comment {
    border-bottom: 1px solid #aaa;
  }
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