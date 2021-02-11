<template>

  <div class="home">

    <!-- 投稿一覧 -->
    <router-view v-if="isRouteShow"></router-view>

    <!-- サイドメニュー -->
    <div class="sidemenu">
      <div class="sidemenu-box">
        <!-- サイドメニュータイトル -->
        <div class="genre-head">ジャンル</div>
        <!-- ジャンル一覧 -->
        <div class="genre-list" v-if="genres">
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
  provide() {
    return {
      reload: this.reload,
    }
  },
  data: function () {
    return {
      isRouteShow: true,
      genres: [],
    }
  },

  methods: {
    // 全ジャンルを取得
    getGenres() {
      axios.get('/api/genre')
        .then((res) => {
          this.genres = res.data;
        }).catch(() => {
          return;
        });
    },
    // 子コンポーネント再描画
    async reload() {
      this.isRouteShow = false;
      await this.$nextTick();
      this.isRouteShow = true;
    },
  },

  mounted() {
    this.getGenres();
  },
}
</script>