<template>

  <div class="thread-box">
    <!-- スレッド一覧の各々 -->
    <a v-for="(thread, index) in threads" :key="thread.id" @click.self="toGuchiDetail(thread.id)" class="guchi-thread">

      <!-- アイコン -->
      <div class="thread-list-icon" @click="toGuchiDetail(thread.id)">
        <div v-if="!thread.icon" class="thread-left-icon-image none" :style="{ backgroundImage: 'url(../image/no-image.png)' }"></div>
        <div v-if="thread.icon" class="thread-left-icon-image" :style="{ backgroundImage: 'url(' + thread.icon + ')' }"></div>
      </div>

      <div class="thread-left" @click="toGuchiDetail(thread.id)">

        <div class="thread-left-top">
          <div class="genre-icon">{{ thread.genre }}</div>
          <div class="thread-time">{{ thread.created_at }}</div>
          <div class="thread-comment-count">
            <img :src="'../../image/comment.png'">
            {{ thread.comment_count }}
          </div>
        </div>

        <!-- グチ部屋のタイトル -->
        <div class="thread-title" @click="toGuchiDetail(thread.id)">{{ thread.title }}</div>

      </div>

      <!-- ブックマーク -->
      <div @click.self="toGuchiDetail(thread.id)" class="bookmark">
        <img v-if="!thread.bookmarked" :src="'../image/bookmark.png'" @click="bookMark(index)">
        <img v-if="thread.bookmarked" :src="'../image/bookmarked.png'" @click="bookMark(index)">
      </div>

    </a>
  
  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      threads: [
        {
          id: 9,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 5432,
          title: '上司がうざすぎる！',
          bookmarked: false,
        },
        {
          id: 3,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 9876,
          title: '転職したい。。',
          bookmarked: false,
        },
      ]
    }
  },
  methods: {
    toGuchiDetail(threadId) {
      this.$router.push({ name: 'guchi.detail', params: { id: threadId }}).catch(() => {});
    },
    bookMark(i) {
      this.threads[i].bookmarked = !this.threads[i].bookmarked;
    }
  },
  directives: {
    ClickOutside
  },
}
</script>