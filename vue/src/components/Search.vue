<script setup lang="ts">
  import { reactive, watch, ref } from 'vue'
  import debounce from 'lodash/debounce'
  import axios from 'axios'
  import Result from './Result.vue'

  const result = ref([])

  const form = reactive({
      searchQuery: '',
      submitButton: 'Search'
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
      }).
      catch(error => {
         result.value = [];
      })
    }
  }, 500)

  watch(form, debounce(() => {
      update();
  }, 500))
</script>

<template>
<div class="box">
  <div class="searchForm">
    <input type="search" id="searchInput" class="searchInput" placeholder="Enter text here" v-model="form.searchQuery"/>
    <span class="searchIcon"><i class="fa fa-search"></i></span>
    <input type="submit" id="submitButton" class="searchButton" @click="update(form.searchQuery)" v-model="form.submitButton"/>
  </div>
</div>
<div class="box">
  <div class="row">
    <Result :result="result" />
  </div>
</div>
</template>

<style scoped>
.searchForm {
  display: flex;
  width: 50rem;
  vertical-align: middle;
  white-space: nowrap;
  background: none;
  align-items: flex-end;

  & .searchIcon {
    position: relative;
    z-index: 1;
    color: #4f5b66;
    font-size: 1rem;
    margin-top: auto;
    margin-bottom: auto;
    right: 1.5rem;
  }

  & .searchInput {
    width: 50rem;
    height: 2rem;
    font-size: 1em;
    float: left;
    color: #63717f;
    -webkit-border-radius: 0.5rem;
    -moz-border-radius: 0.5rem;
    border-radius: 0.5rem;
    padding-left: 2em;
    padding-right: 2em;
    border-style: solid;
    border-width: 1px;
    box-shadow: 2px 2px 2px #4f5b66;;

    &::-webkit-input-placeholder {
      color: #65737e;
    }
    &:-moz-placeholder {
      color: #65737e;
    }
    &::-moz-placeholder {
      color: #65737e;
    }
    &:-ms-input-placeholder {
      color: #65737e;
    }
    &:hover,
    &:focus,
    &:active{
      outline: none;
      background-color: #fff;
    }
  }

  & .searchButton {
    top: 50%;
    height: 1.9rem;
    border-radius: 10px;
    border-color: transparent;
    color: white;
    transition: .2s linear;
    background: #0B63F6;

    &:hover {
      box-shadow: 0 0 0 2px white, 0 0 0 4px #3C82F8;
    }
  }
}

.searchResult {

}

</style>
