<template>

  <div class="user-page">

    <div class="user-container">
      
      <!-- ユーザー情報エリア -->
      <div class="user-info">

        <!-- ユーザー情報トップ -->
        <div class="user-info-top">

          <!-- ユーザーアイコン -->
          <div class="userpage-icon">
            <img :src="icon">
          </div>

          <div class="user-info-top-right">

            <!-- ユーザー名 -->
            <div class="userinfo-name-area">
              <div>{{ name }}</div>
            </div>
            <!-- フォローボタン -->
            <div class="userinfo-btn-area">
              <div v-if="!followed" @click="toggleFollow" class="userinfo-follow-btn">フォローする</div>
              <div v-if="followed" @click="toggleFollow" class="userinfo-follow-btn-followed">フォロー中</div>
            </div>

            <!-- 投稿数、フォロワー数、フォロー数表示エリア（画面幅730px以上） -->
            <div class="userinfo-data-area">

              <div class="each-data1">投稿<span class="num">{{ postCount }}</span>件</div>
              <div @click="opneModalFollow(1)" class="each-data2">フォロワー<span class="num">{{ follower }}</span>人</div>
              <div @click="opneModalFollow(2)" class="each-data3">フォロー中<span class="num">{{ follow }}</span>人</div>

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
              <div class="userinfo-number">{{ postCount }}</div>
              <div>件</div>
            </div>

            <div @click="opneModalFollow(1)" class="userinfo-nums-each clickable">
              <div>フォロワー</div>
              <div class="userinfo-number">{{ follower }}</div>
              <div>人</div>
            </div>

            <div @click="opneModalFollow(2)" class="userinfo-nums-each clickable">
              <div>フォロー中</div>
              <div class="userinfo-number">{{ follow }}</div>
              <div>人</div>
            </div>

        </div>

        <!-- メニューボタンエリア -->
        <div class="user-info-menubtn">

          <router-link :to="{ name: 'user', params: { id: $route.params.id }}" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user', params: { id: $route.params.id }}).href }">
            投稿
          </router-link>

          <router-link :to="{ name: 'user.donmai', params: { id: $route.params.id } }" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user.donmai', params: { id: $route.params.id }}).href }">
            どんまい
          </router-link>

          <router-link :to="{ name: 'user.guchi', params: { id: $route.params.id } }" class="each-btn" :class="{ 'selected': $route.path == $router.resolve({ name: 'user.guchi', params: { id: $route.params.id }}).href }">
            みんなでグチ
          </router-link>

        </div>

      </div>

      <router-view></router-view>

    </div>


    <!-- 「フォロワー」モーダル -->
    <div v-if="modalFollowShow" @click.self="closeModalFollow" class="overlay-post">

      <div class="overlay-donmai-content">

        <div v-if="!isFollowModal" class="overlay-donmai-title">
          フォロワー
        </div>
        <div v-if="isFollowModal" class="overlay-donmai-title">
          フォロー中
        </div>

        <!-- 「フォロワー」を選択した場合 -->
        <div v-if="!isFollowModal" class="donmai-user-box">

          <div v-for="(user, index) in modalFollowers" :key="user.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  <img :src="user.icon">
                </router-link>
              </div>

              <!-- ユーザー名 -->
              <div class="overlay-donmai-user-name">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  {{ user.name }}
                </router-link>
              </div>

            </div>

            <div class="overlay-donmai-right">

              <!-- フォローボタン -->
              <div v-if="!user.followed" @click="followToggle(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="user.followed" @click="followToggle(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

        </div>

        <!-- 「フォロー中」を選択したの場合 -->
        <div v-if="isFollowModal" class="donmai-user-box">

          <div v-for="(user, index) in modalFollows" :key="user.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  <img :src="user.icon">
                </router-link>
              </div>

              <!-- ユーザー名 -->
              <div class="overlay-donmai-user-name">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: user.id }}">
                  {{ user.name }}
                </router-link>
              </div>

            </div>

            <div class="overlay-donmai-right">

              <!-- フォローボタン -->
              <div v-if="!user.followed" @click="followToggle(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="user.followed" @click="followToggle(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

        </div>

      </div>

    </div>
    
  </div>

</template>


<script>
export default {
  data() {
    return {
      icon: '../../image/unko.jpg',
      name: 'うんこマン',
      followed: false,
      postCount: 8000,
      follower: 3450,
      follow: 2155,
      // 前のページのルート
      prevRoute: null,
      // フォロー・フォロワーのモーダル
      isFollowModal: false,
      modalFollowShow: false,
      modalFollowers: [
        {
          id: 111,
          icon: '../../image/img1.jpg',
          name: 'あああああ',
          followed: false,
        },
        {
          id: 222,
          icon: '../../image/img2.jpg',
          name: 'いいいいい',
          followed: false,
        },
        {
          id: 333,
          icon: '../../image/img3.jpg',
          name: 'ううううううう',
          followed: false,
        },
        {
          id: 444,
          icon: '../../image/img4.jpg',
          name: 'えええええええ',
          followed: false,
        },
        {
          id: 555,
          icon: '../../image/img5.jpg',
          name: 'おおおおおお',
          followed: false,
        },
        {
          id: 666,
          icon: '../../image/img6.jpg',
          name: 'かかかかかかか',
          followed: false,
        },
        {
          id: 777,
          icon: '../../image/img7.jpg',
          name: '聴き聴ききききき',
          followed: false,
        },
        {
          id: 888,
          icon: '../../image/img8.jpg',
          name: 'クククククくくく',
          followed: false,
        },
      ],
      modalFollows: [
        {
          id: 123,
          icon: '../../image/img7.jpg',
          name: 'マイク',
          followed: true,
        },
        {
          id: 234,
          icon: '../../image/img8.jpg',
          name: 'マイク',
          followed: true,
        },
        {
          id: 345,
          icon: '../../image/img2.jpg',
          name: 'マイク',
          followed: true,
        },
        {
          id: 456,
          icon: '../../image/img1.jpg',
          name: 'マイク',
          followed: true,
        },
      ],
    }
  },
  methods: {
    // モーダルウィンドウ開閉時に背景のスクロール位置を維持
    keepScrollWhenOpen() {
      const body = document.querySelector('body');
      const userPage = document.querySelector('.user-page');
      this.scrollPosition = window.pageYOffset;
      body.classList.add('bodyWhenOverlay');
      userPage.classList.add('user-page-when-overlay');
      userPage.style.top = -this.scrollPosition + 'px';
    },
    keepScrollWhenClose() {
      const body = document.querySelector('body');
      const userPage = document.querySelector('.user-page');
      body.classList.remove('bodyWhenOverlay');
      userPage.classList.remove('user-page-when-overlay');
      userPage.style.top = null;
      window.scroll(0, this.scrollPosition);
      this.scrollPosition = null;
    },
    // フォロー・フォロワーのモーダルウィンドウの開閉
    opneModalFollow(n) {
      this.keepScrollWhenOpen();
      if (n == 1) {
        this.isFollowModal = false;
      } else {
        this.isFollowModal = true;
      }
      this.modalFollowShow = true;
    },
    closeModalFollow() {
      this.keepScrollWhenClose();
      this.modalFollowShow = false;
      console.log('うんこ');
    },
    // フォローとアンフォローの処理
    toggleFollow() {
      this.followed = !this.followed;
      if (this.followed) {
        this.follower++;
      } else {
        this.follower--;
      }
    },
    // フォロー・フォロワーのフォローとアンフォロー
    followToggle(i) {
      if (this.isFollowModal) {
        this.modalFollows[i].followed = !this.modalFollows[i].followed;
      } else {
        this.modalFollowers[i].followed = !this.modalFollowers[i].followed;
      }
    },
    // フォロー・フォロワーモーダルのユーザーをクリックした際の処理
    // fromModalFollwToUser() {
    //   const body = document.querySelector('body');
    //   const userPage = document.querySelector('.user-page');
    //   body.classList.remove('bodyWhenOverlay');
    //   userPage.classList.remove('user-page-when-overlay');
    //   userPage.style.top = null;
    //   this.scrollPosition = null;
    //   this.modalFollowShow = false;
    // }
  },
  beforeRouteLeave (to, from, next) {
    if (this.modalFollowShow) {
      this.keepScrollWhenClose();
      this.modalFollowShow = false;
    }
    next();
  },
  beforeRouteUpdate (to, from, next) {
    if (this.modalFollowShow) {
      this.keepScrollWhenClose();
      this.modalFollowShow = false;
    }
    next();
  },
}
</script>