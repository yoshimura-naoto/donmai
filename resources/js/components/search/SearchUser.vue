<template>
  
  <div>

    <!-- <div :to="{ name: 'user', params: { id: user.id }}" v-for="(user, index) in users" :key="index" class="search-user-box"> -->
    <div v-for="(user, index) in users" :key="index" @click.self="toUserPage(user.id)" class="search-user-box">

      <!-- 左側 -->
      <div @click="toUserPage(user.id)" class="search-user-left">

        <!-- アイコン -->
        <img :src="user.icon" class="search-user-icon">

      </div>

      <!-- 右側 -->
      <div @click.self="toUserPage(user.id)" class="search-user-right">

        <div @click.self="toUserPage(user.id)" class="search-user-right-top">

          <!-- 名前 -->
          <div @click.self="toUserPage(user.id)" class="search-user-name">
            {{ user.name }}
          </div>

          <!-- フォローボタン -->
          <div v-if="!user.followed" @click="toggleFollow(user)" class="search-user-follow-btn">
            フォローする
          </div>
          <div v-if="user.followed" @click="toggleFollow(user)" class="search-user-followed-btn">
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
      users: [
        {
          id: 3,
          icon: '../../image/unko.jpg',
          name: 'チンカス',
          followed: false,
          pr: 'うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。うんちが大好き。'
        },
        {
          id: 6,
          icon: '../../image/img1.jpg',
          name: 'いぬぬわん',
          followed: false,
          pr: 'わんわん。わんわん。わんわん。わんわん。わんわん。わんわん。わんわん。わんわん。わんわん。わんわん。'
        },
        {
          id: 9,
          icon: '../../image/img4.jpg',
          name: 'ふるでりか',
          followed: false,
          pr: 'にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！にぱー！'
        }
      ],
    }
  },
  methods: {
    // フォローボタンの処理
    toggleFollow(user) {
      user.followed = !user.followed;
    },
    toUserPage(user_id) {
      this.$router.push({ name: 'user', params: { id: user_id }});
    }
  },
}
</script>