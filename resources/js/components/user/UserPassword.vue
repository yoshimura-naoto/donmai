<template>
  
  <div class="user-edit-page">

    <div class="user-edit">

      <div class="user-edit-box" v-if="form">

        <!-- トップ -->
        <div class="user-edit-top">
          パスワード変更
        </div>

        <!-- パスワード変更 -->
        <div class="user-edit-password">

          <div class="user-edit-password-each">

            <label for="current-password">
              現在のパスワード
            </label>

            <input type="password" id="current-password" v-model="form.currentPassword">

            <div v-if="errors">
              <div v-if="errors.msg" class="user-edit-error">
                {{ errors.msg[0] }}
              </div>
            </div>

            <div v-if="errors">
              <div v-if="errors.currentPassword" class="user-edit-error">
                {{ errors.currentPassword[0] }}
              </div>
            </div>

          </div>
          
          <div class="user-edit-password-each">

            <label for="new-password">
              新しいパスワード
            </label>

            <input type="password" id="new-password" v-model="form.newPassword">

            <div v-if="errors">
              <div v-if="errors.newPassword" class="user-edit-error">
                {{ errors.newPassword[0] }}
              </div>
            </div>

          </div>

          <div class="user-edit-password-each">

            <label for="new-password-confirm">
              新しいパスワードを確認
            </label>

            <input type="password" id="new-password-confirm" v-model="form.newPassword_confirmation">

          </div>

        </div>

        <!-- 変更ボタン -->
        <div class="user-edit-modify-btn" @click="submit">
          変更
        </div>

        <!-- テスト用 -->
        <!-- <div class="user-edit-modify-btn" @click="checkForm">
          チェック
        </div> -->

      </div>

    </div>

  </div>

</template>


<script>
export default {
  data () {
    return {
      // 送信データ
      form: {
        id: null,
        currentPassword: null,
        newPassword: null,
        newPassword_confirmation: null,
      },
      errors: [],
      editProcessing: false,
    }
  },

  methods: {
    // フォーム送信
    submit() {
      if (this.editProcessing) return;
      this.editProcessing = true;
      axios.post('/api/user/password', this.form)
        .then(() => {
          this.editProcessing = false;
          this.$router.push({ name: 'user', params: { id: this.form.id }});
        }).catch((error) => {
          this.errors = error.response.data.errors;
          this.editProcessing = false;
        });
    },
    // フォームの値をチェック
    checkForm() {
      console.log(this.form);
    }
  },

  mounted() {
    axios.get('/api/user')
      .then((res) => {
        this.form = res.data;
        console.log(this.form);
      })
  },

  beforeRouteLeave (to, from, next) {
    if (!this.editProcessing) {
      next();
    }
  },
}
</script>