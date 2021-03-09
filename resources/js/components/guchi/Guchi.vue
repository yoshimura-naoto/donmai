<template>

  <div class="guchi" ref="scrollend">

    <div class="guchi-container">

      <div class="guchi-main">

        <!-- 一覧トップのボタンたち -->
        <div class="guchi-top-btns">

          <!-- 戻るボタン -->
          <div v-if="$route.path.substr(0, 12) == '/guchi/room/'" class="thread-back-btn" @click="$router.go(-1)">戻る</div>

          <!-- ジャンルボタン、新規作成ボタン -->
          <div class="guchi-top-btns-1">

            <!-- <div @click="genreMenuOpen" v-click-outside="genreMenuClose" class="guchi-genre-btn">ジャンル</div> -->
            <div class="guchi-genre-btn">
              
              <div>ジャンル</div>

              <!-- <ul :class="{'guchi-genre-list-open': genreOpen}" class="guchi-genre-list"> -->
              <ul class="guchi-genre-list">
                <li>
                  <router-link :to="{ name: 'guchi.all' }" :class="{ 'selected': $route.path == '/guchi/all' || $route.path == '/guchi/all/trend' }">
                    すべて
                  </router-link>
                </li>
                <li v-for="(genre, index) in genres" :key="index">
                  <router-link :to="'/guchi/' + genre.route" :class="{ 'selected': $route.path == '/guchi/' + genre.route || $route.path == '/guchi/' + genre.route + '/trend' }">
                    {{ genre.name }}
                  </router-link>
                </li>
              </ul>

            </div>

            <!-- スレッド新規作成ボタン -->
            <div v-if="!createOpened && $route.path.substr(0, 12) !== '/guchi/room/'" class="guchi-new" @click="openCreateForm">
              部屋を作る
            </div>

          </div>

          <!-- 新着順と人気順で並べ替えボタン -->
          <div v-if="$route.path.substr(0, 12) !== '/guchi/room/'" class="guchi-change-order">
            <router-link v-if="!$route.path.includes('/trend')" :to="$route.path" :class="{ 'selected': !$route.path.includes('/trend') }">
              新着
            </router-link>
            <router-link v-if="$route.path.includes('/trend')" :to="$route.path.slice(0, -6)" :class="{ 'selected': !$route.path.includes('/trend') }">
              新着
            </router-link>
            <!-- <router-link v-if="!$route.path.includes('/trend')" :to="{ path: $route.path + '/trend' }" :class="{ 'selected': $route.path.includes('/trend') }"> -->
            <router-link v-if="!$route.path.includes('/trend')" :to="$route.path + '/trend'" :class="{ 'selected': $route.path.includes('/trend') }">
              人気
            </router-link>
            <router-link v-if="$route.path.includes('/trend')" :to="$route.path" :class="{ 'selected': $route.path.includes('/trend') }">
              人気
            </router-link>
          </div>

        </div>

        <!-- 部屋を作成 -->
        <div v-if="createOpened && $route.path.substr(0, 12) !== '/guchi/room/'" class="guchi-new-form">
          
          <div class="top">

            <div class="top-title">
              部屋を作成
            </div>

            <div class="guchi-new-form-close" @click="closeCreateForm">
              とじる
            </div>

          </div>

          <div class="bottom">

            <div class="image-select">

              <div class="title">
                画像を選択
              </div>

              <!-- アイコン画像のプレビュー -->
              <div class="guchi-image-preview-area">
                <div v-if="!form.icon" class="no-image">NO IMAGE</div>
                <img class="guchi-new-batsu-icon" :src="'../image/batsu.png'" v-if="form.icon && !$route.path.includes('/trend')" @click="deletePreview">
                <img class="guchi-new-batsu-icon" :src="'../../image/batsu.png'" v-if="form.icon && $route.path.includes('/trend')" @click="deletePreview">
                <div class="guchi-image-preview" :style="{ backgroundImage: 'url(' + url + ')' }"></div>
              </div>

              <label>
                <img v-if="!$route.path.includes('/trend')" class="image-icon" :src="'../image/image.png'">
                <img v-if="$route.path.includes('/trend')" class="image-icon" :src="'../../image/image.png'">
                <input class="file-upload" type="file" ref="preview" @change="uploadFile" accept="image/*">
              </label>

              <div v-if="message" class="user-edit-error">
                {{ message }}
              </div>

            </div>

            <div class="title-input">

              <div class="title-input-title">トークテーマを入力</div>

              <input type="text" class="guchi-title-input" v-model="form.title">

              <div v-if="errors.title" class="user-edit-error">
                {{ errors.title[0] }}
              </div>

              <!-- ジャンル選択エリア -->
              <div class="select-genre-guchi">
                <select name="genre" v-model="form.genre">
                  <option value="" selected>ジャンルを選択してください</option>
                  <option v-for="(genre, index) in genres" :key="index" :value="genre.route">{{ genre.name }}</option>
                </select>
              </div>

              <div v-if="errors.genre" class="user-edit-error">
                {{ errors.genre[0] }}
              </div>

              <!-- テスト用 -->
              <!-- <div class="create">
                <div class="create-btn" @click="check">チェック</div>
              </div> -->

              <!-- 作成ボタン -->
              <div class="create">
                <div class="create-btn" @click="submit">作成</div>
              </div>

            </div>

          </div>

        </div>

        <router-view :key="reloadKey"></router-view>

      </div>

      <!-- 画面幅が1000px以上の時のサイドバーのジャンルメニュー -->
      <div class="guchi-genre-wide">

        <div class="guchi-genre-head">ジャンル</div>

        <ul>
          <li>
            <router-link :to="{ name: 'guchi.all' }" :class="{ 'selected': $route.path == '/guchi/all' || $route.path == '/guchi/all/trend' }">
              すべて
            </router-link>
          </li>
          <li v-for="(genre, index) in genres" :key="index">
            <router-link :to="'/guchi/' + genre.route" :class="{ 'selected': $route.path == '/guchi/' + genre.route || $route.path == '/guchi/' + genre.route + '/trend' }">
              {{ genre.name }}
            </router-link>
          </li>
        </ul>

      </div>

    </div>

  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      // 部屋の新規作成
      form: {
        icon: null,
        title: '',
        genre: '',
      },
      // file: null,
      message: '',
      errors: [],
      url: '',
      createOpened: false,
      genreOpen: false,
      genres: [],
      // 子コンポーネントの再読み込み用のキー
      reloadKey: 0,
    }
  },
  methods: {
    // チェック
    check() {
      console.log(this.form);
    },
    // ジャンル一覧を取得
    getGenres() {
      axios.get('/api/guchiroom/genres')
        .then((res) => {
          this.genres = res.data;
        }).catch(() => {
          return;
        });
    },
    // ジャンルメニューの開閉
    genreMenuOpen() {
      this.genreOpen = true;
    },
    genreMenuClose() {
      this.genreOpen = false;
    },
    // 部屋作成フォームの表示と非表示
    openCreateForm() {
      this.createOpened = true;
    },
    closeCreateForm() {
      this.createOpened = false;
    },
    // 画像アップロードのプレビュー
    uploadFile() {
      this.form.icon = this.$refs.preview.files[0];
      if (!this.form.icon.type.match('image.*')) {
        this.message = '画像ファイルを選択してください';
        this.form.icon = null;
        return;
      }
      this.url = URL.createObjectURL(this.form.icon);
      this.$refs.preview.value = '';
      this.message = '';
      // console.log(this.form.icon);
      // console.log(this.url);
    },
    // 画像プレビューの削除
    deletePreview() {
      URL.revokeObjectURL(this.url);
      this.url = '';
      this.form.icon = '';
      // console.log(this.url);
      // console.log(this.form.icon);
    },
    // グチ部屋の投稿（作成）
    submit() {
      let data = new FormData();
      if (this.form.icon) {
        data.append('icon', this.form.icon);
      }
      data.append('title', this.form.title);
      data.append('genre', this.form.genre);
      axios.post('/api/guchiroom/create', data)
        .then(() => {
          this.form.icon = null;
          this.form.title = '';
          this.form.genre = '';
          this.url = null;
          this.errors = [];
          this.message = '';
          this.closeCreateForm();
          if (this.$route.path === '/guchi/all') {
            this.reloadKey++;
          } else {
            this.$router.push({ name: 'guchi.all' });
          }
        }).catch((error) => {
          this.errors = error.response.data.errors;
        });
    },
  },

  mounted() {
    this.getGenres();
  },

  beforeRouteUpdate (to, from, next) {
    if (this.createOpened) {
      this.closeCreateForm();
    }
    next();
  },

  directives: {
    ClickOutside
  },
}
</script>