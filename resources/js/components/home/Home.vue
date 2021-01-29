<template>

  <div class="home">

    <!-- 投稿一覧 -->
    <router-view></router-view>

    <!-- サイドメニュー -->
    <div class="sidemenu">
      <div class="sidemenu-box">
        <!-- サイドメニュータイトル -->
        <div class="genre-head">ジャンル</div>
        <!-- ジャンル一覧 -->
        <div class="genre-list">
          <ul>
            <li v-for="(genre, index) in genres" :key="index">
              <router-link :to="{ name: genre.route }" :class="{ 'selected': $route.path == $router.resolve({ name: genre.route }).href }">
                {{ genre.name }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </div>

</template>


<script>
export default {
  data: function () {
    return {
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
  }
}
</script>