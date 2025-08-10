<script setup lang="ts">
  import { reactive, watch, ref } from 'vue'
  import { debounceFilter, watchWithFilter } from '@vueuse/core'
  import axios from 'axios'
  import Result from './Result.vue'

  const result = ref([])

  const form = reactive({
      searchQuery: '',
      submitButton: 'Search',
  })

  const pageSize = import.meta.env.VITE_PAGE_SIZE ?? 1000

  const update = () => {
    if (!searchBtnDisabled()) {
      axios.get(import.meta.env.VITE_API_URL + '/search', {
        params: {
          need: form.searchQuery,
          page: currentPage
        }
      }).
      then(response => {
        result.value = response.data
      }).
      catch(error => {
         result.value = [];
      })
    }
  }

  var currentPage = 1

  const pagePrev = () => {
    if (--currentPage < 1) {
      currentPage = 1
    }
    update()
  }

  const pageNext = () => {
    ++currentPage
    update()
  }

  const searchBtnDisabled = () => {
    return form.searchQuery !== null && form.searchQuery.length < import.meta.env.VITE_MIN_SEARCH_LENGTH;
  }

  const isFirstPage = () => {
    return searchBtnDisabled() || (currentPage === 1)
  }

  const isLastPage = () => {
    return searchBtnDisabled() || (result.value.length < pageSize)
  }

  watchWithFilter(
    form,
    () => { update() },
    {
      eventFilter: debounceFilter(500, { maxWait: 1000 })
    }
  )
</script>

<template>
  <div class="searchForm">
    <span class="searchIcon"><i class="fa fa-search"></i></span>
    <input type="search" id="searchInput" class="searchInput" placeholder="Enter text here" v-model="form.searchQuery"/>
    <input type="submit" id="submitButton" class="searchButton" @click="update()" :disabled="searchBtnDisabled()" v-model="form.submitButton"/>
  </div>
  <div class="resultPagination">
    <button @click="pagePrev()" :disabled="isFirstPage()">Prev</button>
    <button @click="pageNext()" :disabled="isLastPage()">Next</button>
  </div>
  <div class="resultSearch">
    <Result :result="result",  />
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
    left: 1.5rem;
  }

  & .searchInput {
    width: 50rem;
    height: 2rem;
    font-size: 1em;
    color: #63717f;
    -webkit-border-radius: 0.5rem;
    -moz-border-radius: 0.5rem;
    border-radius: 0.5rem;
    padding-left: 2em;
    padding-right: 0.1em;
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

    &::-webkit-input-placeholder       {opacity: 1; transition: opacity 0.3s ease;}
    &::-moz-placeholder                {opacity: 1; transition: opacity 0.3s ease;}
    &:-moz-placeholder                 {opacity: 1; transition: opacity 0.3s ease;}
    &:-ms-input-placeholder            {opacity: 1; transition: opacity 0.3s ease;}
    &:focus::-webkit-input-placeholder {opacity: 0; transition: opacity 0.3s ease;}
    &:focus::-moz-placeholder          {opacity: 0; transition: opacity 0.3s ease;}
    &:focus:-moz-placeholder           {opacity: 0; transition: opacity 0.3s ease;}
    &:focus:-ms-input-placeholder      {opacity: 0; transition: opacity 0.3s ease;}
    &:hover::-webkit-input-placeholder {opacity: 0; transition: opacity 0.3s ease;}
    &:hover::-moz-placeholder          {opacity: 0; transition: opacity 0.3s ease;}
    &:hover:-moz-placeholder           {opacity: 0; transition: opacity 0.3s ease;}
    &:hover:-ms-input-placeholder      {opacity: 0; transition: opacity 0.3s ease;}
  }

  & .searchButton {
    top: 50%;
    height: 1.95rem;
    margin-left: 1rem;
    border-radius: 10px;
    border-color: transparent;
    color: white;
    transition: .2s linear;
    background: #0B63F6;

    &:disabled {
      background-color: rgba(118, 118, 118);
    }

    &:hover {
      box-shadow: 0 0 0 2px white, 0 0 0 4px #3C82F8;
    }

    &:disabled {
      cursor: not-allowed;
    }
  }
}

.resultSearch {
  margin-top: 2rem;
}

.resultPagination {
  padding-top: 0.5rem;
  padding-left: 1rem;

  & button {
    color: #ffffff77;
    padding-left: 1em;
    padding-right: 1em;
    background-color: transparent;

    &:hover:enabled {
      cursor: pointer;
      color: #ffffffdd;
    }
  }
}
</style>
