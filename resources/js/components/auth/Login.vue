<template>

  <div class="register-page">

    <div class="register">

      <div class="register-box">

        <div class="register-top">
          ログイン
        </div>

        <!-- 認証失敗メッセージ -->
        <div v-if="errors.msg" class="register-each">

          <div class="register-error">
            {{ errors.msg[0] }}
          </div>

        </div>

        <form @submit.prevent="login">
        
          <!-- メールアドレス -->
          <div class="register-each">

            <label for="email">メールアドレス</label>

            <input type="text" name="email" v-model="form.email">

            <div v-if="errors.email" class="register-error">
              {{ errors.email[0] }}
            </div>

          </div>

          <!-- パスワード -->
          <div class="register-each">

            <label for="password">パスワード</label>

            <input type="password" name="password" v-model="form.password">

            <div v-if="errors.password" class="register-error">
              {{ errors.password[0] }}
            </div>

          </div>

          <!-- ログインボタン -->
          <div class="register-bottom">

            <!-- <div class="register-btn" @click="login">
              ログイン
            </div> -->
            <input type="submit" value="ログイン" class="register-btn">

          </div>

        </form>

      </div>

    </div>

  </div>
  
</template>


<script>
export default {
  data () {
    return {
      form: {},
      errors: [],
    }
  },
  methods: {
    login() {
      axios.get('/sanctum/csrf-cookie')
        .then((res) => {
          axios.post('/api/login', this.form)
            .then(() => {
              localStorage.setItem('auth', 'true');
              this.$router.push({ name: 'home' });
            }).catch((error) => {
              this.errors = error.response.data.errors;
            });
        });
    }
  },
}
</script>