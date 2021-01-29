<template>
  
  <div class="thread-page">

    <div class="thread-page-container">

      <div class="thread-back-btn" @click="$router.go(-1)">戻る</div>

      <div class="thread-scroll-btn" @click="scrollToEnd">
        <img :src="'../image/shita.png'">
      </div>

      <div class="thread-content">

        <!-- スレッドタイトル -->
        <div class="thread-title-area">

          <div class="thread-title-area-icon">
            <img :src="'../image/unko.jpg'">
          </div>

          <div class="thread-title-area-right">

            <div class="thread-title-area-title">
              <div class="thread-title">うんこ食べたくなってこない？うんこ食べたくなってこない？</div>
            </div>

            <div class="thread-title-area-infos">

              <div class="thread-title-area-comment">
                <div class="comment-count">
                    <img :src="'../image/comment.png'">
                    {{ comments.length }}
                </div>
              </div>

              <div class="thread-title-area-time">
                <div class="time">2021/01/28 18:28</div>
              </div>

            </div>

          </div>

        </div>

        <!-- コメント一覧 -->
        <div v-for="comment in comments" :key="comment.id" class="thread-comment-area"  :class="{ 'self-comment': comment.self }">

          <div class="thread-comment-area-top">
            <div class="id" :class="{ 'self-id': comment.self }">{{ comment.id }}:</div>
            <div class="time">{{ comment.created_at }}</div>
          </div>

          <div class="thread-comment-area-comment">
            {{ comment.content }}
          </div>

          <div class="thread-comment-area-image">
            <div v-for="(image, index) in comment.images" :key="index">
              <img :src="image">
            </div>
          </div>

          <div class="thread-comment-area-bottom">

            <div class="thread-comment-area-good">
              <div class="good">
                {{ comment.good_count }}
                <img v-if="!comment.gooded" @click="goodToggle(comment.id)" :src="'../image/good.png'">
                <img v-if="comment.gooded" @click="goodToggle(comment.id)" :src="'../image/gooded.png'">
              </div>
            </div>

          </div>

        </div>

        <!-- コメント投稿エリア -->
        <div class="thread-post-area">

          <div class="title">投稿する</div>

          <div class="input">
            <textarea></textarea>
          </div>

          <div class="thread-image-preview">
            <div v-for="(url, index) in urls" :key="index" class="thread-each-preview">
              <img class="thread-batsu-icon" :src="'../image/batsu.png'" @click="deletePreview(url, index)">
              <img class="thread-each-image" :src="url">
            </div>
          </div>

          <div class="image-upload">
            <label for="comment-photo">
              <img :src="'../image/image.png'">
              <input type="file" @change="uploadFile" ref="threadPreview" id="comment-photo" accept="image/*" style="display:none;" multiple>
            </label>
          </div>

          <div class="check-btns">
            <div class="tokumei" :class="{ 'selected': postStyle == 'anonymous' }" @click="anonymous">
              <img :src="'../image/check.png'">
              匿名で投稿
            </div>
            <div class="show-name" :class="{ 'selected': postStyle == 'showName' }" @click="showName">
              <img :src="'../image/check.png'">
              名前とアイコンを表示
            </div>
          </div>

          <div class="post">
            <div class="post-btn">投稿</div>
          </div>

        </div>

      </div>

    </div>

  </div>

</template>


<script>
export default {
  data() {
    return {
      postStyle: 'anonymous',
      comments: [
        {
          id: 1,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 2,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: true,
        },
        {
          id: 3,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [
            '../image/img1.jpg',
            '../image/img3.jpg',
          ],
          good_count: 115,
          gooded: true,
          self: false,
        },
        {
          id: 4,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 5,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 6,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: true,
        },
        {
          id: 7,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 8,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
        },
        {
          id: 9,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: true,
        },
        {
          id: 10,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [
            '../image/img5.jpg',
          ],
          good_count: 115,
          gooded: false,
          self: true,
        },
        {
          id: 11,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 12,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: true,
        },
        {
          id: 13,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
        {
          id: 14,
          created_at: '2021/01/28 18:28',
          content: 'コロナになってから異様にうんこ食べたくなるんだけどわかる人おる？',
          images: [],
          good_count: 115,
          gooded: false,
          self: false,
        },
      ],
      urls: [],
      files: [],
    }
  },
  methods: {
    // 匿名か名前表示か選択
    anonymous() {
      this.postStyle = 'anonymous';
    },
    showName() {
      this.postStyle = 'showName';
    },
    // グッドの切り替え
    goodToggle(commentId) {
      const comment = this.comments.find((v) => v.id == commentId);
      comment.gooded = !comment.gooded;
      if (comment.gooded == false) {
        comment.good_count--;
      } else {
        comment.good_count++;
      }
    },
    // 画像アップロードとプレビュー
    uploadFile() {
      const file = this.$refs.threadPreview.files;
      if (this.files.length + file.length > 3) {
        window.alert('画像は１投稿につき３枚までです！');
      } else {
        this.files.push(...file);
        for (let i = 0; i < file.length; i++) {
          this.urls.push(URL.createObjectURL(file[i]));
        }
        this.$refs.threadPreview.value = '';
        console.log(this.files);
        console.log(this.urls);
      }
    },
    // 画像プレビューの削除
    deletePreview(url, index) {
      this.urls.splice(index, 1);
      this.files.splice(index, 1);
      URL.revokeObjectURL(url);
      console.log(this.files);
      console.log(this.urls);
    },
    // ページ最下部へスクロール
    scrollToEnd() {
      const element = document.documentElement;
      const bottom = element.scrollHeight - element.clientHeight - 319;
      window.scroll(0, bottom);
    },
  },
  mounted() {
    this.scrollToEnd();
  },
}
</script>