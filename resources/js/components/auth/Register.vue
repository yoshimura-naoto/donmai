<template>

  <div class="register-page">

    <div class="register">

      <div class="register-box">

        <div class="register-top">
          ユーザー登録
        </div>

        <form @submit.prevent="submit">

          <!-- ユーザーネーム -->
          <div class="register-each">

            <label for="name">ユーザーネーム</label>

            <input type="text" name="name" v-model="form.name">

            <div v-if="errors.name" class="register-error">
              {{ errors.name[0] }}
            </div>

          </div>
          
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

          <!-- パスワード確認 -->
          <div class="register-each">

            <label for="password-confirm">パスワードの確認</label>

            <input type="password" name="password-confirm" v-model="form.password_confirmation">

          </div>

          <!-- 登録ボタン -->
          <div class="register-bottom">

            <!-- <div class="register-btn" @click="submit">
              登録
            </div> -->
            <input type="submit" value="登録" class="register-btn">

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
    submit() {
      axios.post('/api/register', this.form)
        .then(() => {
          this.$router.push({ name: 'auth.login' });
        }).catch((error) => {
          this.errors = error.response.data.errors;
        });
    }
  },
}
</script>