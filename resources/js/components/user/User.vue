<template>

  <div class="user-page">

    <div class="user-container">
      
      <!-- ユーザー情報エリア -->
      <div class="user-info">

        <!-- ユーザー情報トップ -->
        <div class="user-info-top">

          <!-- ユーザーアイコン -->
          <div class="userpage-icon" v-if="user">
            <img :src="user.icon">
            <!-- <img v-if="user.icon" :src="user.icon">
            <img v-if="!user.icon" :src="'../../image/user.png'"> -->
          </div>

          <div class="user-info-top-right">

            <!-- ユーザー名 -->
            <div class="userinfo-name-area">
              <div v-if="user">{{ user.name }}</div>
            </div>

            <!-- フォローボタン・プロフィール編集ボタン -->
            <div class="userinfo-btn-area" v-if="authUser && user">

              <div v-if="Number($route.params.id) !== authUser.id && !user.followed" @click="followUser" class="userinfo-follow-btn">
                フォローする
              </div>

              <div v-if="Number($route.params.id) !== authUser.id && user.followed" @click="unfollowUser" class="userinfo-follow-btn-followed">
                フォロー中
              </div>

              <div v-if="Number($route.params.id) == authUser.id" @click="toUserEdit" class="userinfo-follow-btn profile-edit-btn">
                プロフィール編集
              </div>

            </div>

            <!-- <div class="userinfo-btn-area">
            </div> -->

            <!-- 投稿数、フォロワー数、フォロー数表示エリア（画面幅730px以上） -->
            <div class="userinfo-data-area" v-if="user">

              <div class="each-data1">投稿<span class="num">{{ postCount }}</span>件</div>
              <div @click="opneModalFollower" class="each-data2">フォロワー<span class="num">{{ user.follower }}</span>人</div>
              <div @click="opneModalFollowing" class="each-data3">フォロー中<span class="num">{{ user.follow }}</span>人</div>

            </div>

          </div>

        </div>

        <!-- ユーザーの自己紹介エリア -->
        <div class="user-info-pr">

          <div class="pr-text" v-if="user">{{ user.pr }}</div>

        </div>

        <!-- 投稿数、フォロワー数、フォロー数表示エリア（画面幅730px未満） -->
        <div class="user-info-nums" v-if="user">

            <div class="userinfo-nums-each">
              <div>投稿</div>
              <div class="userinfo-number">{{ postCount }}</div>
              <div>件</div>
            </div>

            <div @click="opneModalFollower" class="userinfo-nums-each clickable">
              <div>フォロワー</div>
              <div class="userinfo-number">{{ user.follower }}</div>
              <div>人</div>
            </div>

            <div @click="opneModalFollowing" class="userinfo-nums-each clickable">
              <div>フォロー中</div>
              <div class="userinfo-number">{{ user.follow }}</div>
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


    <!-- 「フォロー・フォロワー」モーダル -->
    <div v-if="modalFollowShow" @click.self="modalFollowClose" class="overlay-post">

      <div class="overlay-donmai-content">

        <div v-if="modalFollowerShow" class="overlay-donmai-title">
          フォロワー
        </div>
        <div v-if="modalFollowingShow" class="overlay-donmai-title">
          フォロー中
        </div>

        <!-- 「フォロワー」を選択した場合 -->
        <div v-if="modalFollowerShow" class="donmai-user-box">

          <div v-for="(follower, index) in modalFollowers" :key="follower.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: follower.id }}">
                  <img :src="follower.icon">
                </router-link>
              </div>

              <!-- ユーザー名 -->
              <div class="overlay-donmai-user-name">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: follower.id }}">
                  {{ follower.name }}
                </router-link>
              </div>

            </div>

            <div class="overlay-donmai-right">

              <!-- フォローボタン -->
              <div v-if="!follower.followed && follower.id !== authUser.id" @click="followFollower(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="follower.followed && follower.id !== authUser.id" @click="unFollowFollower(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

          <div class="no-follower" v-if="!user.follower">
            フォロワーはいません。
          </div>

        </div>

        <!-- 「フォロー中」を選択したの場合 -->
        <div v-if="modalFollowingShow" class="donmai-user-box">

          <div v-for="(followingUser, index) in modalFollows" :key="followingUser.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <router-link :to="{ name: 'user', params: { id: followingUser.id }}">
                  <img :src="followingUser.icon">
                </router-link>
              </div>

              <!-- ユーザー名 -->
              <div class="overlay-donmai-user-name">
                <router-link :to="{ name: 'user', params: { id: followingUser.id }}">
                  {{ followingUser.name }}
                </router-link>
              </div>

            </div>

            <div class="overlay-donmai-right">

              <!-- フォローボタン -->
              <div v-if="!followingUser.followed && followingUser.id !== authUser.id" @click="followFollowing(index)" class="overlay-donmai-follow">
                フォローする
              </div>
              <div v-if="followingUser.followed && followingUser.id !== authUser.id" @click="unFollowFollowing(index)" class="overlay-donmai-followed">
                フォロー中
              </div>

            </div>

          </div>

          <div class="no-follower" v-if="!user.follow">
            誰もフォローしていません。
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
      // 認証ユーザー情報
      authUser: null,
      // ユーザー情報
      user: null,
      // フォロー・フォロワー・投稿数の情報
      // followed: false,
      postCount: 8000,
      // follower: 3450,
      // follow: 2155,
      // 前のページのルート
      prevRoute: null,
      // フォロー・フォロワーのモーダル
      modalFollowShow: false,
      modalFollowingShow: false,
      modalFollowerShow: false,
      modalFollows: [
        // id, icon, name, followed,
      ],
      modalFollowers: [
        // id, icon, name, followed,
      ],
    }
  },

  methods: {
    // axiosで認証ユーザー情報を取得
    getAuthUserInfo() {
      axios.get('/api/user')
      .then((res) => {
        // console.log(res.data);
        this.authUser = res.data;
      }).catch(() => {
        return;
      });
    },
    // axiosで現在のページのユーザー情報を取得
    getUserInfo(paramId) {
      axios.get('/api/user/' + paramId)
      .then((res) => {
        // console.log(res.data);
        this.user = res.data;
        if (this.user.icon === null) {
          this.user.icon = '../../image/user.png';
        }
        // this.user.pr = str_replace('\n', '<br>');
      }).catch(() => {
        return;
      });
    },
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
    opneModalFollowing() {
      axios.get('/api/following/' + this.user.id)
        .then((res) => {
          this.modalFollows = res.data;
          // console.log(this.modalFollows);
          for (let i = 0; i < this.modalFollows.length; i++) {
            if (!this.modalFollows[i].icon) {
              this.modalFollows[i].icon = '../../image/user.png';
            }
            // this.modalFollows[i].followed = true;
          }
          this.keepScrollWhenOpen();
          this.modalFollowShow = true;
          this.modalFollowingShow = true;
        }).catch(() => {
          return;
        });
    },
    opneModalFollower() {
      axios.get('/api/follower/' + this.user.id)
        .then((res) => {
          this.modalFollowers = res.data;
          // console.log(this.modalFollowers);
          for (let i = 0; i < this.modalFollowers.length; i++) {
            if (!this.modalFollowers[i].icon) {
              this.modalFollowers[i].icon = '../../image/user.png';
            }
          }
          this.keepScrollWhenOpen();
          this.modalFollowShow = true;
          this.modalFollowerShow = true;
        }).catch(() => {
          return;
        });
    },
    modalFollowClose() {
      this.keepScrollWhenClose();
      this.modalFollowShow = false;
      this.modalFollowingShow = false;
      this.modalFollowerShow = false;
    },
     // プロフィール編集ページへ遷移
    toUserEdit() {
      this.$router.push({ name: 'user.edit'});
    },
    // フォローとアンフォローの処理（でかボタン）
    followUser() {
      axios.post('/api/follow', this.user)
        .then(() => {
          this.user.followed = true;
          this.user.follower++;
        }).catch(() => {
          return;
        });
    },
    unfollowUser() {
      axios.post('/api/unfollow', this.user)
        .then(() => {
          this.user.followed = false;
          this.user.follower--;
        }).catch(() => {
          return;
        });
    },
    // フォロー・フォロワーのフォローとアンフォロー
    // フォロワーモーダルでフォロー
    followFollower(i) {
      axios.post('/api/follow', this.modalFollowers[i])
        .then(() => {
          this.modalFollowers[i].followed = true;
          if (this.authUser.id == this.user.id) {
            this.user.follow++;
          }
        }).catch(() => {
          return;
        });
    },
    // フォロワーモーダルでアンフォロー
    unFollowFollower(i) {
      axios.post('/api/unfollow', this.modalFollowers[i])
        .then(() => {
          this.modalFollowers[i].followed = false;
          if (this.authUser.id == this.user.id) {
            this.user.follow--;
          }
        }).catch(() => {
          return;
        });
    },
    // フォローモーダルでフォロー
    followFollowing(i) {
      axios.post('/api/follow', this.modalFollows[i])
        .then(() => {
          this.modalFollows[i].followed = true;
          if (this.authUser.id == this.user.id) {
            this.user.follow++;
          }
        }).catch(() => {
          return;
        });
    },
    // フォローモーダルでアンフォロー
    unFollowFollowing(i) {
      axios.post('/api/unfollow', this.modalFollows[i])
        .then(() => {
          this.modalFollows[i].followed = false;
          if (this.authUser.id == this.user.id) {
            this.user.follow--;
          }
        }).catch(() => {
          return;
        });
    },
  },

  mounted() {
    // 認証ユーザー情報取得
    this.getAuthUserInfo();
    // このページで表示するユーザーの情報取得
    this.getUserInfo(this.$route.params.id);
  },

  beforeRouteLeave (to, from, next) {
    if (this.modalFollowShow) {
      this.modalFollowClose();
    }
    next();
  },

  beforeRouteUpdate (to, from, next) {
    if (this.modalFollowShow) {
      this.modalFollowClose();
    }
    this.getAuthUserInfo();
    this.getUserInfo(to.params.id);
    next();
  },
}
</script>