<template>

  <!-- 投稿一覧 -->
  <div class="tags-posts">

    <!-- 各投稿 -->
    <div v-for="(post, index) in posts" :key="post.id" class="posts">

      <!-- ロゴ画像 -->
      <div class="logo-area">
        <router-link :to="{ name: 'user', params: { id: post.userId }}">
          <img :src="post.userIcon">
        </router-link>
      </div>

      <!-- 内容表示エリア -->
      <div class="input-area">

        <!-- ユーザー名 -->
        <div class="user-name-post">{{ post.user }}</div>

        <!-- テキスト -->
        <div class="post-body">
          うんちしたいな。<br>
          シッコもしたい。<br>
          もう我慢できない。
        </div>

        <!-- タグ -->
        <div v-if="post.tags" class="post-tags">
          <router-link :to="{ name: 'tags.new', params: { name: tag.replace('#', '') }}" v-for="(tag, index) in post.tags" :key="index">
            {{ tag }}
          </router-link>
        </div>

        <!-- 画像表示 -->
        <div v-if="post.images.length > 0" class="post-image-view">
          <div v-for="(image, index) in post.images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.images.length == 1, 'two-image-pre': post.images.length == 2, 'three-image-pre': post.images.length == 3, 'four-image-pre': post.images.length == 4, 'yokonaga': post.images.length == 3 && index == 0 }">
            <div @click="openImageModal(image)" class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + image + ')' }" :class="{ 'one-each-image': post.images.length == 1, 'two-each-image': post.images.length == 2, 'three-each-image': post.images.length == 3, 'four-each-image': post.images.length == 4, 'yokonaga-image': post.images.length == 3 && index == 0 }"></div>
            </div>
          </div>
        </div>

        <!-- リアクションボタン -->
        <div class="post-btns">
          <!-- どんまいボタン -->
          <div class="post-donmai post-action-icon" @click="donmai(index)">
            <img v-if="!post.donmai" :src="'../../../image/donmai.png'" width="30px">
            <img v-if="post.donmai" :src="'../../../image/donmaied.png'" width="30px">
            {{ post.donmai_count }}
          </div>
          <!-- コメントボタン -->
          <div @click="openModalPost(index)" class="post-comment-icon post-action-icon">
            <img :src="'../../../image/comment.png'">
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
              <img :src="modalPostUserIcon">
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
                <img :src="'../../../image/unko.jpg'">
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
                    <img v-if="!comment.good" :src="'../../../image/good.png'" @click="commentGood(comment.id)">
                    <img v-if="comment.good" :src="'../../../image/gooded.png'" @click="commentGood(comment.id)">
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
                            <img v-if="!reply.good" :src="'../../../image/good.png'" @click="replyGood(comment.id ,reply.id)">
                            <img v-if="reply.good" :src="'../../../image/gooded.png'" @click="replyGood(comment.id ,reply.id)">
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
      commentInput: '',
      commentHeight: '31px',
      // モーダル
      modalPostShow: false,
      modalPostId: null,
      modalPostUser: '',
      modalPostUserId: null,
      modalPostUserIcon: '',
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
          icon: '../../../image/img1.jpg',
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
              icon: '../../../image/img5.jpg',
              day: '4日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 3,
              userId: 4,
              user: '筒香',
              icon: '../../../image/img6.jpg',
              day: '3日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 4,
              userId: 5,
              user: '田中将大',
              icon: '../../../image/img7.jpg',
              day: '2日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 5,
              userId: 6,
              user: 'ダルビッシュ',
              icon: '../../../image/img8.jpg',
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
          icon: '../../../image/img2.jpg',
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
          icon: '../../../image/img3.jpg',
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
              icon: '../../../image/img5.jpg',
              day: '4日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 3,
              userId: 4,
              user: 'シャーザー',
              icon: '../../../image/img6.jpg',
              day: '3日前',
              goodCount: 37,
              good: false,
            },
            {
              id: 4,
              userId: 5,
              user: 'バーランダー',
              icon: '../../../image/img7.jpg',
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
          icon: '../../../image/img4.jpg',
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
          icon: '../../../image/img1.jpg',
          name: 'ジェイコブ・デグロム',
          followed: false,
        },
        {
          id: 22,
          icon: '../../../image/img2.jpg',
          name: 'ゲリット・コール',
          followed: false,
        },
        {
          id: 33,
          icon: '../../../image/img3.jpg',
          name: '山本由伸',
          followed: false,
        },
        {
          id: 44,
          icon: '../../../image/img4.jpg',
          name: '千賀滉大',
          followed: false,
        },
        {
          id: 55,
          icon: '../../../image/img5.jpg',
          name: '菅野智之',
          followed: false,
        },
        {
          id: 66,
          icon: '../../../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 77,
          icon: '../../../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 88,
          icon: '../../../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 99,
          icon: '../../../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
        {
          id: 1010,
          icon: '../../../image/img6.jpg',
          name: 'うんこクソ野郎',
          followed: false,
        },
      ],
      // 投稿
      posts: [
        {
          id: 3,
          user: 'うんこマン',
          userId: 19,
          userIcon: '../../../image/img5.jpg',
          donmai: false,
          donmai_count: 10000,
          comment_count: 210,
          images: [
            '../../../image/img1.jpg',
          ],
          tags: [
            '#ばか',
            '#あほ',
            '#うんち',
            '#ちんちん',
            '#ハゲ',
          ],
        },
        {
          id: 2,
          user: 'ハゲ野郎',
          userId: 20,
          userIcon: '../../../image/img6.jpg',
          donmai: false,
          donmai_count: 9500,
          comment_count: 220,
          images: [
            '../../../image/img1.jpg',
            '../../../image/img6.jpg',
          ],
        },
        {
          id: 4,
          user: 'でべそ野郎',
          userId: 21,
          userIcon: '../../../image/img7.jpg',
          donmai: false,
          donmai_count: 8000,
          comment_count: 230,
          images: [],
        },
        {
          id: 1,
          user: 'ちんこ野郎',
          userId: 22,
          userIcon: '../../../image/img8.jpg',
          donmai: false,
          donmai_count: 7400,
          comment_count: 100,
          images: [
            '../../../image/img2.jpg',
            '../../../image/img3.jpg',
            '../../../image/img4.jpg',
          ],
        },
      ]
    };
  },
  methods: {
    // コメント入力欄の高さをフレキシブルに
    changeCommentHeight() {
      this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
      this.commentHeight = 18 + 'px';
      this.$nextTick(() => {
        this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
      });
    },
    // どんまい機能の処理
    donmai(index) {
      let post = this.posts[index];
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
      const searchPage = document.querySelector('.search-page');
      this.scrollPosition = window.pageYOffset;
      body.classList.add('bodyWhenOverlay');
      searchPage.classList.add('search-page-when-overlay');
      searchPage.style.top = -this.scrollPosition + 'px';
    },
    keepScrollWhenClose() {
      const body = document.querySelector('body');
      const searchPage = document.querySelector('.search-page');
      body.classList.remove('bodyWhenOverlay');
      searchPage.classList.remove('search-page-when-overlay');
      searchPage.style.top = null;
      window.scroll(0, this.scrollPosition);
      this.scrollPosition = null;
    },
    // 投稿のモーダルウィンドウの開閉
    openModalPost(postIndex) {
      this.keepScrollWhenOpen();
      this.modalPostId = this.posts[postIndex].id;
      this.modalPostUser = this.posts[postIndex].user;
      this.modalPostUserId = this.posts[postIndex].userId;
      this.modalPostUserIcon = this.posts[postIndex].userIcon;
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
    // コメント入力欄の高さを計算
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
    // コメント入力欄の高さの変化を監視
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