<template>

  <div class="guchi" ref="scrollend">

    <div class="guchi-container">

      <div class="guchi-main">

        <!-- 一覧トップのボタンたち -->
        <div v-if="$route.path !== $router.resolve({ name: 'guchi.detail' }).href" class="guchi-top-btns">

          <!-- ジャンルボタン、新規作成ボタン -->
          <div class="guchi-top-btns-1">

            <div @click="genreMenuOpen" v-click-outside="genreMenuClose" class="guchi-genre-btn">ジャンル</div>

            <ul :class="{'guchi-genre-list-open': genreOpen}" class="guchi-genre-list">
              <li v-for="(genre, index) in genres" :key="index">
                <router-link :to="{ name: genre.route[0] }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route[0] }).href || $route.path == $router.resolve({ name: genre.route[1] }).href }">
                  {{ genre.name }}
                </router-link>
              </li>
            </ul>

            <!-- スレッド新規作成ボタン -->
            <div v-if="!createOpened" class="guchi-new" @click="openCreateForm">
              部屋を作る
            </div>
            <!-- <div v-if="createOpened" class="guchi-new" @click="closeCreateForm">
              閉じる
            </div> -->

          </div>

          <!-- 新着順と人気順で並べ替えボタン -->
          <div class="guchi-change-order">
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
        <div v-if="createOpened && $route.path !== $router.resolve({ name: 'guchi.detail' }).href" class="guchi-new-form">
          
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

              <div class="guchi-image-preview-area">
                <div v-if="!file" class="no-image">NO IMAGE</div>
                <img class="guchi-new-batsu-icon" :src="'../image/batsu.png'" v-if="file && !$route.path.includes('/trend')" @click="deletePreview">
                <img class="guchi-new-batsu-icon" :src="'../../image/batsu.png'" v-if="file && $route.path.includes('/trend')" @click="deletePreview">
                <div class="guchi-image-preview" :style="{ backgroundImage: 'url(' + url + ')' }"></div>
              </div>

              <label>
                <img v-if="!$route.path.includes('/trend')" class="image-icon" :src="'../image/image.png'">
                <img v-if="$route.path.includes('/trend')" class="image-icon" :src="'../../image/image.png'">
                <input class="file-upload" type="file" ref="preview" @change="uploadFile" accept="image/*">
              </label>

            </div>

            <div class="title-input">

              <div class="title-input-title">トークテーマを入力</div>

              <input type="text" class="guchi-title-input">

              <div class="create">
                <div class="create-btn">作成</div>
              </div>

            </div>

          </div>

        </div>

        <router-view></router-view>

      </div>

      <!-- 画面幅が1000px以上の時のサイドバーのジャンルメニュー -->
      <div class="guchi-genre-wide">

        <div class="guchi-genre-head">ジャンル</div>

        <ul>
          <li v-for="(genre, index) in genres" :key="index">
            <router-link :to="{ name: genre.route[0] }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route[0] }).href || $route.path == $router.resolve({ name: genre.route[1] }).href }">
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
      file: null,
      url: null,
      createOpened: false,
      genreOpen: false,
      genres: [
        {
          name: 'すべて',
          route: [
            'guchi.all',
            'guchi.all.trend',
          ]
        },
        {
          name: '仕事',
          route: [
            'guchi.genre',
            'guchi.genre.trend',
            ]
        },
        {
          name: '日常',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: '人間関係',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'どじ',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: '恥かいた',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: '学校',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: '恋愛',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: '結婚生活',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ゲーム',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'うんこうんこうんこ',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
        {
          name: 'ちんちん',
          route: [
            'guchi.aaa',
            'guchi.aaa',
            ],
        },
      ],
    }
  },
  methods: {
    // ジャンルメーニューの開閉
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
      this.file = this.$refs.preview.files[0];
      this.url = URL.createObjectURL(this.file);
      this.$refs.preview.value = '';
      console.log(this.file);
      console.log(this.url);
    },
    // 画像プレビューの削除
    deletePreview() {
      URL.revokeObjectURL(this.url);
      this.url = '';
      this.file = '';
      console.log(this.file);
      console.log(this.url);
    },
  },
  directives: {
    ClickOutside
  },
}
</script>