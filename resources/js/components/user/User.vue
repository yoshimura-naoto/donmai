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

              <div class="each-data1">投稿<span class="num">{{ user.posts_count }}</span>件</div>
              <div @click="opneModalFollower" class="each-data2">フォロワー<span class="num">{{ user.followers_count }}</span>人</div>
              <div @click="opneModalFollowing" class="each-data3">フォロー中<span class="num">{{ user.follows_count }}</span>人</div>

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
              <div class="userinfo-number">{{ user.posts_count }}</div>
              <div>件</div>
            </div>

            <div @click="opneModalFollower" class="userinfo-nums-each clickable">
              <div>フォロワー</div>
              <div class="userinfo-number">{{ user.followers_count }}</div>
              <div>人</div>
            </div>

            <div @click="opneModalFollowing" class="userinfo-nums-each clickable">
              <div>フォロー中</div>
              <div class="userinfo-number">{{ user.follows_count }}</div>
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
        <div v-if="modalFollowerShow" class="donmai-user-box" @scroll="followersPaginate">

          <div v-for="(follower, index) in modalFollowers" :key="follower.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <!-- <router-link :to="{ name: 'user', params: { id: user.id }}" @click.native="fromModalFollwToUser"> -->
                <router-link :to="{ name: 'user', params: { id: follower.id }}">
                  <img v-if="follower.icon" :src="follower.icon">
                  <img v-if="!follower.icon" :src="'../../image/user.png'">
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

          <div class="no-follower" v-if="user.followers_count === 0">
            フォロワーはいません。
          </div>

        </div>

        <!-- 「フォロー中」を選択したの場合 -->
        <div v-if="modalFollowingShow" class="donmai-user-box" @scroll="followsPaginate">

          <div v-for="(followingUser, index) in modalFollows" :key="followingUser.id" class="donmai-user-list">

            <div class="overlay-donmai-left">

              <!-- アイコン -->
              <div class="overlay-donmai-user-icon">
                <router-link :to="{ name: 'user', params: { id: followingUser.id }}">
                  <img v-if="followingUser.icon" :src="followingUser.icon">
                  <img v-if="!followingUser.icon" :src="'../../image/user.png'">
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

          <div class="no-follower" v-if="user.follows_count === 0">
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
      user: {
        // id
        // icon
        // name
        // pr
        // posts_count
        // follows_count
        // followers_count
        // followed
      },
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
      // フォローモーダルの無限スクロール用
      followsLoading: false,
      followsLoadMore: true,
      followsPage: 1,
      isLastFollowsPage: false,
      // フォロワーモーダルの無限スクロール用
      followersLoading: false,
      followersLoadMore: true,
      followersPage: 1,
      isLastFollowersPage: false,
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
    // フォロー中のユーザーの取得
    getFollows() {
      if (this.isLastFollowsPage) return;
      if (this.followsLoading) return;
      this.followsLoading = true;
      axios.get('/api/following/' + this.user.id + '?page=' + this.followsPage)
        .then((res) => {
          // console.log(res.data);
          const follows = res.data.data.map((obj) => {
            return obj.followed_user;
          });
          this.modalFollows.push(...follows);
          // this.modalFollows.push(...res.data.data);
          this.followsLoading = false;
          if (this.followsPage === res.data.last_page) {
            this.isLastFollowsPage = true;
          }
          this.followsPage++;
        }).catch((error) => {
          console.log(error);
          this.followsLoading = false;
        });
    },
    // フォロワーの取得
    getFollowers() {
      if (this.isLastFollowersPage) return;
      if (this.followersLoading) return;
      this.followersLoading = true;
      axios.get('/api/follower/' + this.user.id + '?page=' + this.followersPage)
        .then((res) => {
          // console.log(res.data);
          const followers = res.data.data.map((obj) => {
            return obj.user;
          });
          this.modalFollowers.push(...followers);
          this.followersLoading = false;
          if (this.followersPage === res.data.last_page) {
            this.isLastFollowersPage = true;
          }
          this.followersPage++;
        }).catch((error) => {
          console.log(error);
          this.followersLoading = false;
        });
    },
    // フォローモーダルを開く
    opneModalFollowing() {
      this.keepScrollWhenOpen();
      this.modalFollowShow = true;
      this.modalFollowingShow = true;
      this.getFollows();
    },
    // フォロワーモーダルを開く
    opneModalFollower() {
      this.keepScrollWhenOpen();
      this.modalFollowShow = true;
      this.modalFollowerShow = true;
      this.getFollowers();
    },
    // フォロー・フォロワーモーダルを閉じる
    modalFollowClose() {
      this.keepScrollWhenClose();
      this.modalFollowShow = false;
      this.modalFollowingShow = false;
      this.modalFollowerShow = false;
      this.modalFollows = [];
      this.modalFollowers = [];
      // フォロー無限スクロールの初期化
      this.followsLoading = false;
      this.followsLoadMore = true;
      this.followsPage = 1;
      this.isLastFollowsPage = false;
      // フォロワー無限スクロールの初期化
      this.followersLoading = false;
      this.followersLoadMore = true;
      this.followersPage = 1;
      this.isLastFollowersPage = false;
    },
    // フォローモーダルの無限スクロール
    followsPaginate() {
      const followingModal = document.querySelector('.donmai-user-box');
      let bottomOfModal = followingModal.scrollTop + followingModal.clientHeight >= followingModal.scrollHeight - 1;
      if (bottomOfModal) {
        this.getFollows();
      }
    },
    // フォロワーモーダルの無限スクロール
    followersPaginate() {
      const followerModal = document.querySelector('.donmai-user-box');
      let bottomOfModal = followerModal.scrollTop + followerModal.clientHeight >= followerModal.scrollHeight - 1;
      if (bottomOfModal) {
        this.getFollowers();
      }
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
          this.user.followers_count++;
        }).catch(() => {
          return;
        });
    },
    unfollowUser() {
      axios.post('/api/unfollow', this.user)
        .then(() => {
          this.user.followed = false;
          this.user.followers_count--;
        }).catch(() => {
          return;
        });
    },
    // フォロワーモーダルでフォロー
    followFollower(i) {
      axios.post('/api/follow', this.modalFollowers[i])
        .then(() => {
          this.modalFollowers[i].followed = true;
          if (this.authUser.id == this.user.id) {
            this.user.follows_count++;
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
            this.user.follows_count--;
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
            this.user.follows_count++;
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
            this.user.follows_count--;
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