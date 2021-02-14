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
    }
  },
  methods: {
    getInitInfo() {
      axios.get('/api/search/users/' + this.$route.params.word)
        .then((res) => {
          console.log(res.data);
          this.authUser = res.data.authUser;
          this.users = res.data.users;
        }).catch(() => {
          return;
        });
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
    this.getInitInfo();
  },


}
</script>