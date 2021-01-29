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
          こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。こんにちは。<br>
          こんにちは。こんにちは。<br>
          こんにちは。こんにちは。
        </div>

        <!-- タグ -->
        <div v-if="post.tags" class="post-tags">
          <a v-for="(tag, index) in post.tags" :key="index">
            {{ tag }}
          </a>
        </div>

        <!-- 画像たち -->
        <div v-if="post.images.length > 0" class="post-image-view">
          <div v-for="(image, index) in post.images" :key="index" class="each-preview" :class="{ 'one-image-pre': post.images.length == 1, 'two-image-pre': post.images.length == 2, 'three-image-pre': post.images.length == 3, 'four-image-pre': post.images.length == 4, 'yokonaga': post.images.length == 3 && index == 0 }">
            <div class="each-image-box">
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
          <div class="post-comment-icon post-action-icon">
            <img :src="'../image/comment.png'">
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
      text: '',
      value: '',
      height: '20px',
      urls: [],
      files: [],
      imageCount: 0,
      posts: [
        {
          id: 1,
          user: 'なおとくん',
          donmai: false,
          donmai_count: 43,
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
          donmai: false,
          donmai_count: 27,
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
          donmai: false,
          donmai_count: 65,
          images: [
            '../image/img1.jpg',
            '../image/img2.jpg',
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
    // テキストエリアの高さをフレキシブルに
    changeHeight() {
      this.height = this.$refs.area.scrollHeight + 'px';
      this.height = 18 + 'px';
      this.$nextTick(() => {
        this.height = this.$refs.area.scrollHeight + 'px';
      });
    },
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
  computed: {
    // テキストエリアの高さを計算
    styles() {
      return {
        'height': this.height,
      }
    }
  },
  watch: {
    // テキストエリアの高さの変化を監視
    text() {
      this.changeHeight();
      // console.log(this.$refs.area.scrollHeight);
    },
  },
  mounted() {
    // マウント時にもテキストエリアの高さをフレキシブルに
    this.changeHeight();
  }
}
</script>