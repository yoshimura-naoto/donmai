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
            <li>
              <router-link :to="{ name: 'home' }" :class="{ 'selected': $route.path == '/' }">
                すべて
              </router-link>
            </li>
            <li v-for="(genre, index) in genres" :key="index">
              <router-link :to="'/genre/' + genre.route" :class="{ 'selected': $route.path == '/genre/' + genre.route }">
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
          name: '仕事',
          route: 'jobs'
        },
        {
          name: '日常',
          route: 'life'
        },
        {
          name: '人間関係',
          route: 'relationships'
        },
        {
          name: 'どじ',
          route: 'dozi'
        },
        {
          name: '恥かいた',
          route: 'shame'
        },
        {
          name: '学校',
          route: 'school'
        },
        {
          name: '恋愛',
          route: 'love'
        },
        {
          name: '結婚生活',
          route: 'marriage'
        },
        {
          name: 'ゲーム',
          route: 'game'
        },
        {
          name: 'うんこうんこうんこ',
          route: 'unko'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
        {
          name: 'ちんちん',
          route: 'chinchin'
        },
      ],
    }
  }
}
</script>