<template>
  <div class="main-home">
    
    <!-- 新規投稿 -->
    <div class="new-post">

      <!-- アイコン画像 -->
      <div class="logo-area">
        <img :src="'../image/unko.jpg'">
      </div>

      <!-- 入力エリア -->
      <div class="input-area">

        <!-- テキスト入力エリア -->
        <div class="input-1">
          <textarea v-model="text" ref="area" :style="styles" class="flex-textarea" placeholder="なんかあった？"></textarea>
        </div>

        <!-- タグ入力エリア -->
        <div class="input-tag">
          <input type="text" placeholder="タグ（例: #ああ #いい）">
        </div>

        <!-- 画像のプレビューエリア -->
        <div v-if="imageCount > 0" class="image-preview" >
          <div v-for="(url, index) in urls" :key="index" class="each-preview" :class="{ 'one-image-pre': imageCount == 1, 'two-image-pre': imageCount == 2, 'three-image-pre': imageCount == 3, 'four-image-pre': imageCount == 4, 'yokonaga': imageCount == 3 && index == 0 }">
            <div class="delete-image">
              <img class="batsu-icon" :src="'../image/batsu.png'" @click="deletePreview(url, index)">
            </div>
            <div class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + url + ')' }" :class="{ 'one-each-image': imageCount == 1, 'two-each-image': imageCount == 2, 'three-each-image': imageCount == 3, 'four-each-image': imageCount == 4, 'yokonaga-image': imageCount == 3 && index == 0 }"></div>
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
            <div class="submit-btn">投稿</div>
          </div>
        </div>

      </div>
    </div>

  
    <!-- 投稿一覧 -->
    <div v-for="(post, index) in posts" :key="post.id" class="posts">

      <!-- アイコン画像 -->
      <div class="logo-area">
        <router-link :to="{ name: 'user', params: { id: post.userId }}">
          <img :src="'../image/unko.jpg'">
        </router-link>
      </div>

      <!-- 内容表示エリア -->
      <div class="input-area">

        <!-- ユーザー名 -->
        <div class="user-name-post">{{ post.user }}</div>

        <!-- テキスト -->
        <div class="post-body">
          こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。<br>
          こんにちは。こんにちは。<br>
          こんにちは。こんにちは。
        </div>

        <!-- タグ -->
        <div v-if="post.tags" class="post-tags">
          <router-link :to="{ name: 'tags.new', params: { name: tag.replace('#', '') }}" v-for="(tag, index) in post.tags" :key="index">
            {{ tag }}
          </router-link>
        </div>

        <!-- 画像たち -->
        <div v-if="post.images.length > 0" class="post-image-view">
          <div v-for="(image, index) in post.images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.images.length == 1, 'two-image-pre': post.images.length == 2, 'three-image-pre': post.images.length == 3, 'four-image-pre': post.images.length == 4, 'yokonaga': post.images.length == 3 && index == 0 }">
            <div @click="openImageModal(image)" class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + image + ')' }" :class="{ 'one-each-image': post.images.length == 1, 'two-each-image': post.images.length == 2, 'three-each-image': post.images.length == 3, 'four-each-image': post.images.length == 4, 'yokonaga-image': post.images.length == 3 && index == 0 }"></div>
            </div>
          </div>
        </div>

        <!-- どんまいボタンとコメントボタンエリア -->
        <div class="post-btns">
          <!-- どんまいボタン -->
          <div class="post-donmai post-action-icon" @click="donmai(index)">
            <img v-if="!post.donmai" :src="'../image/donmai.png'" width="30px">
            <img v-if="post.donmai" :src="'../image/donmaied.png'" width="30px">
            {{ post.donmai_count }}
          </div>
          <!-- コメントボタン -->
          <div @click="openModalPost(index)" class="post-comment-icon post-action-icon">
            <img :src="'../image/comment.png'">
            {{ post.comment_count }}
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
              <img :src="'../image/unko.jpg'">
            </router-link>
          </div>

          <!-- 内容表示エリア -->
          <div class="input-area">

            <!-- ユーザー名 -->
            <div class="user-name-post">{{ modalPostUser }}</div>

            <!-- テキスト -->
            <div class="post-body">
              こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。<br>
              こんにちは。こんにちは。<br>
              こんにちは。こんにちは。
            </div>

            <!-- タグ -->
            <div v-if="modalPostTags" class="post-tags">
              <router-link :to="{ name: 'tags.new', params: { name: tag.replace('#', '') }}" v-for="(tag, index) in modalPostTags" :key="index">
                {{ tag }}
              </router-link>
            </div>

            <!-- 画像たち -->
            <div v-if="modalPostImages.length > 0" class="post-image-view">
              <div v-for="(image, index) in modalPostImages" :key="index" class="each-preview" :class="{ 'one-image-pre': modalPostImages.length == 1, 'two-image-pre': modalPostImages.length == 2, 'three-image-pre': modalPostImages.length == 3, 'four-image-pre': modalPostImages.length == 4, 'yokonaga': modalPostImages.length == 3 && index == 0 }">
                <div @click="openImageModal(image)" class="each-image-box">
                  <div class="each-image" :style="{ backgroundImage: 'url(' + image + ')' }" :class="{ 'one-each-image': modalPostImages.length == 1, 'two-each-image': modalPostImages.length == 2, 'three-each-image': modalPostImages.length == 3, 'four-each-image': modalPostImages.length == 4, 'yokonaga-image': modalPostImages.length == 3 && index == 0 }"></div>
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
                <img :src="'../image/unko.jpg'">
              </div>
            
              <div class="input-3">

                <textarea v-model="commentInput" ref="commentarea" :style="commentStyles" class="flex-textarea-2" placeholder="コメントを入力"></textarea>

                <div class="comment-btn-main">
                  コメント
                </div>

              </div>
              
            </div>

            <!-- コメントたち -->
            <div v-for="(comment, index) in modalPostComments" :key="comment.id" class="overlay-post-comment-area">

              <!-- アイコン -->
              <div class="overlay-post-comment-left">

                <router-link :to="{ name: 'user', params: { id: comment.userId }}">
                  <img :src="comment.icon">
                </router-link>

              </div>

              <div class="overlay-post-comment-right">

                <div class="overlay-post-comment-top">

                  <!-- 名前 -->
                  <div class="overlay-post-name">
                    {{ comment.user }}
                  </div>

                  <!-- 日付 -->
                  <div class="overlay-post-day">
                    {{ comment.day }}
                  </div>

                </div>

                <!-- コメント内容 -->
                <div class="overlay-post-comment">
                  うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。
                </div>

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
                    <img v-if="!comment.good" :src="'../image/good.png'" @click="commentGood(comment.id)">
                    <img v-if="comment.good" :src="'../image/gooded.png'" @click="commentGood(comment.id)">
                  </div>

                </div>

                <!-- コメントへの返信入力フォーム -->
                <div v-if="comment.replyFormOpened" class="input-3">

                  <!-- <textarea v-model="comment.replyInput" ref="replyarea" @keydown="changeReplyHeight(index)" :style="replyStyles(index)" class="flex-textarea-2" placeholder="コメントを入力"></textarea> -->
                  <textarea v-model="comment.replyInput" ref="replyarea" class="flex-textarea-2" placeholder="返信を入力"></textarea>

                  <div class="comment-reply-btns">

                    <div @click="closeReplyForm(index)" class="reply-cancel-btn">
                      キャンセル
                    </div>

                    <div class="comment-btn">
                      コメント
                    </div>

                  </div>

                </div>

                <!-- コメントへの返信 -->
                <div v-if="comment.replyShow">

                  <div v-for="reply in comment.replies" :key="reply.id" class="comment-reply-area">

                    <div class="overlay-post-comment-reply-area">

                      <!-- アイコン -->
                      <div class="overlay-post-comment-left">
                        <router-link :to="{ name: 'user', params: { id: reply.userId }}">
                          <img :src="reply.icon">
                        </router-link>
                      </div>

                      <div class="overlay-post-comment-right">

                        <div class="overlay-post-comment-top">

                          <!-- 名前 -->
                          <div class="overlay-post-name">
                            {{ reply.user }}
                          </div>

                          <!-- 日付 -->
                          <div class="overlay-post-day">
                            {{ reply.day }}
                          </div>

                        </div>

                        <!-- コメント内容 -->
                        <div class="overlay-post-comment">
                          うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。うんちあほ。
                        </div>

                        <div class="overlay-post-bottom">

                          <!-- コメントへの返信へのいいね -->
                          <div class="overlay-post-good">
                            {{ reply.goodCount }}
                            <img v-if="!reply.good" :src="'../image/good.png'" @click="replyGood(comment.id ,reply.id)">
                            <img v-if="reply.good" :src="'../image/gooded.png'" @click="replyGood(comment.id ,reply.id)">
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
        <img :src="modalImage" class="overlay-image-image">
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
              <div v-if="!user.followed" @click="donmaiFollowToggle(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="user.followed" @click="donmaiFollowToggle(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>
</template>


<script>
export default {
  data: function () {
    return {
      scrollPosition: null,
      value: '',
      urls: [],
      files: [],
      imageCount: 0,
      text: '',
      height: '20px',
      commentInput: '',
      commentHeight: '31px',
      // モーダル
      modalPostShow: false,
      modalPostId: null,
      modalPostUser: '',
      modalPostUserId: null,
      modalPostDonmai: false,
      modalPostDonmaiCount: null,
      modalPostImages: [],
      modalPostTags: [],
      modalPostCommentCount: 9,
      modalPostComments: [
        {
          id: 1,
          userId: 2,
          user: '鈴木誠也',
          icon: '../image/img1.jpg',
          day: '5日前',
          goodCount: 37,
          good: false,
          replyCount: 4,
          replyShow: false,
          replyFormOpened: false,
          replyInput: '',
          replyHeight: '31px',
          replies: [
            {
              id: 2,
              userId: 3,
              user: '柳田',
              icon: '../image/img5.jpg',
              day: '4日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 3,
              userId: 4,
              user: '筒香',
              icon: '../image/img6.jpg',
              day: '3日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 4,
              userId: 5,
              user: '田中将大',
              icon: '../image/img7.jpg',
              day: '2日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 5,
              userId: 6,
              user: 'ダルビッシュ',
              icon: '../image/img8.jpg',
              day: '1日前',
              goodCount: 37,
              good: false,
            },
          ],
        },
        {
          id: 2,
          userId: 4,
          user: 'ジーター',
          icon: '../image/img2.jpg',
          day: '4日前',
          goodCount: 37,
          good: false,
          replyCount: 0,
          replyShow: false,
          replyFormOpened: false,
          replyInput: '',
          replyHeight: '31px',
          replies: [],
        },
        {
          id: 3,
          userId: 6,
          user: 'ブライアント',
          icon: '../image/img3.jpg',
          day: '3日前',
          goodCount: 37,
          good: false,
          replyCount: 3,
          replyShow: false,
          replyFormOpened: false,
          replyInput: '',
          replyHeight: '31px',
          replies: [
            {
              id: 2,
              userId: 3,
              user: 'カーショウ',
              icon: '../image/img5.jpg',
              day: '4日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 3,
              userId: 4,
              user: 'シャーザー',
              icon: '../image/img6.jpg',
              day: '3日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 4,
              userId: 5,
              user: 'バーランダー',
              icon: '../image/img7.jpg',
              day: '2日前',
              goodCount: 37,
              good: false,
            },
          ],
        },
        {
          id: 4,
          userId: 8,
          user: '松井秀樹',
          icon: '../image/img4.jpg',
          day: '2日前',
          goodCount: 37,
          good: false,
          replyCount: 0,
          replyShow: false,
          replyFormOpened: false,
          replyInput: '',
          replyHeight: '31px',
          replies: [],
        },
      ],
      // 画像モーダル
      modalImageShow: false,
      modalImage: '',
      // どんまいモーダル
      modalDonmaiShow: false,
      modalDonmaiUsers: [
        {
          id: 11,
          icon: '../image/img1.jpg',
          name: 'ジェイコブ・デグロム',
          followed: false,
        },
        {
          id: 22,
          icon: '../image/img2.jpg',
          name: 'ゲリット・コール',
          followed: false,
        },
        {
          id: 33,
          icon: '../image/img3.jpg',
          name: '山本由伸',
          followed: false,
        },
        {
          id: 44,
          icon: '../image/img4.jpg',
          name: '千賀滉大',
          followed: false,
        },
        {
          id: 55,
          icon: '../image/img5.jpg',
          name: '菅野智之',
          followed: false,
        },
        {
          id: 66,
          icon: '../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 77,
          icon: '../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 88,
          icon: '../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 99,
          icon: '../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 1010,
          icon: '../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
      ],
      // 投稿
      posts: [
        {
          id: 1,
          user: 'なおとくん',
          userId: 1,
          donmai: false,
          donmai_count: 43,
          comment_count: 9,
          images: [
            '../image/img1.jpg',
            '../image/img3.jpg',
            '../image/img5.jpg',
            '../image/img7.jpg',
          ],
          tags: [
            '#ばか',
            '#あほ',
            '#うんち',
            '#ちんちん',
            '#ハゲ',
          ]
        },
        {
          id: 2,
          user: '城之内くん',
          userId: 2,
          donmai: false,
          donmai_count: 27,
          comment_count: 11,
          images: [
            '../image/img6.jpg',
          ],
          tags: [
            '#ひぐらし',
            '#にぱー',
            '#けいちゃん',
            '#おやしろさま',
          ]
        },
        {
          id: 3,
          user: 'マイケル',
          userId: 3,
          donmai: false,
          donmai_count: 65,
          comment_count: 12,
          images: [
            '../image/img1.jpg',
            '../image/img2.jpg',
            '../image/tatenaga.jpg',
          ],
          tags: [
            '#天気',
            '#洪水',
          ]
        },
      ],
    };
  },
  methods: {
    // 投稿のテキストエリアの高さをフレキシブルに
    changeHeight() {
      this.height = this.$refs.area.scrollHeight + 'px';
      this.height = 31 + 'px';
      this.$nextTick(() => {
        this.height = this.$refs.area.scrollHeight + 'px';
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
      const file = this.$refs.preview.files;
      if (this.files.length + file.length > 4) {
        window.alert('画像は最大４枚までです！');
      } else {
        this.files.push(...file);
        for (let i = 0; i < file.length; i++) {
          this.urls.push(URL.createObjectURL(file[i]));
        }
        this.$refs.preview.value = '';
      }
      this.imageCount = this.files.length;
      // console.log(this.files);
      // console.log(this.urls);
      // console.log(this.imageCount);
    },
    // 画像プレビューの削除
    deletePreview(url, index) {
      this.urls.splice(index, 1);
      this.files.splice(index, 1);
      URL.revokeObjectURL(url);
      this.imageCount = this.files.length;
      // console.log(this.files);
      // console.log(this.urls);
      // console.log(this.imageCount);
    },
    // どんまい機能の処理
    donmai(i) {
      const post = this.posts[i];
      post.donmai = !post.donmai;
      if (post.donmai == false) {
        post.donmai_count--;
      } else {
        post.donmai_count++;
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
    openModalPost(postIndex) {
      this.keepScrollWhenOpen();
      this.modalPostId = this.posts[postIndex].id;
      this.modalPostUser = this.posts[postIndex].user;
      this.modalPostUserId = this.posts[postIndex].userId;
      this.modalPostDonmai = this.posts[postIndex].donmai;
      this.modalPostDonmaiCount = this.posts[postIndex].donmai_count;
      this.modalPostImages = this.posts[postIndex].images;
      this.modalPostTags = this.posts[postIndex].tags;
      this.modalPostCommentCount = this.posts[postIndex].comment_count;
      this.modalPostShow = true;
    },
    closeModalPost() {
      this.keepScrollWhenClose();
      this.modalPostShow = false;
      this.modalPostId = null;
      this.modalPostUser = '';
      this.modalPostUserId = null;
      this.modalPostDonmai = false;
      this.modalPostDonmaiCount = null;
      this.modalPostImages = [];
      this.modalPostTags = [];
      this.modalPostCommentCount = null;
      this.modalPostComments.map(function(element) {
        element.replyShow = false;
      });
    },
    // どんまいしたユーザーのモーダルウィンドウの開閉
    openModalDonmai() {
      this.modalDonmaiShow = true;
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
      const overlayImage = document.querySelector('.overlay-image-image');
      const img = new Image();
      img.src = image;
      const img_width = img.width;
      const img_height = img.height;
      // console.log(overlayImage);
      // console.log(img_width);
      // console.log(img_height);
      if (img_height > img_width) {
        overlayImage.classList.add('height-is-bigger');
      }
    },
    closeImageModal() {
      if (!this.modalPostShow) {
        this.keepScrollWhenClose();
      }
      const overlayImage = document.querySelector('.overlay-image-image');
      overlayImage.classList.remove('height-is-bigger');
      this.modalImageShow = false;
      this.modalImage = '';
    },
    // どんまいしたユーザーのフォローとアンフォロー
    donmaiFollowToggle(i) {
      this.modalDonmaiUsers[i].followed = !this.modalDonmaiUsers[i].followed;
    },
    //コメントへの返信の表示と非表示
    openCommentReply(i) {
      this.modalPostComments[i].replyShow = true;
    },
    closeCommentReply(i) {
      this.modalPostComments[i].replyShow = false;
    },
    // コメントのいいね機能
    commentGood(i) {
      const comment = this.modalPostComments.find((v) => v.id == i);
      comment.good = !comment.good;
      if (!comment.good) {
        comment.goodCount--;
      } else {
        comment.goodCount++;
      }
    },
    // コメントの返信へのいいね機能
    replyGood(comId, repId) {
      const comment = this.modalPostComments.find((v) => v.id == comId);
      const reply = comment.replies.find((v) => v.id == repId);
      reply.good = !reply.good;
      if (!reply.good) {
        reply.goodCount--;
      } else {
        reply.goodCount++;
      }
    },
    // コメントへの返信フォームの表示と非表示
    openReplyForm(i) {
      this.modalPostComments[i].replyFormOpened = true;
    },
    closeReplyForm(i) {
      this.modalPostComments[i].replyFormOpened = false;
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
    text() {
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
  mounted() {
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
}
</script>