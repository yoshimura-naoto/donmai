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
      <div :class="{ 'no-search': !searchOpened }" class="search search-1" v-if="$route.path !== '/register' && $route.path !== '/login'">
        <input v-model="keyword" @keydown.enter="search" v-click-outside="searchClose" class="search-form" id="search" type="text" placeholder="検索">
      </div>

      <!-- メニューたち -->
      <div v-if="!searchOpened" class="menus">

        <!-- 検索アイコン（ボタン） -->
        <div class="search-icon" @click="searchOpen" v-if="$route.path !== '/register' && $route.path !== '/login'">
          <img :src="'/image/search2.png'">
        </div>

        <!-- ジャンルメニュー -->
        <div :class="{'genre-menu-display-1': $route.path == $router.resolve({ name: 'home' }).href || $route.path.substr(0, 7) == '/genre/' }" class="header-genre-menu-2">

          <div class="menu-set-btn-genre" @click="genreMenuToggle" v-click-outside="genreMenuClose">
            ジャンル
          </div>

          <div v-if="genreOpened" class="pulldown-genre-menu">
            <ul>
              <li>
                <router-link :to="{ name: 'home' }" :class="{ 'selected': $route.path == '/' }">
                  すべて
                </router-link>
              </li>
              <li v-for="(genre, index) in genres" :key="index">
                <router-link :to="{ name: 'home.genre', params: { name: genre.route }}" :class="{ 'selected': $route.path == '/genre/' + genre.route }">
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

            <li v-if="$route.path !== '/register' && $route.path !== '/login'">
              <router-link :to="{name: 'home'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == $router.resolve({ name: 'home' }).href }">
                  ホーム
                </div>
              </router-link>
            </li>

            <li v-if="$route.path !== '/register' && $route.path !== '/login'">
              <router-link :to="{name: 'trend'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == $router.resolve({ name: 'trend' }).href }">
                  トレンド
                </div>
              </router-link>
            </li>

            <li v-if="$route.path !== '/register' && $route.path !== '/login'">
              <router-link :to="{name: 'hot'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == $router.resolve({ name: 'hot' }).href }">
                  話題の投稿
                </div>
              </router-link>
            </li>

            <li v-if="$route.path !== '/register' && $route.path !== '/login'">
              <router-link :to="{name: 'guchi.all'}">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path.substr(0, 6) == '/guchi' }">
                  みんなでグチ
                </div>
              </router-link>
            </li>

            <li v-if="$route.path !== '/register' && $route.path !== '/login' && authUser">
              <router-link :to="{name: 'user', params: { id: authUser.id }}">
              <!-- <router-link :to="{name: 'user', params: { id: 100 }}"> -->
                <div class="menu-set-btn">プロフィール</div>
              </router-link>
            </li>

            <li v-if="$route.path !== '/register' && $route.path !== '/login'">
              <a @click="logout">
                <div class="menu-set-btn">ログアウト</div>
              </a>
            </li>

            <li v-if="$route.path == '/register' || $route.path == '/login'">
              <router-link :to="{ name: 'auth.login' }">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == '/login' }">
                  ログイン
                </div>
              </router-link>
            </li>

            <li v-if="$route.path == '/register' || $route.path == '/login'">
              <router-link :to="{ name: 'auth.register' }">
                <div class="menu-set-btn" :class="{ 'current-2': $route.path == '/register' }">
                  登録
                </div>
              </router-link>
            </li>

          </ul>
        </div>


        <!-- 画面幅670px以上の時のヘッダーメニュー -->
        <div class="menu each-menu" v-if="$route.path !== '/register' && $route.path !== '/login'">
          <router-link :to="{name: 'home'}" :class="{ 'current': $route.path == $router.resolve({ name: 'home' }).href || $route.path.substr(0, 7) == '/genre/' }">
            <div class="menu-set-btn">ホーム</div>
          </router-link>
        </div>

        <div v-if="$route.path !== '/register' && $route.path !== '/login'" :class="{'genre-menu-display-2': $route.path == $router.resolve({ name: 'home' }).href || $route.path.substr(0, 7) == '/genre/' }" class="header-genre-menu-wide">
          <div class="menu-set-btn-genre-wide" @click="genreMenuToggleWide" v-click-outside="genreMenuWideClose">
            ジャンル
          </div>
          <div v-if="genreMenuWideOpened" class="pulldown-genre-menu">
            <ul>
              <li>
                <router-link :to="{ name: 'home' }" :class="{ 'selected': $route.path == '/' }">
                  すべて
                </router-link>
              </li>
              <li v-for="(genre, index) in genres" :key="index">
                <router-link :to="{ name: 'home.genre', params: { name: genre.route }}" :class="{ 'selected': $route.path == '/genre/' + genre.route }">
                  {{ genre.name }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>

        <div v-if="$route.path !== '/register' && $route.path !== '/login'" class="menu each-menu">
          <router-link :to="{name: 'trend'}" :class="{ 'current': $route.path == $router.resolve({ name: 'trend' }).href }">
            <div class="menu-set-btn">トレンド</div>
          </router-link>
        </div>

        <div v-if="$route.path !== '/register' && $route.path !== '/login'" class="menu each-menu">
          <router-link :to="{name: 'hot'}" :class="{ 'current': $route.path == $router.resolve({ name: 'hot' }).href }">
            <div class="menu-set-btn">話題の投稿</div>
          </router-link>
        </div>

        <div v-if="$route.path !== '/register' && $route.path !== '/login'" class="menu each-menu">
          <router-link :to="{name: 'guchi.all'}" :class="{ 'current': $route.path.substr(0, 6) == '/guchi' }">
            <div class="menu-set-btn">みんなでグチ</div>
          </router-link>
        </div>

        <div v-if="$route.path !== '/register' && $route.path !== '/login' && authUser" class="icon each-menu">

          <img :src="authUser.icon" @click="toggle" v-click-outside="close" :class="{'clicked':opened}">

          <ul v-if="opened">

            <li>
              <router-link :to="{name: 'user', params: { id: authUser.id }}">
                プロフィール
              </router-link>
            </li>

            <li>
              <a @click="logout">
                ログアウト
              </a>
            </li>

          </ul>
        </div>

        <div v-if="$route.path == '/register' || $route.path == '/login'" class="menu each-menu">
          <router-link :to="{name: 'auth.login'}" :class="{ 'current': $route.path == '/login' }">
            <div class="menu-set-btn">ログイン</div>
          </router-link>
        </div>

        <div v-if="$route.path == '/register' || $route.path == '/login'" class="menu each-menu login-menu">
          <router-link :to="{name: 'auth.register'}" :class="{ 'current': $route.path == '/register' }">
            <div class="menu-set-btn">登録</div>
          </router-link>
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
      // 認証ユーザー情報
      authUser: null,
      // メニューの開閉
      opened: false,
      genreOpened: false,
      genreMenuWideOpened: false,
      searchOpened: false,
      canSearchClose: false,
      keyword: '',
      genres: [],
    }
  },

  methods: {
    // プロフィールメニューの開閉
    toggle() {
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
    // 検索の処理
    search(e) {
      if (e.keyCode !== 13) return;
      this.$router.push({ name: 'search.post.new', params: { word: this.keyword }})
        .catch(() => {
          return;
        });
    },
    // ログアウト
    logout() {
      axios.post('/api/logout')
        .then(() => {
          localStorage.removeItem('auth');
          this.$router.push({ name: 'auth.login' });
        });
    }
  },

  mounted() {
    // 認証ユーザー情報取得
    if (this.$route.path !== '/login' && this.$route.path !== '/register') {
      axios.get('/api/headinit')
        .then((res) => {
          // console.log('ちんこ');
          // console.log(res.data);
          this.authUser = res.data.authUser;
          this.genres = res.data.genres;
          if (!this.authUser.icon) {
            this.authUser.icon = '/image/user.png';
          }
          // console.log(this.genres);
        }).catch(() => {
          // console.log('うんこ');
        });
    }
  },

  directives: {
    // 要素の外側クリックを認識するライブラリ
    ClickOutside
  },
}
</script>