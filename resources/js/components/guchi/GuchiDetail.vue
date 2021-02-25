<template>
  
  <div class="thread-page">

    <div class="thread-page-container">

      <!-- 下ボタン -->
      <div class="thread-scroll-btn" @click="scrollToEnd">
        <img :src="'../../image/shita.png'">
      </div>

      <div class="thread-content">

        <!-- スレッドタイトル -->
        <div class="thread-title-area">

          <!-- スレッドのアイコン -->
          <div class="thread-title-area-icon">
            <div v-if="!guchiRoom.icon" class="thread-title-area-icon-image none" :style="{ backgroundImage: 'url(../../image/no-image.png)' }"></div>
            <div v-if="guchiRoom.icon" class="thread-title-area-icon-image" :style="{ backgroundImage: 'url(' + guchiRoom.icon + ')' }"></div>
          </div>

          <div class="thread-title-area-right">

            <!-- スレッドタイトル -->
            <div class="thread-title-area-title">
              <div class="thread-title">{{ guchiRoom.title }}</div>
            </div>

            <div class="thread-title-area-infos">

              <!-- コメント数 -->
              <div class="thread-title-area-comment">
                <div class="comment-count">
                    <img :src="'../../image/comment.png'">
                    {{ guchiRoom.guchis_count }}
                </div>
              </div>

              <div class="thread-title-area-time">
                <div class="time">{{ guchiRoom.created_at }}</div>
              </div>

            </div>

          </div>

        </div>


        <!-- コメント一覧 -->
        <div :key="resetKey">

          <div v-for="(guchi, index) in guchis" :key="guchi.id" class="thread-comment-area"  :class="{ 'self-comment': guchi.isSelf }">

            <!-- IDと投稿日時 -->
            <div v-if="!guchi.user_id" class="thread-comment-area-top">
              <div class="id" :class="{ 'self-id': guchi.isSelf }">{{ index + 1 }}:</div>
              <div class="time">{{ guchi.created_at }}</div>
            </div>

            <div v-if="guchi.user_id" class="thread-comment-area-top">
              <div class="id" :class="{ 'self-id': guchi.isSelf }">
                <img v-if="guchi.user.icon" :src="guchi.user.icon"><img v-if="!guchi.user.icon" :src="'../../image/user.png'"> {{ guchi.user.name }} {{ guchi.created_at }}
              </div>
            </div>

            <!-- コメント本文 -->
            <div class="thread-comment-area-comment">{{ guchi.body }}</div>

            <!-- 画像表示 -->
            <div v-if="guchi.guchi_images.length > 0" class="thread-comment-area-image">
              <div v-for="(image, index) in guchi.guchi_images" :key="index">
                <img :src="image.path" @click="openImageModal(image.path)">
              </div>
            </div>

            <div class="thread-comment-area-bottom">

              <!-- いいね -->
              <div class="thread-comment-area-good">
                <div class="good">
                  {{ guchi.guchi_goods_count }}
                  <img v-if="!guchi.gooded" @click="good(index)" :src="'../../image/good.png'">
                  <img v-if="guchi.gooded" @click="unGood(index)" :src="'../../image/gooded.png'">
                </div>
              </div>

            </div>

            <!-- グチ削除ボタン -->
            <img v-if="guchi.isSelf" :src="'../../image/batsu.png'" @click="deleteGuchiModalOpen(index)" class="guchi-delete-icon">

          </div>

        </div>

        <!-- ページネーション -->
        <div v-if="guchis.length > 0" class="guchi-detail-paginate">
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

        <!-- コメント投稿エリア -->
        <div class="thread-post-area">

          <div class="title">投稿する</div>

          <!-- コメント入力欄 -->
          <div class="input">
            <textarea v-model="form.body"></textarea>
          </div>

          <div v-if="errors.body" class="user-edit-error">
            {{ errors.body[0] }}
          </div>

          <!-- 画像のプレビュー -->
          <div class="thread-image-preview">
            <div v-for="(url, index) in urls" :key="index" class="thread-each-preview">
              <img class="thread-batsu-icon" :src="'../../image/batsu.png'" @click="deletePreview(url, index)">
              <img class="thread-each-image" :src="url">
            </div>
          </div>

          <!-- 画像アップロード -->
          <div class="image-upload">
            <label for="comment-photo">
              <img :src="'../../image/image.png'">
              <input type="file" @change="uploadFile" ref="threadPreview" id="comment-photo" accept="image/*" style="display:none;" multiple>
            </label>
          </div>

          <div v-if="message" class="user-edit-error">
            {{ message }}
          </div>

          <!-- 匿名か名前公開するか選択 -->
          <div class="check-btns">
            <div class="tokumei" :class="{ 'selected': form.anonymous }" @click="anonymous">
              <img :src="'../../image/check.png'">
              匿名で投稿（投稿の削除不可）
            </div>
            <div class="show-name" :class="{ 'selected': !form.anonymous }" @click="showName">
              <img :src="'../../image/check.png'">
              名前とアイコンを表示
            </div>
          </div>

          <!-- テスト用 -->
          <!-- <div class="post">
            <div class="post-btn" @click="check">チェック</div>
          </div> -->

          <!-- 投稿ボタン -->
          <div class="post">
            <div class="post-btn" @click="submit">投稿</div>
          </div>

        </div>

      </div>

    </div>


    <!-- 画像モーダル -->
    <div v-show="modalImageShow" @click.self="closeImageModal" class="overlay-image">

      <div class="overlay-image-box">
        <img :src="modalImage" class="overlay-image-image" :class="{'height-is-bigger':heightIsBigger}">
      </div>

    </div>


    <!-- 削除確認モーダル -->
    <div v-if="deleteGuchiModalOpened" @click.self="deleteGuchiModalClose" class="delete-post-modal-cover">
      <div class="post-delete-check">
        <div class="post-delete-really">削除しますか？</div>
        <div class="post-delete-btns">
          <div class="post-delete-cancel" @click="deleteGuchiModalClose">キャンセル</div>
          <div class="post-delete-delete" @click="deleteGuchi">削除</div>
        </div>
      </div>
    </div>

  </div>

</template>


<script>
export default {
  data() {
    return {
      // 認証ユーザー情報
      authUser: null,
      // 縦長の画像の幅の調整
      tatenagaImageWidth: null,
      heightIsBigger: false,
      // モーダル
      modalImageShow: false,
      modalImage: '',
      // このグチ部屋の情報
      guchiRoom: {
        // id:
        // icon:
        // title:
        // created_at:
        // user_id:
      },
      // グチ投稿で送信するもの
      form: {
        body: '',
        images: [],
        anonymous: 'true',
      },
      message: null,
      errors: [],
      // グチたち
      guchis: [
        // {
          // id:
          // user_id:
          // isSelf:
          // body:
          // anonymous:
          // created_at:
          // guchi_images:
          // guchi_goods_count:
          // gooded:
          // user: {
              // id:
              // name:
              // icon:
          // }
        // }
      ],
      urls: [],
      guchiCreated: false, // グチを投稿したかどうか
      // スクロール位置の記憶
      scrollPosition: null,
      // 削除確認モーダル
      deleteGuchiModalOpened: false,
      // 削除する予定の部屋のid
      deleteGuchiIndex: null,
      // 削除中
      guchiDeleting: false,
      // リロードの用のキー
      resetKey: 0,
      // ページネーション
      currentPage: 1,
      lastPage: 1,
      total: 1,
      from: 0,
      to: 0,
    }
  },

  methods: {
    // チェックテスト
    check() {
      console.log(this.form);
    },
    // ページ初期化
    getInitInfo(roomId) {
      axios.get('/api/guchi/init/' + roomId)
        .then((res) => {
          this.authUser = res.data.authUser;
          this.guchiRoom = res.data.guchiRoom;
          this.getGuchis(1, roomId);
        }).catch(() => {
          return;
        });
    },
    // グチの取得（ページネーション）
    getGuchis(page, roomId) {
      axios.get('/api/guchi/get/' + roomId + '?page=' + page)
        .then((res) => {
          console.log(res.data);
          this.guchis = res.data.data;
          this.currentPage = res.data.current_page;
          this.lastPage = res.data.last_page;
          this.total = res.data.total;
          this.from = res.data.from;
          this.to = res.data.to;
          if (!this.guchiCreated) {
            window.scrollTo(0, 0);
          } else {
            this.$nextTick(function() {
              this.scrollToEnd();
            });
          }
          this.guchiCreated = false;
        }).catch((error) => {
          console.log(error);
        });
    },
    // ページ遷移
    changePage(page) {
      if (page >= 1 && page <= this.lastPage) this.getGuchis(page, this.$route.params.id);
    },
    // グッドの切り替え
    good(i) {
      axios.post('/api/guchi/good/' + this.guchis[i].id)
        .then(() => {
          this.guchis[i].gooded = true;
          this.guchis[i].guchi_goods_count++;
        }).catch(() => {
          return;
        });
    },
    unGood(i) {
      axios.post('/api/guchi/ungood/' + this.guchis[i].id)
        .then(() => {
          this.guchis[i].gooded = false;
          this.guchis[i].guchi_goods_count--;
        }).catch(() => {
          return;
        });
    },
    // 画像アップロードとプレビュー
    uploadFile() {
      const addImages = this.$refs.threadPreview.files;
      for (let i = 0; i < addImages.length; i++) {
        if (!addImages[i].type.match('image.*')) {
          this.message = '画像ファイルを選択してください！';
          return;
        }
      }
      if (this.form.images.length + addImages.length > 3) {
        window.alert('画像は１投稿につき３枚までです！');
      } else {
        this.form.images.push(...addImages);
        for (let i = 0; i < this.form.images.length; i++) {
          this.urls.push(URL.createObjectURL(this.form.images[i]));
        }
        this.$refs.threadPreview.value = '';
        this.message = null;
        console.log(this.urls);
        console.log(this.form.images);
      }
    },
    // 画像プレビューの削除
    deletePreview(url, index) {
      this.urls.splice(index, 1);
      this.form.images.splice(index, 1);
      URL.revokeObjectURL(url);
      console.log(this.urls);
      console.log(this.form.images);
    },
    // 匿名か名前表示か選択
    anonymous() {
      this.form.anonymous = 'true';
    },
    showName() {
      this.form.anonymous = null;
    },
    // グチの送信
    submit() {
      let data = new FormData();
      Object.entries(this.form).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          value.forEach((v, i) => {
            data.append(key + '[]', v)
          })
        } else {
          data.append('body', this.form.body);
          data.append('roomId', this.guchiRoom.id);
          if (this.form.anonymous) {
            data.append('anonymous', this.form.anonymous);
          }
        }
      })
      axios.post('/api/guchi/create', data)
        .then(() => {
          this.form.body = '';
          this.form.images = [];
          this.form.anonymous = true;
          this.errors = [];
          this.urls = [];
          this.message = null;
          this.guchiCreated = true;
          if (this.total % 5 === 0 && this.total > 0) {
            this.getGuchis(this.lastPage + 1, this.$route.params.id);
          } else {
            this.getGuchis(this.lastPage, this.$route.params.id);
          }
        }).catch((error) => {
          this.errors = error.response.data.errors;
        });
    },
    // ページ最下部へスクロール
    scrollToEnd() {
      const element = document.documentElement;
      const bottom = element.scrollHeight - element.clientHeight;
      window.scroll(0, bottom);
    },
    // モーダルウィンドウ開閉時に背景のスクロール位置を維持
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
      const searchPage = document.querySelector('.guchi-main');
      body.classList.remove('bodyWhenOverlay');
      searchPage.classList.remove('guchi-main-when-overlay');
      searchPage.style.top = null;
      window.scroll(0, this.scrollPosition);
      this.scrollPosition = null;
    },
    // 画像のモーダルウィンドウの開閉
    openImageModal(image) {
      this.keepScrollWhenOpen();
      this.modalImageShow = true;
      this.modalImage = image;
      const img = new Image();
      img.src = image;
      const img_width = img.width;
      const img_height = img.height;
      if (img_height >= img_width) {
        this.heightIsBigger = true;
        document.querySelector('.overlay-image-image').addEventListener('load', () => {
          this.tatenagaImageWidth = document.querySelector('.overlay-image-image').clientWidth;
          // console.log(this.tatenagaImageWidth);
        });
      }
    },
    closeImageModal() {
      this.keepScrollWhenClose();
      this.heightIsBigger = false;
      this.tatenagaImageWidth = null;
      this.modalImageShow = false;
      this.modalImage = '';
    },
    // 画像のモーダルで画像が縦長だった場合の対処
    handleResize() {
      if (this.tatenagaImageWidth && this.tatenagaImageWidth > window.innerWidth) {
        this.heightIsBigger = false;
      } else if (this.tatenagaImageWidth && this.tatenagaImageWidth <= window.innerWidth) {
        this.heightIsBigger = true;
      } else {
        return;
      }
    },
    // グチ削除モーダルの開閉
    deleteGuchiModalOpen(i) {
      this.keepScrollWhenOpen();
      this.deleteGuchiIndex = i;
      this.deleteGuchiModalOpened = true;
      console.log(this.guchis[this.deleteGuchiIndex].id);
    },
    deleteGuchiModalClose() {
      this.keepScrollWhenClose();
      this.deleteGuchiIndex = null;
      this.deleteGuchiModalOpened = false;
    },
    // グチの削除
    deleteGuchi() {
      if (this.guchiDeleting) return;
      this.guchiDeleting = true;
      axios.post('/api/guchi/delete/' + this.guchis[this.deleteGuchiIndex].id)
        .then(() => {
          this.guchis.splice(this.deleteGuchiIndex, 1);
          this.deleteGuchiModalClose();
          this.guchiDeleting = false;
        }).catch((error) => {
          console.log(error);
          this.guchiDeleting = false;
        });
    },
  },

  created() {
    window.addEventListener('resize', this.handleResize);
  },

  destroyed() {
    window.removeEventListener('resize', this.handleResize);
  },

  mounted() {
    this.getInitInfo(this.$route.params.id);
  },

  computed: {
    pages() {
      let start = _.max([this.currentPage - 2, 1]);
      let end = _.min([start + 5, this.lastPage + 1]);
      start = _.max([end - 5, 1]);
      return _.range(start, end);
    },
  },

  beforeRouteLeave (to, from, next) {
    if (this.modalImageShow || this.deleteGuchiModalOpened) {
      this.keepScrollWhenClose();
      this.modalImageShow = false;
      this.deleteGuchiModalClose();
    }
    next();
  },

  beforeRouteUpdate (to, from, next) {
    if (this.modalImageShow || this.deleteGuchiModalOpened) {
      this.keepScrollWhenClose();
      this.modalImageShow = false;
      this.deleteGuchiModalClose();
    }
    this.getInitInfo(to.params.id)
    next();
  },
}
</script>