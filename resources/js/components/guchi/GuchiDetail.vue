<template>
  
  <div class="thread-page">

    <div class="thread-page-container">

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
                    {{ guchisTotal }}
                </div>
              </div>

              <div class="thread-title-area-time">
                <div class="time">{{ guchiRoom.created_at }}</div>
              </div>

            </div>

          </div>

        </div>


        <!-- コメント一覧 -->
        <div :key="resetKey" class="guchis-chat-area" @scroll="guchisPaginate">

          <div v-if="guchisLoading">読み込み中...</div>

          <div v-for="(guchi, index) in guchis" :key="guchi.id" class="thread-comment-area" :class="[{'self-comment': guchi.isSelf}, {'anonymous': !guchi.user_id}]">

            <!-- IDと投稿日時 -->
            <!-- 匿名の場合 -->
            <div v-if="!guchi.user_id" class="thread-comment-area-top">
              <div class="id">匿名:</div>
              <div class="time">{{ guchi.created_at }}</div>
            </div>
            <!-- 名前を公開する場合 -->
            <div v-if="guchi.user_id" class="thread-comment-area-top">
              <div class="id" :class="{ 'self-id': guchi.isSelf }">
                <router-link :to="{ name: 'user', params: { id: guchi.user.id }}">
                  <img v-if="guchi.user.icon" :src="guchi.user.icon">
                  <img v-if="!guchi.user.icon" :src="'../../image/user.png'">
                  {{ guchi.user.name }} 
                </router-link>
                {{ guchi.created_at }}
              </div>
            </div>

            <!-- コメント本文 -->
            <div class="thread-comment-area-comment">{{ guchi.body }}</div>

            <!-- 画像表示 -->
            <!-- <div v-if="guchi.guchi_images.length > 0" class="thread-comment-area-image"> -->
            <div v-if="guchi.guchi_images.length > 0" class="thread-comment-area-image" :class="{'cant-see': guchisLoading}">
              <div v-for="(image, index) in guchi.guchi_images" :key="index">
                <img :src="image.path" @click="openImageModal(image.path)" @load="imgLoaded()">
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
          <div v-if="tooLongGuchiMessage" class="user-edit-error">
            {{ tooLongGuchiMessage }}
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
            <div class="post-btn" @click="submit">送信</div>
          </div>

        </div>

      </div>

    </div>


    <!-- 画像モーダル -->
    <div v-show="modalImageShow" @click.self="closeImageModal" class="overlay-image">

      <div class="overlay-image-box">
        <img :src="'../../image/batsu.png'" @click="closeImageModal" class="image-modal-close">
        <img :src="modalImage" @load="bigImageResize" class="overlay-image-image" :class="{'height-is-bigger':heightIsBigger}">
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
      tooLongGuchiMessage: '',
      postProsessing: false,
      // グチたち
      guchisTotal: 0,
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
      // 無限スクロール用
      guchisLoading: false,
      loadMoreGuchis: true,
      chatScrollHeight: 0,
      // 一番下にスクロールするかどうか
      scrollToBottom: false,
      // マウント中かどうか
      mounting: false,
      // すでに読み込み完了している画像の数
      loadedImgCount: 0,
      // 新たに読み込む画像の数
      newImageCount: 0,
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
          this.scrollToBottom = true;
          this.mounting = true;
          this.getGuchis(roomId);
        }).catch(() => {
          return;
        });
    },
    // グチの取得（無限スクロール）
    getGuchis(roomId) {
      if (!this.loadMoreGuchis) return;
      if (this.guchisLoading) return;
      this.guchisLoading = true;
      axios.get('/api/guchi/get/' + roomId + '?loaded_guchis_count=' + this.guchis.length)
        .then((res) => {
          // console.log(res.data);
          this.guchisTotal = res.data.guchisTotal;
          this.newImageCount = res.data.imagesCount;
          this.guchis.unshift(...res.data.guchis);
          if (this.guchis.length === res.data.guchisTotal) {
            this.loadMoreGuchis = false;
          }
          // console.log(this.guchis.length);
          this.$nextTick(function() {
            const guchiChatArea = document.querySelector('.guchis-chat-area');
            if (this.scrollToBottom && this.newImageCount === 0) {
              guchiChatArea.scrollTop = guchiChatArea.scrollHeight - guchiChatArea.clientHeight;
              this.scrollToBottom = false;
              this.chatScrollHeight = guchiChatArea.scrollHeight;
              this.guchisLoading = false;
              this.mounting = false;
            } else if (!this.scrollToBottom && this.newImageCount === 0) {
              guchiChatArea.scrollTop = guchiChatArea.scrollHeight - this.chatScrollHeight;
              this.chatScrollHeight = guchiChatArea.scrollHeight;
              this.guchisLoading = false;
              this.mounting = false;
            }
          });
        }).catch((error) => {
          console.log(error);
          this.guchisLoading = false;
          this.mounting = false;
        });
    },
    // 最新のグチを１件取得
    getLatestGuchi() {
      const guchiChatArea = document.querySelector('.guchis-chat-area');
      // console.log(guchiChatArea.scrollTop);
      // console.log(guchiChatArea.scrollHeight);
      // console.log(guchiChatArea.clientHeight);
      if (guchiChatArea.scrollTop >= guchiChatArea.scrollHeight - guchiChatArea.clientHeight * 2) {
        this.scrollToBottom = true;
      }
      axios.get('/api/guchi/latest/' + this.$route.params.id)
        .then((res) => {
          // console.log(res.data);
          this.newImageCount = res.data.guchi_images_count;
          this.guchis.push(res.data);
          // スクロール位置が底の方にあるときだけ、最新のグチを取得した際に一番下にスクロールされる
          this.$nextTick(function() {
            if (this.chatScrollHeight < 510) {
              this.scrollToEnd();
            }
            if (this.scrollToBottom && this.newImageCount === 0) {
              guchiChatArea.scrollTop = guchiChatArea.scrollHeight - guchiChatArea.clientHeight;
              this.chatScrollHeight = guchiChatArea.scrollHeight;
              this.scrollToBottom = false;
            }
          });
        }).catch((error) => {
          console.log(error);
        });
    },
    // グチの無限スクロールページネーション
    guchisPaginate() {
      const guchiChatArea = document.querySelector('.guchis-chat-area');
      if (guchiChatArea.scrollTop === 0) {
        this.getGuchis(this.$route.params.id);
      }
    },
    // 画像を全て読み込んでからスクロール位置決定
    imgLoaded() {
      this.loadedImgCount++;
      const guchiChatArea = document.querySelector('.guchis-chat-area');
      if (this.loadedImgCount === this.newImageCount && this.scrollToBottom) {
        if (this.chatScrollHeight < 510 && !this.mounting) {
          this.scrollToEnd();
        }
        guchiChatArea.scrollTop = guchiChatArea.scrollHeight - guchiChatArea.clientHeight;
        this.scrollToBottom = false;
        this.loadedImgCount = 0;
        this.chatScrollHeight = guchiChatArea.scrollHeight;
        this.guchisLoading = false;
        this.mounting = false;
      } else if (this.loadedImgCount === this.newImageCount && !this.scrollToBottom && this.guchisLoading) {
        guchiChatArea.scrollTop = guchiChatArea.scrollHeight - this.chatScrollHeight;
        this.chatScrollHeight = guchiChatArea.scrollHeight;
        this.loadedImgCount = 0;
        this.chatScrollHeight = guchiChatArea.scrollHeight;
        this.guchisLoading = false;
        this.mounting = false;
      }
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
      const files = this.$refs.threadPreview.files;
      if (this.form.images.length + files.length > 3) {  // 枚数制限バリデーション
        window.alert('画像は3枚までです！');
        return;
      }
      let totalFileSize = 0;
      for (let i = 0; i < this.form.images.length; i++) {
        totalFileSize += this.form.images[i].size;
      }
      let loadedImagesCount = 0;
      for (let i = 0; i < files.length; i++) {
        totalFileSize += files[i].size;
        if (!files[i].type.match('image.*') || totalFileSize > 1600000) {  // 合計ファイルサイズ、画像でないファイルのバリデーション
          window.alert('送信するファイルサイズの合計が1.6MBを超えているか、画像でないファイルをアップロードしようとしています！');
          return;
        }
        let image = new Image();
        image.src = URL.createObjectURL(files[i]);
        image.addEventListener('load', () => {
          loadedImagesCount++;
          if (image.naturalWidth > 2500 || image.naturalHeight > 2500) {
            window.alert('画像は縦・横それぞれ2500px以下のものを選択してください！');
            return;
          }
          if (loadedImagesCount === files.length) {
            this.form.images.push(...files);
            for (let i = 0; i < files.length; i++) {
              this.urls.push(URL.createObjectURL(files[i]));
            }
            this.$refs.threadPreview.value = '';
          }
        });
      }
    },
    // 画像プレビューの削除
    deletePreview(url, index) {
      this.urls.splice(index, 1);
      this.form.images.splice(index, 1);
      URL.revokeObjectURL(url);
      // console.log(this.urls);
      // console.log(this.form.images);
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
      if (this.postProsessing) return;
      this.postProsessing = true;
      // 文字数判定
      let textCount = this.form.body.replace(/\n/g, '').length;
      // console.log(textCount);
      if (textCount > 100) {
        this.tooLongGuchiMessage = '100文字以内にしてください！（現在' + textCount + '文字）';
        this.errors = [];
        this.postProsessing = false;
        return;
      } else {
        this.tooLongGuchiMessage = '';
      }
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
          this.postProsessing = false;
        }).catch((error) => {
          this.errors = error.response.data.errors;
          this.postProsessing = false;
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
        this.$nextTick(function() {
            this.tatenagaImageWidth = document.querySelector('.overlay-image-image').clientWidth;
            this.handleResize();
            // console.log(this.tatenagaImageWidth);
        });
      }
    },
    bigImageResize() {
      const overlayImage = document.querySelector('.overlay-image-image');
      if (overlayImage.clientHeight > window.innerHeight) {
        overlayImage.style.maxWidth = window.innerHeight / overlayImage.clientHeight * overlayImage.clientWidth + 'px';
        overlayImage.style.maxHeight = window.innerHeight + 'px';
      }
    },
    closeImageModal() {
      this.keepScrollWhenClose();
      const overlayImage = document.querySelector('.overlay-image-image');
      overlayImage.style.maxWidth = 800 + 'px';
      overlayImage.style.maxHeight = 'none';
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
      // console.log(this.guchis[this.deleteGuchiIndex].id);
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
          this.deleteGuchiModalClose();
          this.guchiDeleting = false;
        }).catch((error) => {
          console.log(error);
          this.guchiDeleting = false;
        });
    },
    // グチをリアルタイムで削除
    deleteGuchiRealTime(guchiId) {
      const guchiIds = this.guchis.map((obj) => {
        return obj.id;
      });
      // console.log(guchiId);
      if (guchiIds.includes(guchiId)) {
        let guchiIndex = guchiIds.indexOf(guchiId);
        this.guchis.splice(guchiIndex, 1);
        const guchiChatArea = document.querySelector('.guchis-chat-area');
        this.chatScrollHeight = guchiChatArea.scrollHeight;
      }
    },
  },

  created() {
    window.addEventListener('resize', this.handleResize);
    window.addEventListener('resize', this.bigImageResize);
  },

  destroyed() {
    window.removeEventListener('resize', this.handleResize);
    window.removeEventListener('resize', this.bigImageResize);
  },

  mounted() {
    // ページ初期化
    this.getInitInfo(this.$route.params.id);
    // 最新のグチを取得
    Echo.private('chat')
      .listen('GuchiCreated', (e) => {
        // console.log(e.guchi.guchi_room_id);
        // console.log(this.$route.params.id);
        if (e.guchi.guchi_room_id == this.$route.params.id) {
          this.guchisTotal++;
          this.getLatestGuchi();
        }
      });
    // グチを削除
    Echo.private('guchiDeleted')
      .listen('GuchiDeleted', (e) => {
        // console.log(e.guchiData.guchi_room_id);
        if (e.guchiData.guchi_room_id == this.$route.params.id) {
          this.guchisTotal--;
          this.deleteGuchiRealTime(e.guchiData.id);
          // console.log(e.guchiData.id);
        }
      });
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
    if (!this.postProsessing && !this.guchiDeleting) {
      next();
    }
  },

  beforeRouteUpdate (to, from, next) {
    if (this.modalImageShow || this.deleteGuchiModalOpened) {
      this.keepScrollWhenClose();
      this.modalImageShow = false;
      this.deleteGuchiModalClose();
    }
    this.getInitInfo(to.params.id)
    if (!this.postProsessing && !this.guchiDeleting) {
      next();
    }
  },
}
</script>