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
              <img class="user-edit-icon-img" v-if="form.icon && !url" :src="form.icon">
              <!-- <img class="user-edit-icon-img" v-if="!form.iconImage && !url && currentIcon" :src="currentIcon"> -->
              <!-- アイコンがない場合の仮画像 -->
              <img class="user-edit-icon-img" v-if="!form.icon && !url" :src="'../../../image/user.png'">
              <!-- <img class="user-edit-icon-img" v-if="!form.iconImage && !url && !currentIcon" :src="'../../../image/user.png'"> -->
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

          </div>

          <!-- 現在のユーザー名表示 -->
          <div class="user-edit-current-user-name">
            {{ form.name }}
          <div class="user-email">{{ email }}</div>
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
          <div v-if="tooLongPrMessage" class="user-edit-error">
            {{ tooLongPrMessage }}
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
      email: '',
      // 送信データ
      form: {
        id: null,
        icon: null,
        name: null,
        pr: '',
        // 新しいアイコン
        iconImage: null,
      },
      // currentIcon: '',
      errors: [],
      // プレビュー用データ
      url: '',
      // 画像アップロードエラーメッセージ
      message: '',
      // 自己紹介が長すぎ
      tooLongPrMessage: '',
      // 変更処理中
      editProcessing: false,
    }
  },

  methods: {
    // 画像の検証とプレビュー表示
    uploadIcon() {
      const file = this.$refs.preview.files[0];
      let image = new Image();
      image.src = URL.createObjectURL(file);
      image.addEventListener('load', () => {
        if (!file.type.match('image.*') || file.size > 1600000 || image.naturalWidth > 2500 || image.naturalHeight > 2500) {
          window.alert('ファイルサイズが1.6MBを超える画像、または画像でないファイルはアップロードできません。画像は縦・横それぞれ2500px以下のものを選択してください。');
          return;
        }
        this.form.icon = null;
        this.form.iconImage = file;
        this.url = URL.createObjectURL(this.form.iconImage);
        this.$refs.preview.value = '';
      });
    },
    // 画像のプレビュー削除
    deletePreview() {
      URL.revokeObjectURL(this.url);
      this.form.iconImage = null;
      this.form.icon = null;
      this.url = '';
      // console.log(this.form.iconImage);
      // console.log(this.form.icon);
      // console.log(this.url);
    },
    // 変更を送信
    submit() {
      if (this.editProcessing) return;
      this.editProcessing = true;
      // 文字数判定
      if (this.form.pr) {
        let textCount = this.form.pr.replace(/\n/g, '').length;
        // console.log(textCount);
        if (textCount > 100) {
          this.tooLongPrMessage = '100文字以内にしてください！（現在' + textCount + '文字）';
          this.errors = [];
          this.editProcessing = false;
          return;
        } else {
          this.tooLongPrMessage = '';
        }
      }
      let data = new FormData();
      data.append('id', this.form.id);
      data.append('name', this.form.name);
      if (this.form.pr) {
        data.append('pr', this.form.pr);
      }
      if (this.form.icon) {
        data.append('icon', this.form.icon);
      }
      if (this.form.iconImage) {
        data.append('iconImage', this.form.iconImage);
      }
      axios.post('/api/user/edit', data)
        .then((res) => {
          const userIcon = res.data.userIcon;
          if (userIcon) {
            document.getElementById('header-icon').src = userIcon;
          } else {
            document.getElementById('header-icon').src = '/image/user.png';
          }
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
        // console.log(res.data);
        this.email = res.data.email;
        this.$set(this.form, 'id', res.data.id);
        this.$set(this.form, 'icon', res.data.icon);
        this.$set(this.form, 'name', res.data.name);
        this.$set(this.form, 'pr', res.data.pr);
      });
  },

  beforeRouteLeave (to, from, next) {
    if (!this.editProcessing) {
      next();
    }
  },
}
</script>