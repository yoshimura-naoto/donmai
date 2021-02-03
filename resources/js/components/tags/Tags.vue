<template>

  <div class="tags-page">

    <div class="tags">

      <!-- タグ名 -->
      <div class="tags-top">

        <div class="tag-name">
          #{{ $route.params.name }}
        </div>

        <div class="tag-posts-count">
          投稿4000万件
        </div>

      </div>

      <!-- 最新ボタンと人気ボタン -->
      <div class="tags-btns-area">

        <router-link :to="{ name: 'tags.new' }" class="tags-new-btn tags-btn" :class="{ 'current': $route.path == $router.resolve({ name: 'tags.new' }).href }">
         最新の投稿
        </router-link>

        <router-link :to="{ name: 'tags.popular' }" class="tags-popular-btn tags-btn" :class="{ 'current': $route.path == $router.resolve({ name: 'tags.popular' }).href }">
          人気の投稿
        </router-link>

      </div>

      <router-view></router-view>

    </div>

  </div>

</template>


<script>
export default {
  
}
</script>