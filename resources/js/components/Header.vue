<template>

  <div class="header">

    <div class="header-1">
      <div class="logo">
        <img :src="'image/logo.png'" alt="ロゴ画像">
      </div>
      <div class="search search-1">
        <input class="search-form" type="text" placeholder="検索">
        <img :src="'image/search.png'">
      </div>
      <div class="menus">
        <div class="menu menu-set">
          <div>MENU</div>
          <ul>
            <li>ホーム</li>
            <li>トレンド</li>
            <li>みんなでグチ</li>
            <li>プロフィール</li>
          </ul>
        </div>
        <div class="menu each-menu">
          ホーム
        </div>
        <div class="menu each-menu">
          トレンド
        </div>
        <div class="menu each-menu">
          みんなでグチ
        </div>
        <div class="icon each-menu">
          <img :src="'image/unko.jpg'" @click="toggle" v-click-outside="close" :class="{'clicked':opened}">
          <ul v-if="opened">
            <li>プロフィール</li>
            <li>ログアウト</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="header-2">
      <div class="search">
        <input class="search-form" type="text" placeholder="検索">
        <img :src="'image/search.png'">
      </div>
    </div>

  </div>

</template>

<script>
import ClickOutside from 'vue-click-outside'

export default {
  data: function () {
    return {
      opened: false,
    }
  },
  methods: {
    toggle() {
      this.opened = !this.opened;
    },
    close() {
      this.opened = false;
    }
  },
  directives: {
    ClickOutside
  }
}
</script>