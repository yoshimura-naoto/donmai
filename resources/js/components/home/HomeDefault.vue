<template>
  <div class="main-home">
    
    <!-- 新規投稿 -->
    <div class="new-post">

      <!-- アイコン画像 -->
      <div class="logo-area" v-if="authUser">
        <img :src="authUser.icon">
      </div>

      <!-- 入力エリア -->
      <div class="input-area">

        <!-- テキスト入力エリア -->
        <div class="input-1">
          <!-- <textarea v-model="newPost.body" ref="area" :style="styles" class="flex-textarea" placeholder="なんかあった？"></textarea> -->
          <textarea v-model="newPost.body" ref="area" :style="styles" class="flex-textarea" placeholder="なんかあった？"></textarea>
        </div>

        <div v-if="errors.body" class="user-edit-error">
          {{ errors.body[0] }}
        </div>

        <!-- ジャンル選択エリア -->
        <div class="select-genre">
          <select name="genre" v-model="newPost.genreIndex">
            <option value='' selected>ジャンルを選択してください</option>
            <option v-for="(genre, index) in genres" :key="index" :value="index">{{ genre.name }}</option>
          </select>

          <div v-if="errors.genreIndex" class="user-edit-error">
            {{ errors.genreIndex[0] }}
          </div>
        </div>

        <!-- タグ入力エリア -->
        <div class="input-tag">
          <input type="text" placeholder="タグ（例: #ああ #いい）" v-model="newPost.tags">
        </div>

        <div v-if="errors.tags" class="user-edit-error">
          {{ errors.tags[0] }}
        </div>

        <!-- 画像のプレビューエリア -->
        <div v-if="newPost.images.length > 0" class="image-preview" >
          <div v-for="(url, index) in urls" :key="index" class="each-preview" :class="{ 'one-image-pre': newPost.images.length == 1, 'two-image-pre': newPost.images.length == 2, 'three-image-pre': newPost.images.length == 3, 'four-image-pre': newPost.images.length == 4, 'yokonaga': newPost.images.length == 3 && index == 0 }">
            <div class="delete-image">
              <img class="batsu-icon" :src="'../image/batsu.png'" @click="deletePreview(url, index)">
            </div>
            <div class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + url + ')' }" :class="{ 'one-each-image': newPost.images.length == 1, 'two-each-image': newPost.images.length == 2, 'three-each-image': newPost.images.length == 3, 'four-each-image': newPost.images.length == 4, 'yokonaga-image': newPost.images.length == 3 && index == 0 }"></div>
            </div>
          </div>
        </div>

        <!-- 画像追加ボタンと投稿ボタン -->
        <div class="input-2">

          <div class="image-add">
            <label>
              <img class="image-icon" :src="'../image/image.png'" alt="画像追加">
              <input class="file-upload" type="file" ref="preview" @change="uploadFile" accept="image/*" multiple>
            </label>
          </div>

          <div class="submit">
            <div class="submit-btn" @click="submit">投稿</div>
          </div>

          <!-- テスト用 -->
          <!-- <div class="submit">
            <div class="submit-btn" @click="check">チェック</div>
          </div> -->
          
        </div>

      </div>
    </div>

  

    <!-- 投稿一覧 -->
    <div v-for="(post, index) in posts" :key="post.id" class="posts">

      <!-- アイコン画像 -->
      <div class="logo-area">
        <router-link :to="{ name: 'user', params: { id: post.user.id }}">
          <img :src="post.user.icon">
        </router-link>
      </div>

      <!-- 内容表示エリア -->
      <div class="input-area">

        <!-- ユーザー名 -->
        <div class="user-name-post">{{ post.user.name }}</div>

        <!-- テキスト -->
        <div class="post-body">{{ post.body }}</div>

        <!-- タグ -->
        <div v-if="post.tags" class="post-tags">
          <router-link :to="{ name: 'tags.new', params: { name: tag.name }}" v-for="(tag, index) in post.tags" :key="index">
            #{{ tag.name }}
          </router-link>
        </div>

        <!-- 画像たち -->
        <div v-if="post.post_images && post.post_images.length > 0" class="post-image-view">
          <div v-for="(image, index) in post.post_images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.post_images.length == 1, 'two-image-pre': post.post_images.length == 2, 'three-image-pre': post.post_images.length == 3, 'four-image-pre': post.post_images.length == 4, 'yokonaga': post.post_images.length == 3 && index == 0 }">
            <div @click="openImageModal(image.path)" class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + image.path + ')' }" :class="{ 'one-each-image': post.post_images.length == 1, 'two-each-image': post.post_images.length == 2, 'three-each-image': post.post_images.length == 3, 'four-each-image': post.post_images.length == 4, 'yokonaga-image': post.post_images.length == 3 && index == 0 }"></div>
            </div>
          </div>
        </div>

        <!-- どんまいボタンとコメントボタンエリア -->
        <div class="post-btns">
          <!-- どんまいボタン -->
          <div class="post-donmai post-action-icon" @click="donmai(index)">
            <img v-if="!post.donmai" :src="'../image/donmai.png'" width="30px">
            <img v-if="post.donmai" :src="'../image/donmaied.png'" width="30px">
            {{ post.donmaiCount }}
          </div>
          <!-- コメントボタン -->
          <div @click="openModalPost(index)" class="post-comment-icon post-action-icon">
            <img :src="'../image/comment.png'">
            {{ post.commentCount }}
          </div>
        </div>

      </div>
    </div>



    <!-- 投稿モーダル -->
    <div v-if="modalPostShow" @click.self="closeModalPost" class="overlay-post">

      <div class="overlay-post-content">

        <!-- 投稿 -->
        <div class="posts-overlay">

          <!-- アイコン画像 -->
          <div class="logo-area">
            <router-link :to="{ name: 'user', params: { id: modalPostUserId }}">
              <img :src="modalPostUserIcon">
            </router-link>
          </div>

          <!-- 内容表示エリア -->
          <div class="input-area">

            <!-- ユーザー名 -->
            <div class="user-name-post">{{ modalPostUserName }}</div>

            <!-- テキスト -->
            <div class="post-body">{{ modalPostBody }}</div>

            <!-- タグ -->
            <div v-if="modalPostTags" class="post-tags">
              <router-link :to="{ name: 'tags.new', params: { name: tag.name }}" v-for="(tag, index) in modalPostTags" :key="index">
                #{{ tag.name }}
              </router-link>
            </div>

            <!-- 画像たち -->
            <div v-if="modalPostImages.length > 0" class="post-image-view">
              <div v-for="(image, index) in modalPostImages" :key="index" class="each-preview" :class="{ 'one-image-pre': modalPostImages.length == 1, 'two-image-pre': modalPostImages.length == 2, 'three-image-pre': modalPostImages.length == 3, 'four-image-pre': modalPostImages.length == 4, 'yokonaga': modalPostImages.length == 3 && index == 0 }">
                <div @click="openImageModal(image.path)" class="each-image-box">
                  <div class="each-image" :style="{ backgroundImage: 'url(' + image.path + ')' }" :class="{ 'one-each-image': modalPostImages.length == 1, 'two-each-image': modalPostImages.length == 2, 'three-each-image': modalPostImages.length == 3, 'four-each-image': modalPostImages.length == 4, 'yokonaga-image': modalPostImages.length == 3 && index == 0 }"></div>
                </div>
              </div>
            </div>

            <!-- どんまいボタンとコメントボタンエリア -->
            <div class="post-btns-overlay">
              <!-- どんまい数 -->
              <div @click="openModalDonmai" class="post-donmai-overlay post-action-icon">
                <b>{{ modalPostDonmaiCount }}</b>件のどんまい
              </div>
              <!-- コメント数 -->
              <div class="post-comment-icon-overlay post-action-icon-comment-overlay">
                <b>{{ modalPostCommentCount }}</b>件のコメント
              </div>
            </div>


            <!-- コメント入力 -->
            <div class="comment-post-area">

              <div class="comment-post-icon">
                <img :src="authUser.icon">
              </div>
            
              <div class="input-3">

                <textarea v-model="commentInput" ref="commentarea" :style="commentStyles" class="flex-textarea-2" placeholder="コメントを入力"></textarea>

                <div v-if="commentErrors.body" class="user-edit-error">
                  {{ commentErrors.body[0] }}
                </div>

                <div class="comment-btn-main" @click="commentPost">
                  コメント
                </div>

              </div>
              
            </div>


            <!-- それぞれのコメントたち -->
            <div v-for="(comment, index) in modalPostComments" :key="comment.id" class="overlay-post-comment-area">

              <!-- アイコン -->
              <div class="overlay-post-comment-left">

                <router-link :to="{ name: 'user', params: { id: comment.user.id }}">
                  <img :src="comment.user.icon">
                </router-link>

              </div>

              <div class="overlay-post-comment-right">

                <div class="overlay-post-comment-top">

                  <!-- 名前 -->
                  <div class="overlay-post-name">
                    {{ comment.user.name }}
                  </div>

                  <!-- 日付 -->
                  <div class="overlay-post-day">
                    {{ comment.created_at }}
                  </div>

                </div>

                <!-- コメント内容 -->
                <div class="overlay-post-comment">{{ comment.body }}</div>

                <div class="overlay-post-bottom">

                  <!-- 返信 -->
                  <div class="overlay-post-comment-count">

                    <div v-if="comment.replyCount && !comment.replyShow" @click="openCommentReply(index)" class="post-comment-count">
                      ▼ {{ comment.replyCount }}件の返信を表示
                    </div>
                    <div v-if="comment.replyCount && comment.replyShow" @click="closeCommentReply(index)" class="post-comment-count">
                      ▲ {{ comment.replyCount }}件の返信を非表示
                    </div>

                    <div @click="openReplyForm(index)" class="comment-replay-btn">返信する</div>

                  </div>

                  <!-- コメントへのいいね -->
                  <div class="overlay-post-good">
                    {{ comment.goodCount }}
                    <img v-if="!comment.gooded" :src="'../image/good.png'" @click="commentGood(index)">
                    <img v-if="comment.gooded" :src="'../image/gooded.png'" @click="commentGood(index)">
                  </div>

                </div>


                <!-- コメントへの返信入力フォーム -->
                <div v-if="comment.replyFormOpened" class="input-3">

                  <!-- <textarea v-model="comment.replyInput" ref="replyarea" @keydown="changeReplyHeight(index)" :style="replyStyles(index)" class="flex-textarea-2" placeholder="コメントを入力"></textarea> -->
                  <textarea v-model="comment.replyInput" ref="replyarea" class="flex-textarea-2" placeholder="返信を入力"></textarea>

                  <div v-if="comment.replyErrors.body" class="user-edit-error">
                    {{ comment.replyErrors.body[0] }}
                  </div>
                  <!-- <div v-if="modalPostComments[0].replyErrors" class="user-edit-error">
                    {{ modalPostComments[0].replyErrors.body[0] }}
                  </div> -->

                  <div class="comment-reply-btns">

                    <div @click="closeReplyForm(index)" class="reply-cancel-btn">
                      キャンセル
                    </div>

                    <div class="comment-btn" @click="reply(index)">
                      コメント
                    </div>

                  </div>

                </div>


                <!-- コメントへの返信たち -->
                <div v-if="comment.replyShow">

                  <div v-for="reply in comment.replies" :key="reply.id" class="comment-reply-area">

                    <div class="overlay-post-comment-reply-area">

                      <!-- アイコン -->
                      <div class="overlay-post-comment-left">
                        <router-link :to="{ name: 'user', params: { id: reply.user.id }}">
                          <img :src="reply.user.icon">
                        </router-link>
                      </div>

                      <div class="overlay-post-comment-right">

                        <div class="overlay-post-comment-top">

                          <!-- 名前 -->
                          <div class="overlay-post-name">
                            {{ reply.user.name }}
                          </div>

                          <!-- 日付 -->
                          <div class="overlay-post-day">
                            {{ reply.created_at }}
                          </div>

                        </div>

                        <!-- コメント内容 -->
                        <div class="overlay-post-comment">{{ reply.body }}</div>

                        <div class="overlay-post-bottom">

                          <!-- コメントへの返信へのいいね -->
                          <div class="overlay-post-good">
                            {{ reply.goodCount }}
                            <img v-if="!reply.gooded" :src="'../image/good.png'" @click="replyGood(comment.id, reply.id)">
                            <img v-if="reply.gooded" :src="'../image/gooded.png'" @click="replyGood(comment.id, reply.id)">
                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

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


    <!-- 「どんまい」モーダル -->
    <div v-if="modalDonmaiShow" @click.self="closeModalDonmai" class="overlay-post">

      <div class="overlay-donmai-content">

        <div class="overlay-donmai-title">
          どんまい！
        </div>

        <div class="donmai-user-box">

          <div v-for="(user, index) in modalDonmaiUsers" :key="user.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  <img :src="user.icon">
                </router-link>
              </div>

              <!-- ユーザー名 -->
              <div class="overlay-donmai-user-name">
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  {{ user.name }}
                </router-link>
              </div>

            </div>

            <div class="overlay-donmai-right">

              <!-- フォローボタン -->
              <div v-if="!user.followed && user.id !== authUser.id" @click="donmaiFollow(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="user.followed && user.id !== authUser.id" @click="donmaiUnFollow(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

        </div>

        <div class="no-follower" v-if="modalDonmaiUsers.length == 0">
          どんまいしてるユーザーはいません。
        </div>

      </div>

    </div>

  </div>
</template>


<script>
export default {
  inject: ['reload'],

  data: function () {
    return {
      // 認証ユーザー情報
      authUser: null,
      // コンポーネントのリロード用
      // isRouterShow: true,
      // ウィンドウ幅
      windowWidth: window.innerWidth,
      // 縦長画像の調整
      tatenagaImageWidth: null,
      heightIsBigger: false,
      // スクロール位置
      scrollPosition: null,
      imageCount: 0,
      // 入力フォームの高さ調整用
      height: '20px',
      commentInput: '',
      commentHeight: '31px',
      // ジャンル
      genres: [],
      // 新規投稿
      newPost: {
        body: '',
        genreIndex: '',
        tags: '',
        images: [],
      },
      errors: [],
      urls: [],
      // 投稿
      posts: [
        // {
        //   id: 1,
        //   user: {
        //     id: 5,
        //     name: '太郎',
        //     icon: 'https:/...'
        //   },
        //   body: 'こんにちは。',
        //   genre: {
        //     route: 'job',
        //     name: '仕事',
        //   },
        //   tags: [
        //     {
        //       id: 1,
        //       name: '鬼滅'
        //     }
        //   ],
        //   post_images: [
        //     {
        //       id: 4,
        //       path: 'https:/...'
        //     }
        //   ],
        //   donmai: false,
        //   donmaiCount: 5,
        //   commentCount: 9,
        // }
      ],
      // モーダル
      modalPostShow: false,
      modalPostIndex: null,
      modalPostId: null,
      modalPostUserId: null,
      modalPostUserName: null,
      modalPostUserIcon: null,
      modalPostBody: null,
      modalPostTags: [],
      modalPostImages: [],
      modalPostDonmai: false,
      modalPostDonmaiCount: null,
      modalPostCommentCount: null,
      modalPostComments: [
        // {
        //   id: 1,
        //   userId: 2,
        //   user: '鈴木誠也',
        //   icon: '../image/img1.jpg',
        //   day: '5日前',
        //   goodCount: 37,
        //   good: false,
        //   replyCount: 4,
        //   replyShow: false,
        //   replyFormOpened: false,
        //   replyInput: '',
        //   replyErrors: [],
        //   replyHeight: '31px',
        //   replies: [
        //     {
        //       id: 2,
        //       userId: 3,
        //       user: '柳田',
        //       icon: '../image/img5.jpg',
        //       day: '4日前',
        //       goodCount: 37,
        //       good: false,
        //     },
        //   ],
        // },
      ],
      newComment: {},
      newReply: {},
      commentErrors: [],
      // 画像モーダル
      modalImageShow: false,
      modalImage: '',
      // どんまいモーダル
      modalDonmaiShow: false,
      modalDonmaiUsers: [
        // {
          // id: 11,
          // icon: '../image/img1.jpg',
          // name: 'ジェイコブ・デグロム',
          // followed: false,
        // },
      ],
    };
  },

  methods: {
    // テスト
    check() {
      console.log(this.newPost);
    },
    // 認証ユーザー情報、全ジャンル、投稿を取得
    getInitInfo() {
      axios.get('/api/home/init')
        .then((res) => {
          this.genres = res.data.genres;
          this.authUser = res.data.authUser;
          this.posts = res.data.posts;
          if (!this.authUser.icon) {
            this.authUser.icon = '../image/user.png';
          }
          for (let i = 0; i < this.posts.length; i++) {
            if (!this.posts[i].user.icon) {
              this.posts[i].icon = '../image/user.png';
            }
          }
          console.log(this.posts);
        }).catch(() => {
          return;
        });
    },
    // 投稿のテキストエリアの高さをフレキシブルに
    changeHeight() {
      this.height = this.$refs.area.scrollHeight + 'px';
      this.height = 25 + 'px';
      this.$nextTick(() => {
        this.height = this.$refs.area.scrollHeight + 'px';
      });
    },
    // 投稿
    submit() {
      let data = new FormData();
      Object.entries(this.newPost).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          value.forEach((v, i) => {
            data.append(key + '[]', v);
          })
        } else {
          data.append(key, value);
        }
      })
      axios.post('/api/post/add', data)
        .then(() => {
          this.reload();
        }).catch((error) => {
          this.errors = error.response.data.errors;
          // console.log(this.errors);
        });
    },
    // コメント入力欄の高さをフレキシブルに
    changeCommentHeight() {
      this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
      this.commentHeight = 18 + 'px';
      this.$nextTick(() => {
        this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
      });
    },
    // コメントへの返信フォームの高さをフレキシブルに変更するメソッド
    // changeReplyHeight(i) {
    //   this.modalPostComments[i].replyHeight = this.$refs.replyarea[i].scrollHeight + 'px';
    //   this.modalPostComments[i].replyHeight = 18 + 'px';
    //   this.$nextTick(() => {
    //     this.modalPostComments[i].replyHeight = this.$refs.replyarea[i].scrollHeight + 'px';
    //   });
    // },
    // 画像アップロードでプレビュー
    uploadFile() {
      const files = this.$refs.preview.files;
      for (let i = 0; i < files.length; i++) {
        if (!files[i].type.match('image.*')) {
          files.splice(i, 1);
        }
      }
      if (this.newPost.images.length + files.length > 4) {
        window.alert('画像は４枚までです！');
      } else {
        this.newPost.images.push(...files);
        for (let i = 0; i < files.length; i++) {
          this.urls.push(URL.createObjectURL(files[i]));
        }
        this.$refs.preview.value = '';
        console.log(this.newPost.images);
        console.log(this.urls);
      }
      // this.imageCount = this.files.length;
      // console.log(this.files);
      // console.log(this.urls);
      // console.log(this.imageCount);
    },
    // 画像プレビューの削除
    deletePreview(url, index) {
      this.urls.splice(index, 1);
      this.newPost.images.splice(index, 1);
      URL.revokeObjectURL(url);
      console.log(this.urls);
      console.log(this.newPost.images);
      // this.imageCount = this.files.length;
      // console.log(this.files);
      // console.log(this.imageCount);
    },
    // どんまい機能の処理
    donmai(i) {
      const post = this.posts[i];
      post.donmai = !post.donmai;
      if (post.donmai == false) {
        axios.post('/api/undonmai/' + this.posts[i].id)
          .then(() => {
            post.donmaiCount--;
          }).catch(() => {
            return;
          });
      } else {
        axios.post('/api/donmai/' + this.posts[i].id)
          .then(() => {
            post.donmaiCount++;
          }).catch(() => {
            return;
          });
      }
    },
    // モーダルのどんまい機能
    modalDonmai(i) {
      const post = this.posts.find((v) => v.id == i);
      post.donmai = !post.donmai;
      this.modalPostDonmai = !this.modalPostDonmai;
      if (!this.modalPostDonmai) {
        post.donmai_count--;
        this.modalPostDonmaiCount--;
      } else {
        post.donmai_count++;
        this.modalPostDonmaiCount++;
      }
    },
    // モーダルウィンドウ開閉時に背景のスクロール位置を維持
    keepScrollWhenOpen() {
      const body = document.querySelector('body');
      const mainHome = document.querySelector('.main-home');
      this.scrollPosition = window.pageYOffset;
      body.classList.add('bodyWhenOverlay');
      mainHome.classList.add('main-home-when-overlay');
      mainHome.style.top = -this.scrollPosition + 'px';
    },
    keepScrollWhenClose() {
      const body = document.querySelector('body');
      const mainHome = document.querySelector('.main-home');
      body.classList.remove('bodyWhenOverlay');
      mainHome.classList.remove('main-home-when-overlay');
      mainHome.style.top = null;
      window.scroll(0, this.scrollPosition);
      this.scrollPosition = null;
    },
    // 投稿のモーダルウィンドウの開閉
    openModalPost(i) {
      axios.get('/api/comments/get/' + this.posts[i].id)
        .then((res) => {
          this.modalPostIndex = i;
          this.modalPostComments = res.data;
          this.keepScrollWhenOpen();
          this.modalPostId = this.posts[i].id;
          this.modalPostUserId = this.posts[i].user.id;
          this.modalPostUserName = this.posts[i].user.name;
          this.modalPostUserIcon = this.posts[i].user.icon;
          this.modalPostBody = this.posts[i].body;
          this.modalPostTags = this.posts[i].tags;
          this.modalPostImages = this.posts[i].post_images;
          this.modalPostDonmai = this.posts[i].donmai;
          this.modalPostDonmaiCount = this.posts[i].donmaiCount;
          this.modalPostCommentCount = this.posts[i].commentCount;
          this.modalPostShow = true;
          // console.log(this.modalPostComments);
          // console.log(this.modalPostUserId);
          // console.log(this.modalPostUserIcon);
        }).catch(() => {
          return;
        });
    },
    closeModalPost() {
      this.keepScrollWhenClose();
      this.modalPostShow = false;
      this.modalPostIndex = null;
      this.modalPostId = null;
      this.modalPostUserId = null;
      this.modalPostUserName = null;
      this.modalPostUserIcon = null;
      this.modalPostBody = null;
      this.modalPostTags = [];
      this.modalPostImages = [];
      this.modalPostDonmai = false;
      this.modalPostDonmaiCount = null;
      this.modalPostCommentCount = null;
      this.modalPostComments.map(function(element) {
        element.replyShow = false;
      });
    },
    // どんまいしたユーザーのモーダルウィンドウの開閉
    openModalDonmai() {
      axios.get('/api/donmai/users/' + this.modalPostId)
        .then((res) => {
          this.modalDonmaiUsers = res.data;
          // console.log(this.modalDonmaiUsers);
          this.modalDonmaiShow = true;
        }).catch(() => {
          return;
        });
    },
    closeModalDonmai() {
      this.modalDonmaiShow = false;
    },
    // 画像のモーダルウィンドウの開閉
    openImageModal(image) {
      if (!this.modalPostShow) {
        this.keepScrollWhenOpen();
      }
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
      if (!this.modalPostShow) {
        this.keepScrollWhenClose();
      }
      this.heightIsBigger = false;
      this.tatenagaImageWidth = null;
      this.modalImageShow = false;
      this.modalImage = '';
    },
    // どんまいしたユーザーのフォローとアンフォロー
    donmaiFollow(i) {
      axios.post('/api/follow', this.modalDonmaiUsers[i])
        .then(() => {
          this.modalDonmaiUsers[i].followed = true;
        }).catch(() => {
          return;
        });
    },
    donmaiUnFollow(i) {
      axios.post('/api/unfollow', this.modalDonmaiUsers[i])
        .then(() => {
          this.modalDonmaiUsers[i].followed = false;
        }).catch(() => {
          return;
        });
    },
    // コメント投稿
    commentPost() {
      let data = new FormData();
      data.append('postId', this.modalPostId);
      data.append('body', this.commentInput);
      axios.post('/api/comment', data)
        .then((res) => {
          console.log(res.data);
          this.modalPostComments.unshift(res.data);
          this.commentInput = '';
          this.posts[this.modalPostIndex].commentCount++;
          this.modalPostCommentCount++;
          this.commentErrors = [];
        }).catch((error) => {
          console.log(error.response.data.errors);
          this.commentErrors = error.response.data.errors;
        });
    },
    // commentPost() {
    //   let data = new FormData();
    //   data.append('postId', this.modalPostId);
    //   data.append('body', this.commentInput);
    //   axios.post('/api/comment', data)
    //     .then((res) => {
    //       console.log(res.data.commentId);
    //       this.newComment.id = res.data.commentId;
    //       this.newComment.created_at = 'たった今';
    //       this.newComment.body = this.commentInput;
    //       this.newComment.user = this.authUser;
    //       this.newComment.goodCount = 0;
    //       this.newComment.gooded = false;
    //       this.newComment.replyCount = 0;
    //       this.newComment.replyFormOpened = false;
    //       this.newComment.replyInput = '';
    //       this.newComment.replies = [];
    //       console.log(this.newComment);
    //       this.modalPostComments.unshift(this.newComment);
    //       console.log(this.modalPostComments);
    //       this.newComment = {};
    //       this.commentInput = '';
    //       this.posts[this.modalPostIndex].commentCount++;
    //       this.modalPostCommentCount++;
    //     }).catch((error) => {
    //       console.log(error.response.data.errors);
    //       this.commentErrors = error.response.data.errors;
    //     });
    // },
    //コメントへの返信の表示と非表示
    openCommentReply(i) {
      this.modalPostComments[i].replyShow = true;
    },
    closeCommentReply(i) {
      this.modalPostComments[i].replyShow = false;
    },
    // コメントへのいいね機能
    commentGood(i) {
      const comment = this.modalPostComments[i];
      // comment.gooded = !comment.gooded;
      if (!comment.gooded) {
        axios.post('/api/comment/good/' + comment.id)
          .then(() => {
            this.$set(comment, 'gooded', true);
            this.$set(comment, 'goodCount', comment.goodCount + 1);
            // comment.goodCount++;
          }).catch(() => {
            return;
          });
      } else {
        axios.post('/api/comment/ungood/' + comment.id)
          .then(() => {
            this.$set(comment, 'gooded', false);
            this.$set(comment, 'goodCount', comment.goodCount - 1);
            // comment.goodCount--;
          }).catch(() => {
            return;
          });
      }
    },
    // コメントへの返信の投稿
    reply(i) {
      let data = new FormData();
      data.append('body', this.modalPostComments[i].replyInput);
      data.append('commentId', this.modalPostComments[i].id);
      axios.post('/api/comment/reply', data)
        .then((res) => {
          console.log(res.data);
          this.modalPostComments[i].replies.push(res.data);
          this.modalPostComments[i].replyCount++;
          this.modalPostComments[i].replyInput = '';
          this.modalPostComments[i].replyErrors = [];
          this.posts[this.modalPostIndex].commentCount++;
          this.modalPostCommentCount++;
          this.modalPostComments[i].replyShow = true;
          this.modalPostComments[i].replyFormOpened = false;
        }).catch((error) => {
          this.modalPostComments[i].replyErrors = error.response.data.errors;
          console.log(this.modalPostComments[0]);
        });
    },
    // reply(i) {
    //   let data = new FormData();
    //   data.append('body', this.modalPostComments[i].replyInput);
    //   data.append('commentId', this.modalPostComments[i].id);
    //   axios.post('/api/comment/reply', data)
    //     .then((res) => {
    //       console.log(res);
    //       this.newReply.id = res.data.replyId;
    //       this.newReply.created_at = 'たった今';
    //       this.newReply.body = this.modalPostComments[i].replyInput;
    //       this.newReply.user = this.authUser;
    //       this.newReply.goodCount = 0;
    //       this.newReply.gooded = false;
    //       console.log(this.newReply);
    //       this.modalPostComments[i].replies.push(this.newReply);
    //       this.modalPostComments[i].replyCount++;
    //       this.modalPostComments[i].replyInput = '';
    //       this.modalPostComments[i].replyErrors = [];
    //       this.newReply = {};
    //       this.posts[this.modalPostIndex].commentCount++;
    //       this.modalPostCommentCount++;
    //       this.modalPostComments[i].replyShow = true;
    //       this.modalPostComments[i].replyFormOpened = false;
    //     }).catch((error) => {
    //       console.log(error.response.data.errors.body[0]);
    //       this.modalPostComments[i].replyErrors = error.response.data.errors;
    //       // console.log(this.modalPostComments[i].replyErrors);
    //       console.log(this.modalPostComments[0]);
    //     });
    // },
    // コメントの返信へのいいね機能
    replyGood(comId, repId) {
      const comment = this.modalPostComments.find((v) => v.id == comId);
      const reply = comment.replies.find((v) => v.id == repId);
      reply.gooded = !reply.gooded;
      if (!reply.gooded) {
        axios.post('/api/comment/reply/ungood/' + repId)
          .then(() => {
            reply.goodCount--;
          }).catch(() => {
            return;
          });
      } else {
        axios.post('/api/comment/reply/good/' + repId)
          .then(() => {
            reply.goodCount++;
          }).catch(() => {
            return;
          });
      }
    },
    // コメントへの返信フォームの表示と非表示
    openReplyForm(i) {
      this.modalPostComments[i].replyFormOpened = true;
    },
    closeReplyForm(i) {
      this.modalPostComments[i].replyFormOpened = false;
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
  },

  computed: {
    // テキストエリアの高さを計算
    styles() {
      return {
        'height': this.height,
      }
    },
    commentStyles() {
      return {
        'height': this.commentHeight,
      }
    },
    // replyStyles(i) {
    //   return {
    //     'height': this.modalPostComments[i].replyHeight,
    //   }
    // },
  },

  watch: {
    // テキストエリアの高さの変化を監視
    'newPost.body': function(val) {
      this.changeHeight();
    },
    commentInput() {
      this.changeCommentHeight();
    },
    // modalPostComments: {
    //   handler: function() {
    //     this.changeReplyHeight();
    //   },
    //   deep: true
    // },
  },

  created() {
    window.addEventListener('resize', this.handleResize);
  },

  destroyed() {
    window.removeEventListener('resize', this.handleResize);
  },

  mounted() {
    // 初期化
    this.getInitInfo();
    // マウント時にもテキストエリアの高さをフレキシブルに
    this.changeHeight();
  },

  beforeRouteLeave (to, from, next) {
    if (this.modalPostShow || this.modalImageShow) {
      this.keepScrollWhenClose();
      this.modalPostShow = false;
      this.modalImageShow = false;
    }
    next();
  },

  // beforeRouteUpdate (to, from, next) {
  //   if (this.modalPostShow || this.modalImageShow) {
  //     this.keepScrollWhenClose();
  //     this.modalPostShow = false;
  //     this.modalImageShow = false;
  //   }
  //   next();
  // },
}
</script>