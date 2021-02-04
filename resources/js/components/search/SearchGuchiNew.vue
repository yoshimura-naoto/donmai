<template>

  <div class="thread-box">
    <!-- スレッド一覧の各々 -->
    <a v-for="(thread, index) in threads" :key="thread.id" @click.self="toGuchiDetail(thread.id)" class="guchi-thread">

      <!-- アイコン -->
      <div class="thread-list-icon" @click="toGuchiDetail(thread.id)">
        <div v-if="!thread.icon" class="thread-left-icon-image none" :style="{ backgroundImage: 'url(../../../image/no-image.png)' }"></div>
        <div v-if="thread.icon" class="thread-left-icon-image" :style="{ backgroundImage: 'url(' + thread.icon + ')' }"></div>
      </div>

      <div class="thread-left" @click="toGuchiDetail(thread.id)">

        <div class="thread-left-top">
          <div class="genre-icon">{{ thread.genre }}</div>
          <div class="thread-time">{{ thread.created_at }}</div>
          <div class="thread-comment-count">
            <img :src="'../../../image/comment.png'">
            {{ thread.comment_count }}
          </div>
        </div>

        <!-- グチ部屋のタイトル -->
        <div class="thread-title" @click="toGuchiDetail(thread.id)">{{ thread.title }}</div>

      </div>

      <!-- ブックマーク -->
      <div @click.self="toGuchiDetail(thread.id)" class="bookmark">
        <img v-if="!thread.bookmarked" :src="'../../../image/bookmark.png'" @click="bookMark(index)">
        <img v-if="thread.bookmarked" :src="'../../../image/bookmarked.png'" @click="bookMark(index)">
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
          id: 1,
          genre: '結婚生活',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '嫁が怖すぎる件。。。',
          icon: '../../../image/img3.jpg',
          bookmarked: false,
        },
        {
          id: 2,
          genre: '恋愛',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '彼女ってどうやったらできるんだろ？',
          bookmarked: false,
        },
        {
          id: 3,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '上司がうざすぎる！',
          bookmarked: false,
        },
        {
          id: 4,
          genre: 'ゲーム',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: 'APEX全然勝てないんだが。。APEX全然勝てないんだが。。APEX全然勝てないんだが。。APEX全然勝てないんだが。。',
          bookmarked: false,
        },
        {
          id: 5,
          genre: '人間関係',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '親友の悪口言い合おうぜ！',
          icon: '../../../image/img5.jpg',
          bookmarked: false,
        },
        {
          id: 6,
          genre: '学校',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '夏休みの宿題多過ぎない？？',
          bookmarked: false,
        },
        {
          id: 7,
          genre: '結婚生活',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '嫁が怖すぎる件。。。',
          bookmarked: false,
        },
        {
          id: 8,
          genre: '恋愛',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '彼女ってどうやったらできるんだろ？',
          bookmarked: false,
        },
        {
          id: 9,
          genre: '仕事',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '上司がうざすぎる！',
          icon: '../../../image/img7.jpg',
          bookmarked: false,
        },
        {
          id: 10,
          genre: 'ゲーム',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: 'APEX全然勝てないんだが。。',
          bookmarked: false,
        },
        {
          id: 11,
          genre: '人間関係',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '親友の悪口言い合おうぜ！',
          bookmarked: false,
        },
        {
          id: 12,
          genre: '学校',
          created_at: '2021/1/27 19:07',
          comment_count: 4698,
          title: '夏休みの宿題多過ぎない？？',
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