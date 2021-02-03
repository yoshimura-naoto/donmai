<template>

  <div class="search-page">

    <div class="search-view">

      <div class="search-container">

        <!-- 検索ワード -->
        <div class="search-word-area">

          <img :src="'../../../image/search2.png'">

          <div class="search-word">
            ｢{{ $route.params.word }}｣
          </div>

        </div>

        <!-- 項目ボタン -->
        <div class="search-menu-area">

          <router-link :to="{ name: 'search.post.new', params: { word: $route.params.word }}" class="search-post search-menu" :class="{ 'selected': $route.path.endsWith('/post/new') || $route.path.endsWith('/post/popular') }">
            投稿
          </router-link>

          <router-link :to="{ name: 'search.guchi.new', params: { word: $route.params.word }}" class="search-guchi search-menu" :class="{ 'selected': $route.path.endsWith('/guchi/new') || $route.path.endsWith('/guchi/popular') }">
            グチ
          </router-link>

          <router-link :to="{ name: 'search.user', params: { word: $route.params.word }}" class="search-user search-menu" :class="{ 'selected': $route.path.endsWith('/user') }">
            ユーザー
          </router-link>

        </div>

        <!-- 最新・人気、切り替えボタン -->
        <div class="search-order-area">

          <router-link v-if="$route.path.endsWith('/post/new') || $route.path.endsWith('/post/popular')" :to="{ name: 'search.post.new', params: { word: $route.params.word } }" class="search-order-new" :class="{ 'selected': $route.path.endsWith('/new') }">
            最新
          </router-link>
          <router-link v-if="$route.path.endsWith('/guchi/new') || $route.path.endsWith('/guchi/popular')" :to="{ name: 'search.guchi.new', params: { word: $route.params.word } }" class="search-order-new" :class="{ 'selected': $route.path.endsWith('/new') }">
            最新
          </router-link>

          <router-link v-if="$route.path.endsWith('/post/new') || $route.path.endsWith('/post/popular')" :to="{ name: 'search.post.popular', params: { word: $route.params.word } }" class="search-order-popular" :class="{ 'selected': $route.path.endsWith('/popular') }">
            人気
          </router-link>
          <router-link v-if="$route.path.endsWith('/guchi/new') || $route.path.endsWith('/guchi/popular')" :to="{ name: 'search.guchi.popular', params: { word: $route.params.word } }" class="search-order-popular" :class="{ 'selected': $route.path.endsWith('/popular') }">
            人気
          </router-link>

        </div>

        <router-view></router-view>

      </div>

    </div>

  </div>

</template>


<script>
export default {
  
}
</script>