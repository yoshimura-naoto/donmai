<template>

  <div class="user-page">

    <div class="user-container">
      
      <!-- ユーザー情報エリア -->
      <div class="user-info">

        <!-- ユーザー情報トップ -->
        <div class="user-info-top">

          <!-- ユーザーアイコン -->
          <div class="userpage-icon">
            <img :src="'../image/unko.jpg'">
          </div>

          <div class="user-info-top-right">

            <!-- ユーザー名 -->
            <div class="userinfo-name-area">
              <div>うんこマン</div>
            </div>
            <!-- フォローボタン -->
            <div class="userinfo-btn-area">
              <div v-if="!followed" @click="toggleFollow" class="userinfo-follow-btn">フォローする</div>
              <div v-if="followed" @click="toggleFollow" class="userinfo-follow-btn-followed">フォロー中</div>
            </div>

            <!-- 投稿数、フォロワー数、フォロー数表示エリア（画面幅730px以上） -->
            <div class="userinfo-data-area">

              <div class="each-data1">投稿<span class="num">8000万</span>件</div>
              <div class="each-data2">フォロワー<span class="num">8000万</span>人</div>
              <div class="each-data3">フォロー中<span class="num">8000万</span>人</div>

            </div>

          </div>

        </div>

        <!-- ユーザーの自己紹介エリア -->
        <div class="user-info-pr">

          <div class="pr-text">
            ただの死にたがりです。<br>
            人生リタイアしたい。<br>
            でも死ぬのって怖いよね。<br>
            じゃあどうしろってんだよ。
          </div>

        </div>

        <!-- 投稿数、フォロワー数、フォロー数表示エリア（画面幅730px未満） -->
        <div class="user-info-nums">

            <div class="userinfo-nums-each">
              <div>投稿</div>
              <div class="userinfo-number">8000万</div>
              <div>件</div>
            </div>

            <div class="userinfo-nums-each clickable">
              <div>フォロワー</div>
              <div class="userinfo-number">8000万</div>
              <div>人</div>
            </div>

            <div class="userinfo-nums-each clickable">
              <div>フォロー中</div>
              <div class="userinfo-number">8000万</div>
              <div>人</div>
            </div>

        </div>

        <!-- メニューボタンエリア -->
        <div class="user-info-menubtn">

          <router-link :to="{ name: 'user' }" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user' }).href }">
            投稿
          </router-link>

          <router-link :to="{ name: 'user.donmai' }" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user.donmai' }).href }">
            どんまい
          </router-link>

          <router-link :to="{ name: 'user.guchi' }" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user.guchi' }).href }">
            みんなでグチ
          </router-link>

        </div>

      </div>

      <router-view></router-view>

    </div>
    
  </div>

</template>


<script>
export default {
  data() {
    return {
      followed: false,
    }
  },
  methods: {
    toggleFollow() {
      this.followed = !this.followed;
    }
  }
}
</script>