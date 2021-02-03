<template>

  <div>
    <!-- 一覧トップのボタンたち -->
    <div class="guchi-top-btns">

      <!-- ジャンルボタン -->
      <div class="guchi-genre">

        <div @click="genreMenuOpen" v-click-outside="genreMenuClose" class="guchi-genre-btn">ジャンル</div>

        <ul :class="{'guchi-genre-list-open': genreOpen}" class="guchi-genre-list">
          <li v-for="(genre, index) in genres" :key="index">
            <router-link :to="{ name: genre.route[0] }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route[0] }).href || $route.path == $router.resolve({ name: genre.route[1] }).href }">
              {{ genre.name }}
            </router-link>
          </li>
        </ul>

      </div>

    </div>

    <h1>GuchiAAA</h1>

  </div>

</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
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
    }
  },
  directives: {
    ClickOutside
  },
}
</script>