<template>
  
  <div>

    <!-- <div :to="{ name: 'user', params: { id: user.id }}" v-for="(user, index) in users" :key="index" class="search-user-box"> -->
    <div v-for="(user, index) in users" :key="index" @click.self="toUserPage(user.id)" class="search-user-box">

      <!-- 左側 -->
      <div @click="toUserPage(user.id)" class="search-user-left">
        <!-- アイコン -->
        <img v-if="user.icon" :src="user.icon" class="search-user-icon">
        <img v-if="!user.icon" :src="'../../../image/user.png'" class="search-user-icon">
      </div>

      <!-- 右側 -->
      <div @click.self="toUserPage(user.id)" class="search-user-right">

        <div @click.self="toUserPage(user.id)" class="search-user-right-top">

          <!-- 名前 -->
          <div @click.self="toUserPage(user.id)" class="search-user-name">
            {{ user.name }}
          </div>

          <!-- フォローボタン -->
          <div v-if="!user.followed && user.id !== authUser.id" @click="follow(index)" class="search-user-follow-btn">
            フォローする
          </div>
          <div v-if="user.followed && user.id !== authUser.id" @click="unFollow(index)" class="search-user-followed-btn">
            フォロー中
          </div>

        </div>

        <!-- 自己紹介文 -->
        <div @click="toUserPage(user.id)" class="search-user-pr">
          {{ user.pr }}
        </div>

      </div>

    </div>

  </div>

</template>


<script>
export default {
  data () {
    return {
      authUser: null,
      users: [
        // {
        //   id: 3,
        //   icon: '../../image/unko.jpg',
        //   name: 'チンカス',
        //   followed: false,
        //   pr: 'うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。'
        // },
      ],
      // 無限スクロール用
      itemLoading: false,
      loadMore: true,
      page: 1,
      isLastPage: false,
    }
  },

  methods: {
    // 認証ユーザー情報、ユーザーを取得
    getInitInfo(word) {
      axios.get('/api/user')
        .then((res) => {
          // console.log(res.data);
          this.authUser = res.data;
          if (!this.authUser.icon) {
            this.authUser.icon = '../../../image/user.png';
          }
          this.getUsers(word);
        }).catch((error) => {
          console.log(error);
        });
    },
    // ユーザーの取得（無限スクロール）
    getUsers(word) {
      if (this.isLastPage) return;
      if (this.itemLoading) return;
      this.itemLoading = true;
      axios.get('/api/search/users/' + word + '?page=' + this.page)
        .then((res) => {
          // console.log(res.data);
          this.users.push(...res.data.data);
          this.itemLoading = false;
          if (this.page === res.data.last_page) {
            this.isLastPage = true;
          }
          this.page++;
          this.$nextTick(() => {
            let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
            if (bottomOfWindow && this.users.length < res.data.total) this.getUsers(word);
          });
        }).catch((error) => {
          console.log(error);
          this.itemLoading = false;
        });
    },
    // 無限スクロールのリセット
    resetPaginate() {
      this.users = [];
      this.itemLoading = false;
      this.loadMore = true;
      this.page = 1;
      this.isLastPage = false;
    },
    // フォローボタンの処理
    follow(i) {
      axios.post('/api/follow', this.users[i])
        .then(() => {
          this.users[i].followed = true;
        }).catch(() => {
          return;
        });
    },
    unFollow(i) {
      axios.post('/api/unfollow', this.users[i])
        .then(() => {
          this.users[i].followed = false;
        }).catch(() => {
          return;
        });
    },
    // ユーザーページへ遷移
    toUserPage(user_id) {
      this.$router.push({ name: 'user', params: { id: user_id }});
    }
  },

  mounted() {
    this.getInitInfo(this.$route.params.word);
    // 投稿の無限スクロール
    window.onscroll = () => {
      // スクロール位置が一番下ならtrue
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight >= document.documentElement.offsetHeight;
      if (bottomOfWindow) {
        this.getUsers(this.$route.params.word);
      }
    }
  },

  beforeRouteLeave (to, from, next) {
    this.isLastPage = true;
    next();
  },

  beforeRouteUpdate (to, from, next) {
    this.resetPaginate();
    this.getUsers(to.params.word);
    next();
  },
}
</script>