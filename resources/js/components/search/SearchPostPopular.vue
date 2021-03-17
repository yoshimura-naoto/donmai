<template>

  <!-- 投稿一覧 -->
  <div class="tags-posts">

    <!-- 各投稿 -->
    <div :key="reloadKey">

      <div v-for="(post, index) in posts" :key="post.id">

        <!-- axiosで取得した投稿が0でもなぜかpostsに１つオブジェクトが生成されるから「v-if="post.user"」でゴリ押しで消す！ -->
        <div class="posts" v-if="post.user">

          <!-- ロゴ画像 -->
          <div class="logo-area">
            <router-link :to="{ name: 'user', params: { id: post.user.id }}">
              <img v-if="post.user.icon" :src="post.user.icon">
              <img v-if="!post.user.icon" :src="'../../../image/user.png'">
            </router-link>
          </div>

          <!-- 内容表示エリア -->
          <div class="input-area">

            <div class="post-name-menu" v-if="post.user && authUser">
              <!-- ユーザー名 -->
              <div class="user-name-post">
                <router-link :to="{ name: 'user', params: { id: post.user.id }}">
                  {{ post.user.name }}
                </router-link>
              </div>
              <!-- メニューボタン -->
              <div v-if="post.user.id === authUser.id && !post.postMenuOpened" @click="openPostMenu(index)" class="post-menu-btn">
                ...
              </div>
              <!-- メニュー -->
              <div v-if="post.postMenuOpened" class="post-menus">
                <div class="post-menu-edit" @click="editPostModalOpen(index)">編集</div>
                <div class="post-menu-delete" @click="deletePostModalOpen(index)">削除</div>
              </div>
              <!-- メニューのモーダルカバー -->
              <div v-if="post.postMenuOpened" @click="closePostMenu(index)" class="post-menu-cover"></div>
            </div>

            <!-- テキスト -->
            <div class="post-body">{{ post.body }}</div>

            <!-- タグ -->
            <div v-if="post.tags" class="post-tags">
              <router-link :to="{ name: 'tags.new', params: { name: tag.name }}" v-for="(tag, index) in post.tags" :key="index">
                #{{ tag.name }}
              </router-link>
            </div>

            <!-- 画像表示 -->
            <div v-if="post.post_images.length > 0" class="post-image-view">
              <div v-for="(image, index) in post.post_images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.post_images.length == 1, 'two-image-pre': post.post_images.length == 2, 'three-image-pre': post.post_images.length == 3, 'four-image-pre': post.post_images.length == 4, 'yokonaga': post.post_images.length == 3 && index == 0 }">
                <div @click="openImageModal(image.path)" class="each-image-box">
                  <div class="each-image" :style="{ backgroundImage: 'url(' + image.path + ')' }" :class="{ 'one-each-image': post.post_images.length == 1, 'two-each-image': post.post_images.length == 2, 'three-each-image': post.post_images.length == 3, 'four-each-image': post.post_images.length == 4, 'yokonaga-image': post.post_images.length == 3 && index == 0 }"></div>
                </div>
              </div>
            </div>

            <!-- リアクションボタン -->
            <div class="post-btns">
              <!-- どんまいボタン -->
              <div class="post-donmai post-action-icon" @click="donmai(index)">
                <img v-if="!post.donmai" :src="'../../../image/donmai.png'" width="30px">
                <img v-if="post.donmai" :src="'../../../image/donmaied.png'" width="30px">
                {{ post.donmaiCount }}
              </div>
              <!-- コメントボタン -->
              <div @click="openModalPost(index)" class="post-comment-icon post-action-icon">
                <img :src="'../../../image/comment.png'">
                {{ post.commentCount }}
              </div>
            </div>

          </div>

        </div>

      </div>

      <!-- 読み込み中 -->
      <div v-if="postsLoading" class="loading">読み込み中...</div>

    </div>


    <!-- 投稿モーダル -->
    <div v-if="modalPostShow" @click.self="closeModalPost" class="overlay-post">

      <div class="overlay-post-content">

        <!-- 投稿 -->
        <div class="posts-overlay" @scroll="commentsPaginate">

          <!-- アイコン画像 -->
          <div class="logo-area">
            <router-link :to="{ name: 'user', params: { id: modalPostUserId }}">
              <img v-if="modalPostUserIcon" :src="modalPostUserIcon">
              <img v-if="!modalPostUserIcon" :src="'../../../image/user.png'">
            </router-link>
          </div>

          <!-- 内容表示エリア -->
          <div class="input-area">

            <!-- ユーザー名 -->
            <div class="user-name-post">
              <router-link :to="{ name: 'user', params: { id: modalPostUserId }}">
                {{ modalPostUserName }}
              </router-link>
            </div>

            <!-- テキスト -->
            <div class="post-body">{{ modalPostBody }}</div>

            <!-- タグ -->
            <div v-if="modalPostTags" class="post-tags">
              <router-link :to="{ name: 'tags.new', params: { name: tag.name }}" v-for="(tag, index) in modalPostTags" :key="index" @click.native="fromModalTagToTag">
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
                <div v-if="tooLongCommentMessage" class="user-edit-error">
                  {{ tooLongCommentMessage }}
                </div>

                <div class="comment-btn-main" @click="commentPost">
                  コメント
                </div>

              </div>
              
            </div>

            <!-- コメントたち -->
            <div v-for="(comment, index) in modalPostComments" :key="comment.id" class="overlay-post-comment-area">

              <!-- アイコン -->
              <div v-if="comment.user.icon" class="overlay-post-comment-left">
                <router-link :to="{ name: 'user', params: { id: comment.user.id }}">
                  <img :src="comment.user.icon">
                </router-link>
              </div>
              <div v-if="!comment.user.icon" class="overlay-post-comment-left">
                <router-link :to="{ name: 'user', params: { id: comment.user.id }}">
                  <img :src="'../../../image/user.png'">
                </router-link>
              </div>

              <div class="overlay-post-comment-right">

                <div class="overlay-post-comment-top">

                  <!-- 名前 -->
                  <div class="overlay-post-name">
                    <router-link :to="{ name: 'user', params: { id: comment.user.id }}">
                      {{ comment.user.name }}
                    </router-link>
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
                    <img v-if="!comment.gooded" :src="'../../../image/good.png'" @click="commentGood(index)">
                    <img v-if="comment.gooded" :src="'../../../image/gooded.png'" @click="commentGood(index)">
                  </div>

                </div>

                <!-- コメントへの返信入力フォーム -->
                <div v-if="comment.replyFormOpened" class="input-3">

                  <!-- <textarea v-model="comment.replyInput" ref="replyarea" @keydown="changeReplyHeight(index)" :style="replyStyles(index)" class="flex-textarea-2" placeholder="コメントを入力"></textarea> -->
                  <textarea v-model="comment.replyInput" ref="replyarea" class="flex-textarea-2" placeholder="返信を入力"></textarea>

                  <div v-if="comment.replyErrors.body" class="user-edit-error">
                    {{ comment.replyErrors.body[0] }}
                  </div>
                  <div v-if="comment.tooLongReplyMessage" class="user-edit-error">
                    {{ comment.tooLongReplyMessage }}
                  </div>

                  <div class="comment-reply-btns">

                    <div @click="closeReplyForm(index)" class="reply-cancel-btn">
                      キャンセル
                    </div>

                    <div class="comment-btn" @click="reply(index)">
                      返信
                    </div>

                  </div>

                </div>

                <!-- コメントへの返信 -->
                <div v-if="comment.replyShow">

                  <div v-for="reply in comment.replies" :key="reply.id" class="comment-reply-area">

                    <div class="overlay-post-comment-reply-area">

                      <!-- アイコン -->
                      <div v-if="reply.user.icon" class="overlay-post-comment-left">
                        <router-link :to="{ name: 'user', params: { id: reply.user.id }}">
                          <img :src="reply.user.icon">
                        </router-link>
                      </div>
                      <div v-if="!reply.user.icon" class="overlay-post-comment-left">
                        <router-link :to="{ name: 'user', params: { id: reply.user.id }}">
                          <img :src="'../../../image/user.png'">
                        </router-link>
                      </div>

                      <div class="overlay-post-comment-right">

                        <div class="overlay-post-comment-top">

                          <!-- 名前 -->
                          <div class="overlay-post-name">
                            <router-link :to="{ name: 'user', params: { id: reply.user.id }}">
                              {{ reply.user.name }}
                            </router-link>
                          </div>

                          <!-- 日付 -->
                          <div class="overlay-post-day">
                            {{ reply.created_at }}
                          </div>

                        </div>

                        <!-- コメント内容 -->
                        <div class="overlay-post-comment">{{ reply.body }}</div>

                        <div class="overlay-post-bottom">

                          <!-- 返信の削除ボタン -->
                          <div v-if="reply.user.id === authUser.id" @click="deleteReply(comment.id, reply.id)" class="reply-delete">
                            削除
                          </div>

                          <!-- コメントへの返信へのいいね -->
                          <div class="overlay-post-good">
                            {{ reply.goodCount }}
                            <img v-if="!reply.gooded" :src="'../../../image/good.png'" @click="replyGood(comment.id ,reply.id)">
                            <img v-if="reply.gooded" :src="'../../../image/gooded.png'" @click="replyGood(comment.id ,reply.id)">
                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                  <div v-if="comment.loadRepliesMore && !comment.repliesLoading" @click="getReplies(index)" class="reply-read-more">↪︎さらに読み込む</div>

                </div>

              </div>

              <div v-if="comment.user.id === authUser.id" @click="deleteComment(index)" class="comment-delete">
                削除
              </div>

            </div>

            <div v-if="commentsLoading" class="comments-loading">読み込み中...</div>

          </div>

        </div>

      </div>

    </div>


    <!-- 画像モーダル -->
    <div v-show="modalImageShow" @click.self="closeImageModal" class="overlay-image">

      <div class="overlay-image-box">
        <img :src="'../../../image/batsu.png'" @click="closeImageModal" class="image-modal-close">
        <img :src="modalImage" @load="bigImageResize" class="overlay-image-image" :class="{'height-is-bigger':heightIsBigger}">
      </div>

    </div>


    <!-- 投稿編集モーダル -->
    <div v-if="modalPostEditShow" @click.self="editPostModalClose" class="overlay-post">

      <!-- <div class="new-post"> -->
      <div class="posts-edit-overlay">

        <!-- アイコン画像 -->
        <div class="logo-area" v-if="authUser">
          <img :src="authUser.icon">
        </div>

        <!-- 入力エリア -->
        <div class="input-area">

          <!-- テキスト入力エリア -->
          <div class="input-1">
            <textarea v-model="editPost.body" ref="editarea" :style="editStyles" class="flex-textarea"></textarea>
          </div>

          <div v-if="editErrors.body" class="user-edit-error">
            {{ editErrors.body[0] }}
          </div>
          <div v-if="tooLongEditBodyMessage" class="user-edit-error">
            {{ tooLongEditBodyMessage }}
          </div>

          <!-- ジャンル選択エリア -->
          <div class="select-genre">
            <select name="genre" v-model="editPost.genreIndex">
              <option value='' selected>ジャンルを選択してください</option>
              <option v-for="(genre, index) in genres" :key="index" :value="index">{{ genre.name }}</option>
            </select>

            <div v-if="editErrors.genreIndex" class="user-edit-error">
              {{ editErrors.genreIndex[0] }}
            </div>
          </div>

          <!-- タグ入力エリア -->
          <div class="input-tag">
            <input type="text" placeholder="タグ（例: #ああ #いい）" v-model="editPost.tags">
          </div>

          <div v-if="editErrors.tags" class="user-edit-error">
            {{ editErrors.tags[0] }}
          </div>

          <!-- 画像のプレビューエリア -->
          <div v-if="editUrls.length > 0" class="image-preview" >
            <div v-for="(url, index) in editUrls" :key="index" class="each-preview" :class="{ 'one-image-pre': editUrls.length == 1, 'two-image-pre': editUrls.length == 2, 'three-image-pre': editUrls.length == 3, 'four-image-pre': editUrls.length == 4, 'yokonaga': editUrls.length == 3 && index == 0 }">
              <div class="delete-image">
                <img class="batsu-icon" :src="'../../../image/batsu.png'" @click="deleteEditPreview(url.id, url.path, index)">
              </div>
              <div class="each-image-box">
                <div class="each-image" :style="{ backgroundImage: 'url(' + url.path + ')' }" :class="{ 'one-each-image': editUrls.length == 1, 'two-each-image': editUrls.length == 2, 'three-each-image': editUrls.length == 3, 'four-each-image': editUrls.length == 4, 'yokonaga-image': editUrls.length == 3 && index == 0 }"></div>
              </div>
            </div>
          </div>

          <!-- 画像追加ボタンと投稿ボタン -->
          <div class="input-2">

            <div class="image-add">
              <label>
                <img class="image-icon" :src="'../../../image/image.png'" alt="画像追加">
                <input class="file-upload" type="file" ref="editpreview" @change="uploadEditFile" accept="image/*" multiple>
              </label>
            </div>

            <div class="submit">
              <div class="submit-btn" @click="editSubmit">更新</div>
            </div>

            <!-- テスト用 -->
            <!-- <div class="submit">
              <div class="submit-btn" @click="editCheck">チェック</div>
            </div> -->
            
          </div>

        </div>
      </div>
      
    </div>


    <!-- 削除の確認モーダル -->
    <div v-if="deletePostModalOpened" @click.self="deletePostModalClose" class="delete-post-modal-cover">
      <div class="post-delete-check">
        <div class="post-delete-really">投稿を削除しますか？</div>
        <div class="post-delete-btns">
          <div class="post-delete-cancel" @click="deletePostModalClose">キャンセル</div>
          <div class="post-delete-delete" @click="deletePost">削除</div>
        </div>
      </div>
    </div>


    <!-- 「どんまい」モーダル -->
    <div v-if="modalDonmaiShow" @click.self="closeModalDonmai" class="overlay-post">

      <div class="overlay-donmai-content">

        <div class="overlay-donmai-title">
          どんまい！
        </div>

        <div class="donmai-user-box" @scroll="donmaiPaginate">

          <div v-for="(user, index) in modalDonmaiUsers" :key="user.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div v-if="user.icon" class="overlay-donmai-user-icon">
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  <img :src="user.icon">
                </router-link>
              </div>
              <div v-if="!user.icon" class="overlay-donmai-user-icon">
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  <img :src="'../../../image/user.png'">
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
  data: function () {
    return {
      // 認証ユーザー
      authUser: null,
      // 投稿一覧部分のみリロードするためのキー
      reloadKey: 0,
      // 縦長の画像の幅の調整
      tatenagaImageWidth: null,
      heightIsBigger: false,
      // スクロール位置
      scrollPosition: null,
      // 入力フォームの高さ調整用
      editHeight: '20px',
      // ジャンル
      genres: [],
      // 投稿の編集
      editPost: {
        id: null,
        body: '',
        genreIndex: '',
        tags: '',
        deleteOldImagesId: [], // 削除する既存の画像のidたち
        newImageFiles: [], // 新たに追加する画像ファイルたち
      },
      newImages: [
          // {
            // id: (マイナス値)
            // file: (File)
          // }
        ], 
      nextNewImageId: -1, // 新たに追加する画像のid（マイナス値）
      // 投稿編集の画像プレビュー用のURL
      editUrls: [
        // {
        //   id: null, (マイナス値なら新しいイメージ、プラス値なら既存のイメージ)
        //   path: '',
        // }
      ],
      tooLongEditBodyMessage: '',
      editErrors: [],
      edited: false,  // 編集したかどうか
      editProcessing: false,  // 編集の処理中
      // 無限スクロール用
      postsLoading: false,
      loadMorePosts: true,
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
      // コメント
      commentInput: '',
      commentHeight: '31px',
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
        //   tooLongReplyMessage: '',
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
        //   コメントへの返信の無限スクロール用
        //   repliesLoading: false,
        //   loadRepliesMore: true,
        // },
      ],
      // コメントの無限スクロール用
      commentsLoading: false,
      loadCommentsMore: true,
      // 新規コメント、返信
      newComment: {},
      newReply: {},
      // コメント投稿関連
      commentErrors: [],
      tooLongCommentMessage: '', // コメントが長すぎるというメッセージ
      commentProcessing: false, // コメント処理中
      replyProcessing: false,
      // 画像モーダル
      modalImageShow: false,
      modalImage: '',
      // 削除の確認のモーダル
      deletePostModalOpened: false,
      deletePostIndex: null,
      deleteProssesing: false,
      // 投稿編集モーダル
      modalPostEditShow: false,
      editPostIndex: null,
      // どんまいモーダル
      modalDonmaiShow: false,
      modalDonmaiUsers: [
        // {
        //   id: 11,
        //   icon: '../image/img1.jpg',
        //   name: 'ジェイコブ・デグロム',
        //   followed: false,
        // },
      ],
      // どんまいモーダルの無限スクロール用
      donmaiLoading: false,
      donmaiLoadMore: true,
      donmaiPage: 1,
      isLastDonmaiPage: false,
    };
  },

  methods: {
    // 投稿編集のチェックのテスト
    editCheck() {
      console.log(this.editPost);
      console.log(this.newImages);
    },
    // 認証ユーザー情報、全ジャンル、投稿を取得
    getInitInfo(word) {
      axios.get('/api/authandgenre')
        .then((res) => {
          // console.log(res.data);
          this.authUser = res.data.authUser;
          this.genres = res.data.genres;
          if (!this.authUser.icon) {
            this.authUser.icon = '../../../image/user.png';
          }
          this.getPosts(word);
        }).catch((error) => {
          console.log(error);
        });
    },
    // 投稿の取得（無限スクロール）
    getPosts(word) {
      if (!this.loadMorePosts) return;
      if (this.postsLoading) return;
      this.postsLoading = true;
      const postIds = this.posts.map((obj) => {
        return obj.id;
      });
      const postIdsString = postIds.join('-');
      // axios.get('/api/post/search/popular/' + word + '?loaded_posts_count=' + this.posts.length)
      axios.get('/api/post/search/popular/' + word + '?loaded_post_ids=' + postIdsString)
        .then((res) => {
          // console.log(res.data);
          this.posts.push(...res.data.posts);
          if (this.posts.length === res.data.postsTotal) {
            this.loadMorePosts = false;
          }
          this.postsLoading = false;
          this.$nextTick(() => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
            if (bottomOfWindow && this.posts.length < res.data.postsTotal) this.getPosts(word);
          });
          // console.log(this.posts.length);
        }).catch((error) => {
          console.log(error);
          this.postsLoading = false;
        });
    },
    // 無限スクロールのリセット
    resetPaginate() {
      this.posts = [];
      this.postsLoading = false;
      this.loadMorePosts = true;
    },
    // 投稿編集のテキストエリアの高さをフレキシブルに
    changeEditHeight() {
      if (this.$refs.editarea) {
        this.editHeight = this.$refs.editarea.scrollHeight + 'px';
        this.editHeight = 25 + 'px';
        this.$nextTick(() => {
          this.editHeight = this.$refs.editarea.scrollHeight + 'px';
        });
      }
    },
    // コメント入力欄の高さをフレキシブルに
    changeCommentHeight() {
      if (this.$refs.commentarea) {
        this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
        this.commentHeight = 18 + 'px';
        this.$nextTick(() => {
          this.commentHeight = this.$refs.commentarea.scrollHeight + 'px';
        });
      }
    },
    // 投稿の編集・削除メニューの開閉
    openPostMenu(i) {
      this.posts.splice(i, 1, {
        ...this.posts[i],
        postMenuOpened: true
      });
    },
    closePostMenu(i) {
      this.posts.splice(i, 1, {
        ...this.posts[i],
        postMenuOpened: false
      });
    },
    // 投稿の削除確認モーダルの開閉
    deletePostModalOpen(i) {
      this.keepScrollWhenOpen();
      this.deletePostIndex = i;
      this.deletePostModalOpened = true;
    },
    deletePostModalClose() {
      this.keepScrollWhenClose();
      this.deletePostModalOpened = false;
      this.closePostMenu(this.deletePostIndex);
      this.deletePostIndex = null;
    },
    // 投稿の削除
    deletePost() {
      if (this.deleteProssesing) return;
      this.deleteProssesing = true;
      axios.post('/api/post/delete/' + this.posts[this.deletePostIndex].id)
        .then(() => {
          this.posts.splice(this.deletePostIndex, 1);
          this.deletePostModalClose();
          this.deleteProssesing = false;
          this.$nextTick(() => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
            if (bottomOfWindow) this.getPosts();
          });
        }).catch((error) => {
          console.log(error);
          this.deleteProssesing = false;
        });
    },
    // 投稿編集モーダルを開く
    editPostModalOpen(i) {
      // スクロール位置の維持
      this.keepScrollWhenOpen();
      // 投稿のidを取得
      this.editPost.id = this.posts[i].id;
      // 本文を取得
      this.editPost.body = this.posts[i].body;
      // ジャンルのインデックスを取得
      const genreRoutes = this.genres.map((obj) => {
        return obj.route;
      });
      this.editPost.genreIndex = genreRoutes.indexOf(this.posts[i].genre.route);
      // タグを文字列化して取得
      if (this.posts[i].tags.length > 0) {
        const tagNames = this.posts[i].tags.map((obj) => {
          return obj.name;
        });
        this.editPost.tags = '#' + tagNames.join(' #');
      }
      // 既存の画像のid、urlを取得
      for (let j = 0; j < this.posts[i].post_images.length; j++) {
        const addEditUrl = new Object();
        addEditUrl.id = this.posts[i].post_images[j].id;
        addEditUrl.path = this.posts[i].post_images[j].path;
        this.editUrls.push(addEditUrl);
      }
      // モーダルを開く
      this.modalPostEditShow = true;
      // テキストエリアの高さをフレキシブルに
      this.$nextTick(() => {
        this.editHeight = this.$refs.editarea.scrollHeight + 'px';
      });
      // 今編集してるpostsのインデックスを記憶
      this.editPostIndex = i;
      // console.log(this.editPost);
      // console.log(this.editUrls);
    },
    // 投稿編集モーダルを閉じる
    editPostModalClose() {
      this.keepScrollWhenClose();
      this.editPost.id = null;
      this.editPost.body = '';
      this.editPost.genreIndex = '';
      this.editPost.tags = '';
      this.editPost.deleteOldImagesId = [];
      this.editPost.newImageFiles = [];
      this.newImages = [];
      this.editUrls = [];
      this.editErrors = [];
      this.nextNewImageId = -1;
      this.editHeight = '20px';
      if (!this.editProcessing) {
        this.closePostMenu(this.editPostIndex);
      }
      this.modalPostEditShow = false;
      // console.log(this.editPost);
      // console.log(this.posts);
    },
    // 投稿編集の画像アップロード
    uploadEditFile() {
      const files = this.$refs.editpreview.files;
      if (this.editUrls.length + files.length > 4) {  // 枚数制限バリデーション
        window.alert('画像は４枚までです！');
        return;
      }
      let totalFileSize = 0;
      for (let i = 0; i < this.newImages.length; i++) {
        totalFileSize += this.newImages[i].file.size;
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
            for (let i = 0; i < files.length; i++) {
              const addFile = new Object();
              addFile.id = this.nextNewImageId; // 新しく追加する画像にはマイナス値のidを付与（既存の画像と区別するため）
              addFile.file = files[i];
              this.newImages.push(addFile);
              // プレビュー用URLをプッシュ
              const addUrl = new Object();
              addUrl.id = this.nextNewImageId;
              addUrl.path = URL.createObjectURL(files[i]);
              this.editUrls.push(addUrl);
              this.nextNewImageId--;
            }
            this.$refs.editpreview.value = '';
          }
        });
      }
    },
    // 投稿編集の画像プレビューの削除
    deleteEditPreview(id, path, index) {
      if (id < 0) {
        // idがマイナス値（新しく追加する画像）の場合はnewImagesから削除
        const arrayId = this.newImages.map((obj) => {
          return obj.id;
        });
        this.newImages.splice(arrayId.indexOf(id), 1);
      } else {
        // 削除する既存の画像のidをdeleteOldImagesIdに追加
        this.editPost.deleteOldImagesId.push(id);
      }
      // プレビュー用URLの削除
      URL.revokeObjectURL(path);
      this.editUrls.splice(index, 1);
      // console.log(this.editPost.deleteOldImagesId);
      // console.log(this.newImages);
      // console.log(this.editUrls);
    },
    // 編集した投稿を送信
    editSubmit() {
      if (this.editProcessing) return;
      this.editProcessing = true;
      // 文字数判定
      let textCount = this.editPost.body.replace(/\n/g, '').length;
      // console.log(textCount);
      if (textCount > 250) {
        this.tooLongEditBodyMessage = '250文字以内にしてください！（現在' + textCount + '文字）';
        this.editErrors = [];
        this.editProcessing = false;
        return;
      } else {
        this.tooLongEditBodyMessage = '';
      }
      this.editPost.newImageFiles = this.newImages.map((obj) => {
        return obj.file;
      });
      let data = new FormData();
      Object.entries(this.editPost).forEach(([key, value]) => {
        if (Array.isArray(value)) {
          value.forEach((v, i) => {
            data.append(key + '[]', v);
          })
        } else {
          data.append(key, value);
        }
      });
      axios.post('/api/post/edit', data)
        .then((res) => {
          // 検索ワードのタグを削除していたらこのページの投稿一覧から削除
          if (this.editPost.tags.includes(this.$route.params.word)) {
            this.posts.splice(this.editPostIndex, 1, res.data.post);
          } else {
            this.posts.splice(this.editPostIndex, 1);
          }
          this.editPostModalClose();
          this.editProcessing = false;
          this.$nextTick(() => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
            if (bottomOfWindow) this.getPosts();
          });
        }).catch((error) => {
          this.editErrors = error.response.data.errors;
          this.editProcessing = false;
        });
    },
    // どんまい機能の処理
    donmai(i) {
      let post = this.posts[i];
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
    openModalPost(i) {
      this.modalPostIndex = i;
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
      this.getComments(); //コメント取得
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
      this.modalPostComments = [];
      this.modalPostComments.map(function(element) {
        element.replyShow = false;
      });
      // 無限スクロール設定の初期化
      this.commentsLoading = false;
      this.loadCommentsMore = true;
    },
    // コメントの取得
    getComments() {
      if (!this.loadCommentsMore) return;
      if (this.commentsLoading) return;
      this.commentsLoading = true;
      axios.get('/api/comments/get/' + this.modalPostId + '?loaded_comments_count=' + this.modalPostComments.length)
        .then((res) => {
          // console.log(res.data);
          this.modalPostComments.push(...res.data.comments);
          if (this.modalPostComments.length === res.data.commentsTotal) {
            this.loadCommentsMore = false;
          }
          this.commentsLoading = false;
          // console.log(this.modalPostComments.length);
        }).catch((error) => {
          console.log(error);
          this.commentsLoading = false;
        });
    },
    // コメントの無限スクロールページネーション
    commentsPaginate() {
      const postsOverlay = document.querySelector('.posts-overlay');
      let bottomOfModal = postsOverlay.scrollTop + postsOverlay.clientHeight >= postsOverlay.scrollHeight - 1;
      if (bottomOfModal) {
        this.getComments();
      }
    },
    // どんまいしたユーザーの取得
    getDonmaiUsers() {
      if (this.isLastDonmaiPage) return;
      if (this.donmaiLoading) return;
      this.donmaiLoading = true;
      axios.get('/api/donmai/users/' + this.modalPostId + '?page=' + this.donmaiPage)
        .then((res) => {
          // console.log(res.data);
          const users = res.data.data.map((obj) => {
            return obj.user;
          });
          this.modalDonmaiUsers.push(...users);
          this.donmaiLoading = false;
          if (this.donmaiPage === res.data.last_page) {
            this.isLastDonmaiPage = true;
          }
          this.donmaiPage++;
        }).catch((error) => {
          console.log(error);
          this.donmaiLoading = false;
        });
    },
    // どんまいしたユーザー一覧のモーダルを開く
    openModalDonmai() {
      this.modalDonmaiShow = true;
      this.getDonmaiUsers();
    },
    closeModalDonmai() {
      this.modalDonmaiShow = false;
      this.modalDonmaiUsers = [];
      // 無限スクロール設定のリセット
      this.donmaiLoading = false;
      this.donmaiLoadMore = true;
      this.donmaiPage = 1;
      this.isLastDonmaiPage = false;
    },
    // どんまいしたユーザー一覧モーダルの無限スクロール
    donmaiPaginate() {
      const donmaiModal = document.querySelector('.donmai-user-box');
      let bottomOfModal = donmaiModal.scrollTop + donmaiModal.clientHeight >= donmaiModal.scrollHeight - 1;
      if (bottomOfModal) {
        this.getDonmaiUsers();
      }
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
      if (!this.modalPostShow) {
        this.keepScrollWhenClose();
      }
      const overlayImage = document.querySelector('.overlay-image-image');
      overlayImage.style.maxWidth = 800 + 'px';
      overlayImage.style.maxHeight = 'none';
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
      if (this.commentProcessing) return;
      this.commentProcessing = true;
      // 文字数判定
      let textCount = this.commentInput.replace(/\n/g, '').length;
      // console.log(textCount);
      if (textCount > 200) {
        this.tooLongCommentMessage = '200文字以内にしてください！（現在' + textCount + '文字）';
        this.commentErrors = [];
        this.commentProcessing = false;
        return;
      } else {
        this.tooLongCommentMessage = '';
      }
      let data = new FormData();
      data.append('postId', this.modalPostId);
      data.append('body', this.commentInput);
      axios.post('/api/comment', data)
        .then((res) => {
          // console.log(res.data);
          this.modalPostComments.unshift(res.data);
          this.commentInput = '';
          this.posts[this.modalPostIndex].commentCount++;
          this.modalPostCommentCount++;
          this.commentErrors = [];
          this.commentProcessing = false;
        }).catch((error) => {
          // console.log(error.response.data.errors);
          // this.modalPostComments[i].replyErrors = [];
          this.commentErrors = error.response.data.errors;
          this.commentProcessing = false;
        });
    },
    // コメント削除
    deleteComment(i) {
      if (window.confirm('削除しますか？')) {
        axios.post('/api/comment/delete/' + this.modalPostComments[i].id)
          .then(() => {
            this.modalPostComments.splice(i, 1);
            this.modalPostCommentCount--;
            this.posts[this.modalPostIndex].commentCount--;
          }).catch((error) => {
            console.log(error);
          });
      }
    },
    // コメントへの返信の取得
    getReplies(i) {
      if (!this.modalPostComments[i].loadRepliesMore) return;
      if (this.modalPostComments[i].repliesLoading) return;
      this.modalPostComments[i].repliesLoading = true;
      axios.get('/api/replies/get/' + this.modalPostComments[i].id + '?loaded_replies_count=' + this.modalPostComments[i].replies.length)
        .then((res) => {
          // console.log(res.data);
          this.modalPostComments[i].replies.push(...res.data.replies);
          if (this.modalPostComments[i].replies.length === res.data.repliesTotal) {
            this.modalPostComments[i].loadRepliesMore = false;
          }
          this.modalPostComments[i].repliesLoading = false;
          // console.log(this.modalPostComments[i].replies.length);
        }).catch((error) => {
          console.log(error);
          this.modalPostComments[i].repliesLoading = false;
        });
    },
    //コメントへの返信の表示と非表示
    openCommentReply(i) {
      this.getReplies(i);
      this.modalPostComments[i].replyShow = true;
    },
    closeCommentReply(i) {
      this.modalPostComments[i].replyShow = false;
    },
    // コメントのいいね機能
    commentGood(i) {
      const comment = this.modalPostComments[i];
      comment.gooded = !comment.gooded;
      if (!comment.gooded) {
        axios.post('/api/comment/ungood/' + comment.id)
          .then(() => {
            comment.goodCount--;
          }).catch(() => {
            return;
          });
      } else {
        axios.post('/api/comment/good/' + comment.id)
          .then(() => {
            comment.goodCount++;
          }).catch(() => {
            return;
          });
      }
    },
    // コメントへの返信の投稿
    reply(i) {
      if (this.replyProcessing) return;
      this.replyProcessing = true;
      // 文字数判定
      let textCount = this.modalPostComments[i].replyInput.replace(/\n/g, '').length;
      // console.log(textCount);
      if (textCount > 200) {
        this.modalPostComments[i].tooLongReplyMessage = '200文字以内にしてください！（現在' + textCount + '文字）';
        this.modalPostComments[i].replyErrors = [];
        this.replyProcessing = false;
        return;
      } else {
        this.modalPostComments[i].tooLongReplyMessage = '';
      }
      let data = new FormData();
      data.append('body', this.modalPostComments[i].replyInput);
      data.append('commentId', this.modalPostComments[i].id);
      axios.post('/api/comment/reply', data)
        .then((res) => {
          // console.log(res.data);
          if (!this.modalPostComments[i].loadRepliesMore) {
            this.modalPostComments[i].replies.push(res.data);
          }
          this.modalPostComments[i].replyCount++;
          this.modalPostComments[i].replyInput = '';
          this.modalPostComments[i].replyErrors = [];
          this.posts[this.modalPostIndex].commentCount++;
          this.modalPostCommentCount++;
          this.modalPostComments[i].replyFormOpened = false;
          this.replyProcessing = false;
        }).catch((error) => {
          this.modalPostComments[i].replyErrors = error.response.data.errors;
          this.replyProcessing = false;
          // console.log(this.modalPostComments[i].replyErrors)
        });
    },
    // コメントへの返信の削除
    deleteReply(comId, repId) {
      if (window.confirm('削除しますか？')) {
        axios.post('/api/reply/delete/' + repId)
          .then(() => {
            const comment = this.modalPostComments.find((v) => v.id === comId);
            const repliesId = comment.replies.map((obj) => {
              return obj.id;
            })
            let replyIndex = repliesId.indexOf(repId); // リプライのインデックスを取得
            comment.replies.splice(replyIndex, 1);
            comment.replyCount--;
            this.modalPostCommentCount--;
            this.posts[this.modalPostIndex].commentCount--;
          }).catch((error) => {
            console.log(error);
          });
      }
    },
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
    // モーダルのタグをクリックして新たなタグページへ遷移する際の処理
    // fromModalTagToTag() {
    //   const body = document.querySelector('body');
    //   const tagsPage = document.querySelector('.tags-page');
    //   body.classList.remove('bodyWhenOverlay');
    //   tagsPage.classList.remove('tags-page-when-overlay');
    //   tagsPage.style.top = null;
    //   this.scrollPosition = null;
    //   this.modalPostShow = false;
    // },
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

  mounted() {
    // 認証ユーザー情報、全ジャンルを取得
    this.getInitInfo(this.$route.params.word);
    // 投稿の無限スクロール
    window.onscroll = () => {
      // スクロール位置が一番下ならtrue
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
      if (bottomOfWindow && !this.modalPostShow && !this.modalPostEditShow && !this.deletePostModalOpened && !this.modalImageShow) {
        this.getPosts(this.$route.params.word);
      }
    }
  },

  computed: {
    // テキストエリアの高さを計算
    editStyles() {
      return {
        'height': this.editHeight,
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
    // コメント入力欄の高さの変化を監視
    commentInput() {
      this.changeCommentHeight();
    },
    // 編集も同様に
    'editPost.body': function(val) {
      if (this.modalPostEditShow) {
        this.changeEditHeight();
      }
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
    window.addEventListener('resize', this.bigImageResize);
    window.addEventListener('resize', this.changeEditHeight);
    window.addEventListener('resize', this.changeCommentHeight);
  },

  destroyed() {
    window.removeEventListener('resize', this.handleResize);
    window.removeEventListener('resize', this.bigImageResize);
    window.removeEventListener('resize', this.changeEditHeight);
    window.removeEventListener('resize', this.changeCommentHeight);
  },

  beforeRouteLeave (to, from, next) {
    if (this.modalPostShow || this.modalImageShow || this.deletePostModalOpened || this.modalPostEditShow) {
      this.keepScrollWhenClose();
      this.modalPostShow = false;
      this.modalImageShow = false;
      this.deletePostModalOpened = false;
      this.modalPostEditShow = false;
    }
    this.loadMorePosts = false;
    if (!this.editProcessing && !this.deleteProssesing && !this.commentProcessing && !this.replyProcessing) {
      next();
    }
  },

  beforeRouteUpdate (to, from, next) {
    if (this.modalPostShow || this.modalImageShow || this.deletePostModalOpened || this.modalPostEditShow) {
      this.keepScrollWhenClose();
      this.modalPostShow = false;
      this.modalImageShow = false;
      this.closeModalDonmai();
      this.deletePostModalOpened = false;
      this.editPostModalClose(); ///////////////!!!!!!!!!!!!!
    }
    this.resetPaginate();
    this.getPosts(to.params.word);
    if (!this.editProcessing && !this.deleteProssesing && !this.commentProcessing && !this.replyProcessing) {
      next();
    }
  },
}
</script>