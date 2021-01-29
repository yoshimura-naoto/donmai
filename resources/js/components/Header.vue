<template>
  <div class="header">

    <div class="header-1">

      <!-- サイトロゴマーク -->
      <div class="logo">
        <router-link :to="{ name: 'home' }">
          <img :src="'/image/logo.png'" alt="ロゴ画像">
        </router-link>
      </div>

      <!-- 検索フォーム -->
      <div :class="{ 'no-search': !searchOpened }" class="search search-1">
        <input v-click-outside="searchClose" class="search-form" id="search" type="text" placeholder="検索">
      </div>

      <!-- メニューたち -->
      <div v-if="!searchOpened" class="menus">

        <!-- 検索アイコン（ボタン） -->
        <div class="search-icon" @click="searchOpen">
          <img :src="'/image/search2.png'">
        </div>

        <!-- ジャンルメニュー -->
        <div :class="{'genre-menu-display-1': $route.path == $router.resolve({ name: 'home' }).href || $route.path == $router.resolve({ name: 'home.genre' }).href}" class="header-genre-menu-2">
          <div class="menu-set-btn-genre" @click="genreMenuToggle" v-click-outside="genreMenuClose">
            ジャンル
          </div>
          <div v-if="genreOpened" class="pulldown-genre-menu">
            <ul>
              <li v-for="(genre, index) in genres" :key="index">
                <router-link :to="{ name: genre.route }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route }).href }">
                  {{ genre.name }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>

        <!-- 画面幅670px未満の時のヘッダーメニューをプルダウン化 -->
        <div class="menu menu-set">
          <div>MENU</div>
          <ul>
            <li>
              <router-link :to="{name: 'home'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == $router.resolve({ name: 'home' }).href }">
                  ホーム
                </div>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'home'}">
                <div class="menu-set-btn">
                  トレンド
                </div>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'hot'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == $router.resolve({ name: 'hot' }).href }">
                  話題の投稿
                </div>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'guchi.all'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path.startsWith('/guchi') }">
                  みんなでグチ
                </div>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'user'}">
                <div class="menu-set-btn">プロフィール</div>
              </router-link>
            </li>
          </ul>
        </div>

        <!-- 画面幅670px以上の時のヘッダーメニュー -->
        <div class="menu each-menu">
          <router-link :to="{name: 'home'}" :class="{ 'current': $route.path == $router.resolve({ name: 'home' }).href || $route.path == $router.resolve({ name: 'home.genre' }).href }">
            <div class="menu-set-btn">ホーム</div>
          </router-link>
        </div>
        <div :class="{'genre-menu-display-2': $route.path == $router.resolve({ name: 'home' }).href || $route.path == $router.resolve({ name: 'home.genre' }).href || $route.path == $router.resolve({ name: 'home.aaa' }).href}" class="header-genre-menu-wide">
          <div class="menu-set-btn-genre-wide" @click="genreMenuToggleWide" v-click-outside="genreMenuWideClose">
            ジャンル
          </div>
          <div v-if="genreMenuWideOpened" class="pulldown-genre-menu">
            <ul>
              <li v-for="(genre, index) in genres" :key="index">
                <router-link :to="{ name: genre.route }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route }).href }">
                  {{ genre.name }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>
        <div class="menu each-menu">
          <router-link :to="{name: 'home'}">
            <div class="menu-set-btn">トレンド</div>
          </router-link>
        </div>
        <div class="menu each-menu">
          <router-link :to="{name: 'hot'}" :class="{ 'current': $route.path == $router.resolve({ name: 'hot' }).href }">
            <div class="menu-set-btn">話題の投稿</div>
          </router-link>
        </div>
        <div class="menu each-menu">
          <router-link :to="{name: 'guchi.all'}" :class="{ 'current': $route.path.startsWith('/guchi') }">
            <div class="menu-set-btn">みんなでグチ</div>
          </router-link>
        </div>
        <div class="icon each-menu">
          <img :src="'/image/unko.jpg'" @click="toggle" v-click-outside="close" :class="{'clicked':opened}">
          <ul v-if="opened">
            <li>
              <router-link :to="{name: 'user'}">
                プロフィール
              </router-link>
            </li>
            <li>
              <a>
                ログアウト
              </a>
            </li>
          </ul>
        </div>
      </div>

    </div>

  </div>
</template>


<script>
import ClickOutside from 'vue-click-outside';

export default {
  data: function () {
    return {
      opened: false,
      genreOpened: false,
      genreMenuWideOpened: false,
      searchOpened: false,
      canSearchClose: false,
      genres: [
        {
          name: 'すべて',
          route: 'home'
        },
        {
          name: '仕事',
          route: 'home.genre'
        },
        {
          name: '日常',
          route: 'home.aaa'
        },
        {
          name: '人間関係',
          route: 'home.aaa'
        },
        {
          name: 'どじ',
          route: 'home.aaa'
        },
        {
          name: '恥かいた',
          route: 'home.aaa'
        },
        {
          name: '学校',
          route: 'home.aaa'
        },
        {
          name: '恋愛',
          route: 'home.aaa'
        },
        {
          name: '結婚生活',
          route: 'home.aaa'
        },
        {
          name: 'ゲーム',
          route: 'home.aaa'
        },
        {
          name: 'うんこうんこうんこ',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
        {
          name: 'ちんちん',
          route: 'home.aaa'
        },
      ],
    }
  },
  methods: {
    // プロフィールメニューの開閉
    toggle(opened) {
      this.opened = !this.opened;
    },
    close() {
      this.opened = false;
    },
    // ジャンルメニューの開閉
    genreMenuToggle() {
      this.genreOpened = !this.genreOpened;
    },
    genreMenuClose() {
      this.genreOpened = false;
    },
    // 画面幅が630px以上1000px未満の時のジャンルメニューの開閉
    genreMenuToggleWide() {
      this.genreMenuWideOpened = !this.genreMenuWideOpened;
    },
    genreMenuWideClose() {
      this.genreMenuWideOpened = false;
    },
    // 検索フォームの表示と非表示切り替え
    searchOpen() {
      if (!this.searchOpened && !this.canSearchClose) {
        this.searchOpened = true;
        document.getElementById('search').focus();
        // console.log(document.getElementById('search'));
      }
    },
    searchClose() {
      if (this.searchOpened && this.canSearchClose) {
        this.searchOpened = false;
        this.canSearchClose = false;
      } else if (this.searchOpened && !this.canSearchOpen) {
        this.searchOpened = true;
        this.canSearchClose = true;
      } else {
        return;
      }
    },
  },
  directives: {
    // 要素の外側クリックを認識するライブラリ
    ClickOutside
  }
}
</script>