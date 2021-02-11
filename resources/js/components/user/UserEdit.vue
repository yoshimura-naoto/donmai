<template>
  
  <div class="user-edit-page">

    <div class="user-edit">

      <div class="user-edit-box" v-if="form">

        <!-- トップ -->
        <div class="user-edit-top">
          プロフィール編集
        </div>

        <div class="user-edit-name-icon">

          <!-- アイコン -->
          <div class="user-edit-icon-name">

            <div class="user-edit-icon">

              <!-- アイコンのプレビュー削除ボタン -->
              <img v-if="url" class="user-edit-batsu-icon" :src="'../../../image/batsu.png'" @click="deletePreview">
              
              <!-- 元々のアイコン -->
              <img class="user-edit-icon-img" v-if="form.icon && !form.iconImage && !url" :src="form.icon">
              <!-- アイコンがない場合の仮画像 -->
              <img class="user-edit-icon-img" v-if="!form.icon && !form.iconImage && !url" :src="'../../../image/user.png'">
              <!-- アップロードしたアイコンのプレビュー -->
              <div v-if="url" class="preview" :style="{ backgroundImage: 'url(' + url + ')' }"></div>

            </div>

            <!-- アイコン画像アップロードボタン -->
            <div class="user-edit-icon-up">
              <label>
                <img class="image-icon" :src="'../../../image/image.png'">
                <input class="file-upload" type="file" ref="preview" @change="uploadIcon" accept="image/*">
              </label>
            </div>

            <!-- アイコン画像のエラーメッセージ表示 -->
            <div v-if="message" class="user-edit-error">
              {{ message }}
            </div>
            <!-- <div v-if="errors.iconImage" class="user-edit-error">
              {{ errors.iconImage }}
            </div> -->

          </div>

          <!-- 現在のユーザー名表示 -->
          <div class="user-edit-current-user-name">
            {{ form.name }}
          </div>

        </div>

        <!-- ユーザー名変更欄 -->
        <div class="user-edit-user-name">

          <label for="name">
            ユーザー名
          </label>

          <input type="text" id="name" v-model="form.name">

          <div v-if="errors.name" class="user-edit-error">
            {{ errors.name[0] }}
          </div>

        </div>

        <!-- 自己紹介文 -->
        <div class="user-edit-pr">

          <label for="pr">
            自己紹介
          </label>

          <textarea v-model="form.pr" id="pr"></textarea>

          <div v-if="errors.pr" class="user-edit-error">
            {{ errors.pr[0] }}
          </div>

        </div>

        <!-- パスワード変更フォームへのリンク -->
        <div class="user-edit-to-password">
          <router-link :to="{ name: 'user.password' }">
            パスワードの変更
          </router-link>
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
        name: null,
        pr: null,
        iconImage: null,
      },
      errors: [],
      // プレビュー用データ
      url: '',
      // file: '',
      // ここでだけ使うデータ
      message: '',
    }
  },

  methods: {
    // 画像の検証とプレビュー表示
    uploadIcon() {
      this.form.iconImage = this.$refs.preview.files[0];
      if (!this.form.iconImage.type.match('image.*')) {
        this.message = '画像ファイルを選択してください';
        this.form.iconImage = null;
        return;
      }
      this.url = URL.createObjectURL(this.form.iconImage);
      this.$refs.preview.value = '';
      this.message = '';
      console.log(this.form.iconImage);
      console.log(this.url);
    },
    // 画像のプレビュー削除
    deletePreview() {
      URL.revokeObjectURL(this.url);
      this.form.iconImage = null;
      this.form.icon = null;
      this.url = '';
      console.log(this.form.iconImage);
      console.log(this.url);
    },
    // 変更
    submit() {
      let data = new FormData();
      data.append('id', this.form.id);
      data.append('name', this.form.name);
      data.append('pr', this.form.pr);
      data.append('iconImage', this.form.iconImage);
      axios.post('/api/user/edit', data)
        .then(() => {
          this.$router.push({ name: 'user', params: { id: this.form.id }});
        }).catch((error) => {
          this.errors = error.response.data.errors;
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
        console.log(res.data);
        this.form = res.data;
      });
  },
}
</script>