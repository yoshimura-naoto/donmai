<template>

  <div class="thread-box">

    <!-- スレッド一覧の各々 -->
    <a v-for="(room, index) in guchiRooms" :key="room.id" @click.self="toGuchiDetail(room.id)" class="guchi-thread">

      <!-- アイコン -->
      <div class="thread-list-icon" @click="toGuchiDetail(room.id)">
        <div v-if="!room.icon" class="thread-left-icon-image none" :style="{ backgroundImage: 'url(../../../image/no-image.png)' }"></div>
        <div v-if="room.icon" class="thread-left-icon-image" :style="{ backgroundImage: 'url(' + room.icon + ')' }"></div>
      </div>

      <div class="thread-left" @click="toGuchiDetail(room.id)">

        <div class="thread-left-top">
          <div class="genre-icon">{{ room.genre }}</div>
          <div class="thread-time">{{ room.created_at }}</div>
          <div class="thread-comment-count">
            <img :src="'../../../image/comment.png'">
            {{ room.guchis_count }}
          </div>
        </div>

        <!-- グチ部屋のタイトル -->
        <div class="thread-title" @click="toGuchiDetail(room.id)">{{ room.title }}</div>

      </div>

      <!-- ブックマーク -->
      <div @click.self="toGuchiDetail(room.id)" class="bookmark">
        <img v-if="!room.bookmarked" :src="'../../../image/bookmark.png'" @click="bookMark(index)">
        <img v-if="room.bookmarked" :src="'../../../image/bookmarked.png'" @click="unBookMark(index)">
      </div>

    </a>
  
  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      guchiRooms: [
        // {
          // id
          // icon
          // title
          // genre
          // created_at
          // user_id
          // guchis_count
          // bookmarked
        // }
      ]
    }
  },

  methods: {
    // 初期化
    getInitInfo() {
      axios.get('/api/search/guchiroom/popular/' + this.$route.params.word)
        .then((res) => {
          console.log(res.data);
          this.guchiRooms = res.data;
        }).then(() => {
          return;
        });
    },
    // グチの詳細ページへ遷移
    toGuchiDetail(roomId) {
      this.$router.push({ name: 'guchi.detail', params: { id: roomId }}).catch(() => {});
    },
    // ブックマーク
    bookMark(i) {
      axios.post('/api/guchiroom/bookmark/' + this.guchiRooms[i].id)
        .then(() => {
          this.guchiRooms[i].bookmarked = true;
        }).catch(() => {
          return;
        });
    },
    unBookMark(i) {
      axios.post('/api/guchiroom/unbookmark/' + this.guchiRooms[i].id)
        .then(() => {
          this.guchiRooms[i].bookmarked = false;
        }).catch(() => {
          return;
        });
    },
  },

  mounted() {
    this.getInitInfo();
  },

  directives: {
    ClickOutside
  },
}
</script>