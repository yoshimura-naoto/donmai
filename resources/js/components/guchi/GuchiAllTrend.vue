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
          id: 4,
          genre: 'ゲーム',
          created_at: '2021/1/27 19:07',
          comment_count: 12000,
          title: 'APEX全然勝てないんだが。。APEX全然勝てないんだが。。APEX全然勝てないんだが。。APEX全然勝てないんだが。。',
        },
        {
          id: 5,
          genre: '人間関係',
          created_at: '2021/1/27 19:07',
          comment_count: 11000,
          title: '親友の悪口言い合おうぜ！',
        },
        {
          id: 6,
          genre: '学校',
          created_at: '2021/1/27 19:07',
          comment_count: 10000,
          title: '夏休みの宿題多過ぎない？？',
        },
        {
          id: 7,
          genre: '結婚生活',
          created_at: '2021/1/27 19:07',
          comment_count: 9000,
          title: '嫁が怖すぎる件。。。',
        },
        {
          id: 8,
          genre: '恋愛',
          created_at: '2021/1/27 19:07',
          comment_count: 8000,
          title: '彼女ってどうやったらできるんだろ？',
        },
        {
          id: 9,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 7000,
          title: '上司がうざすぎる！',
        },
        {
          id: 10,
          genre: 'ゲーム',
          created_at: '2021/1/27 19:07',
          comment_count: 6000,
          title: 'APEX全然勝てないんだが。。',
        },
        {
          id: 11,
          genre: '人間関係',
          created_at: '2021/1/27 19:07',
          comment_count: 5000,
          title: '親友の悪口言い合おうぜ！',
        },
        {
          id: 12,
          genre: '学校',
          created_at: '2021/1/27 19:07',
          comment_count: 4000,
          title: '夏休みの宿題多過ぎない？？',
        },
        {
          id: 1,
          genre: '結婚生活',
          created_at: '2021/1/27 19:07',
          comment_count: 3000,
          title: '嫁が怖すぎる件。。。',
        },
        {
          id: 2,
          genre: '恋愛',
          created_at: '2021/1/27 19:07',
          comment_count: 2000,
          title: '彼女ってどうやったらできるんだろ？',
        },
        {
          id: 3,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 1000,
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