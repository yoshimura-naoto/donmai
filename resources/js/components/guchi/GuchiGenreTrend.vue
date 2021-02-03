<template>

  <div class="thread-box">
    <!-- スレッド一覧の各々 -->
    <router-link :to="{ name: 'guchi.detail' }" v-for="thread in threads" :key="thread.id" class="guchi-thread">

      <div class="thread-list-icon">
        <div v-if="!thread.icon" class="thread-left-icon-image none" :style="{ backgroundImage: 'url(../../image/no-image.png)' }"></div>
        <div v-if="thread.icon" class="thread-left-icon-image" :style="{ backgroundImage: 'url(' + thread.icon + ')' }"></div>
      </div>

      <div class="thread-left">

        <div class="thread-left-top">
          <div class="genre-icon">{{ thread.genre }}</div>
          <div class="thread-time">{{ thread.created_at }}</div>
          <div class="thread-comment-count">
            <img :src="'../../image/comment.png'">
            {{ thread.comment_count }}
          </div>
        </div>

        <div class="thread-title">{{ thread.title }}</div>

      </div>

      <div class="bookmark">
        <img :src="'../../image/bookmark.png'">
      </div>

    </router-link>
  
  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      threads: [
        {
          id: 3,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 9876,
          title: '転職したい。。',
        },
        {
          id: 9,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 5432,
          title: '上司がうざすぎる！',
        },
      ]
    }
  },
  methods: {
    
  },
  directives: {
    ClickOutside
  },
}
</script>