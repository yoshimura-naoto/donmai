<template>

  <div class="thread-box">

    <!-- スレッド一覧の各々 -->
    <a v-for="(room, index) in guchiRooms" :key="room.id" @click.self="toGuchiDetail(room.id)" class="guchi-thread">

      <!-- アイコン -->
      <div class="thread-list-icon" @click="toGuchiDetail(room.id)">
        <div v-if="!room.icon" class="thread-left-icon-image none" :style="{ backgroundImage: 'url(../../image/no-image.png)' }"></div>
        <div v-if="room.icon" class="thread-left-icon-image" :style="{ backgroundImage: 'url(' + room.icon + ')' }"></div>
      </div>

      <div class="thread-left" @click="toGuchiDetail(room.id)">

        <div class="thread-left-top">
          <div class="genre-icon">{{ room.genre }}</div>
          <div class="thread-time">{{ room.created_at }}</div>
          <div class="thread-comment-count">
            <img :src="'../../image/comment.png'">
            {{ room.guchis_count }}
          </div>
        </div>

        <!-- グチ部屋のタイトル -->
        <div class="thread-title" @click="toGuchiDetail(room.id)">{{ room.title }}</div>

      </div>

      <!-- ブックマーク -->
      <div @click.self="toGuchiDetail(room.id)" class="bookmark">
        <img v-if="!room.bookmarked" :src="'../../image/bookmark.png'" @click="bookMark(index)">
        <img v-if="room.bookmarked" :src="'../../image/bookmarked.png'" @click="unBookMark(index)">
        <img v-if="room.user_id === authUser.id" :src="'../../image/batsu.png'" @click="deleteRoomModalOpen(index)" class="room-delete-icon">
      </div>

    </a>

    <!-- ページネーション -->
    <div v-if="guchiRooms.length > 0" class="guchi-detail-paginate">
      <div class="guchi-detail-paginate-box">
        <div class="guchiroom-paginate">
          <div @click="changePage(1)" class="gechiroom-paginate-first" :class="{'change-page-disabled': currentPage <= 1}">&laquo;</div>
          <div @click="changePage(currentPage - 1)" class="gechiroom-paginate-prev" :class="{'change-page-disabled': currentPage <= 1}">&lt;</div>
          <div v-for="page in pages" :key="page" @click="changePage(page)" class="guchiroom-paginate-page" :class="{'current-page': page === currentPage}">{{ page }}</div>
          <div @click="changePage(currentPage + 1)" class="gechiroom-paginate-next" :class="{'change-page-disabled': currentPage >= lastPage}">&gt;</div>
          <div @click="changePage(lastPage)" class="gechiroom-paginate-last" :class="{'change-page-disabled': currentPage >= lastPage}">&raquo;</div>
        </div>
        <div class="guchiroom-paginate-info">全{{ total }}件中{{ from }}〜{{ to }}件表示</div>
      </div>
    </div>

    <!-- 削除確認モーダル -->
    <div v-if="deleteRoomModalOpened" @click.self="deleteRoomModalClose" class="delete-post-modal-cover">
      <div class="post-delete-check">
        <div class="post-delete-really">部屋を削除しますか？</div>
        <div class="post-delete-btns">
          <div class="post-delete-cancel" @click="deleteRoomModalClose">キャンセル</div>
          <div class="post-delete-delete" @click="deleteRoom">削除</div>
        </div>
      </div>
    </div>
  
  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      // 認証ユーザー情報
      authUser: null,
      // グチ部屋
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
      ],
      // スクロール位置の記憶
      scrollPosition: null,
      // 削除確認モーダル
      deleteRoomModalOpened: false,
      // 削除する予定の部屋のid
      deleteRoomIndex: null,
      // グチ部屋削除中
      roomDeleting: false,
      // ページネーション
      currentPage: 1,
      lastPage: 1,
      total: 1,
      from: 0,
      to: 0,
    }
  },

  methods: {
    // 認証ユーザー情報、グチ部屋の取得
    getInitInfo(genreName) {
      axios.get('/api/user')
        .then((res) => {
          this.authUser = res.data;
          this.getGuchiRooms(1, genreName);
        }).catch((error) => {
          console.log(error);
        });
    },
    // グチ部屋の取得
    getGuchiRooms(page, genreName) {
      axios.get('/api/guchiroom/genre/trend/' + genreName + '?page=' + page)
        .then((res) => {
          console.log(res.data);
          this.guchiRooms = res.data.data;
          this.currentPage = res.data.current_page;
          this.lastPage = res.data.last_page;
          this.total = res.data.total;
          this.from = res.data.from;
          this.to = res.data.to;
        }).catch((error) => {
          console.log(error);
        });
    },
    // ページ遷移
    changePage(page) {
      if (page >= 1 && page <= this.lastPage) this.getGuchiRooms(page, this.$route.params.name);
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
    // ブックマークのキャンセル
    unBookMark(i) {
      axios.post('/api/guchiroom/unbookmark/' + this.guchiRooms[i].id)
        .then(() => {
          this.guchiRooms[i].bookmarked = false;
        }).catch(() => {
          return;
        });
    },
    // モーダル開閉時に背景のスクロール位置の維持
    keepScrollWhenOpen() {
      const body = document.querySelector('body');
      const guchiMain = document.querySelector('.guchi-main');
      this.scrollPosition = window.pageYOffset;
      body.classList.add('bodyWhenOverlay');
      guchiMain.classList.add('guchi-main-when-overlay');
      guchiMain.style.top = -this.scrollPosition + 'px';
    },
    keepScrollWhenClose() {
      const body = document.querySelector('body');
      const guchiMain = document.querySelector('.guchi-main');
      body.classList.remove('bodyWhenOverlay');
      guchiMain.classList.remove('guchi-main-when-overlay');
      guchiMain.style.top = null;
      window.scroll(0, this.scrollPosition);
      this.scrollPosition = null;
    },
    // 削除確認モーダルの開閉
    deleteRoomModalOpen(i) {
      this.keepScrollWhenOpen();
      this.deleteRoomIndex = i;
      this.deleteRoomModalOpened = true;
      console.log(this.guchiRooms[this.deleteRoomIndex].id);
    },
    deleteRoomModalClose() {
      this.keepScrollWhenClose();
      this.deleteRoomIndex = null;
      this.deleteRoomModalOpened = false;
    },
    // 部屋の削除
    deleteRoom() {
      if (this.roomDeleting) return;
      this.roomDeleting = true;
      axios.post('/api/guchiroom/delete/' + this.guchiRooms[this.deleteRoomIndex].id)
        .then(() => {
          if (this.currentPage === this.lastPage && this.guchiRooms.length === 1 && this.currentPage !== 1) {
            this.changePage(this.currentPage - 1);
          } else {
            this.changePage(this.currentPage);
          }
          this.deleteRoomModalClose();
          this.roomDeleting = false;
        }).catch((error) => {
          console.log(error);
          this.roomDeleting = false;
        });
    },
  },

  mounted() {
    this.getInitInfo(this.$route.params.name);
  },

  computed: {
    pages() {
      let start = _.max([this.currentPage - 2, 1]);
      let end = _.min([start + 5, this.lastPage + 1]);
      start = _.max([end - 5, 1]);
      return _.range(start, end);
    },
  },

  beforeRouteLeave (to, from ,next) {
    if (this.deleteRoomModalOpened) {
      this.deleteRoomModalClose();
    }
    next();
  },

  beforeRouteUpdate(to, from, next) {
    if (this.deleteRoomModalOpened) {
      this.deleteRoomModalClose();
    }
    this.getInitInfo(to.params.name);
    next();
  },

  directives: {
    ClickOutside
  },
}
</script>