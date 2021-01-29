<template>

  <!-- ジャンル別投稿一覧 -->
  <div class="main-home">

    <!-- 各投稿 -->
    <div v-for="(post, index) in posts" :key="post.id" class="posts">

      <!-- ロゴ画像 -->
      <div class="logo-area">
        <router-link :to="{ name: 'user' }">
          <img :src="'../image/unko.jpg'">
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
          <span v-for="(tag, index) in post.tags" :key="index">
            {{ tag }}
          </span>
        </div>

        <!-- 画像表示 -->
        <div v-if="post.images.length > 0" class="post-image-view">
          <div v-for="(image, index) in post.images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.images.length == 1, 'two-image-pre': post.images.length == 2, 'three-image-pre': post.images.length == 3, 'four-image-pre': post.images.length == 4, 'yokonaga': post.images.length == 3 && index == 0 }">
            <div class="each-image-box">
              <div class="each-image" :style="{ backgroundImage: 'url(' + image + ')' }" :class="{ 'one-each-image': post.images.length == 1, 'two-each-image': post.images.length == 2, 'three-each-image': post.images.length == 3, 'four-each-image': post.images.length == 4, 'yokonaga-image': post.images.length == 3 && index == 0 }"></div>
            </div>
          </div>
        </div>

        <!-- リアクションボタン -->
        <div class="post-btns">
          <!-- どんまいボタン -->
          <div class="post-donmai post-action-icon" @click="donmai(index)">
            <img v-if="!post.donmai" :src="'../image/donmai.png'" width="30px">
            <img v-if="post.donmai" :src="'../image/donmaied.png'" width="30px">
            {{ post.donmai_count }}
          </div>
          <!-- コメントボタン -->
          <div class="post-comment-icon post-action-icon">
            <img :src="'image/comment.png'">
            12
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
      // imageCount: 0,
      posts: [
        {
          id: 1,
          user: 'うんこマン',
          donmai: false,
          donmai_count: 15,
          images: [
            '../image/img1.jpg',
            '../image/img2.jpg',
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
          user: 'ちんこ野郎',
          donmai: false,
          donmai_count: 37,
          images: [
            '../image/img2.jpg',
            '../image/img3.jpg',
            '../image/img4.jpg',
          ],
        },
        // {
        //   id: 3,
        //   user: 'マザーファッカー',
        //   donmai: false,
        // },
      ]
    };
  },
  methods: {
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
  },
}
</script>