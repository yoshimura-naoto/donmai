<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


use App\User;



class UserTest extends TestCase
{
    use RefreshDatabase;

    // ユーザー登録、ログイン
    public function testRegister()
    {
        // ユーザー登録
        // 成功パターン
        $form = [
            'name' => 'なおと',
            'email' => 'naoto@mail',
            'password' => 'naoto1234',
            'password_confirmation' => 'naoto1234'
        ];
        $this->postJson('/api/register', $form)->assertStatus(200);
        $data = [
            'name' => 'なおと',
            'email' => 'naoto@mail',
        ];
        $this->assertDatabaseHas('users', $data);
        // 同じメアドは登録できない
        $form2 = [
            'name' => 'けいご',
            'email' => 'naoto@mail',
            'password' => 'keigo1234',
            'password_confirmation' => 'keigo1234'
        ];
        $this->postJson('/api/register', $form2)->assertStatus(422);
        // パスワードは６文字以上
        $form3 = [
            'name' => '太郎',
            'email' => 'taro@mail',
            'password' => 'taro1',
            'password_confirmation' => 'taro1'
        ];
        $this->postJson('/api/register', $form3)->assertStatus(422);
        // パスワードは半角英数字、ハイフン、アンダーバーのみ可
        $form4 = [
            'name' => '次郎',
            'email' => 'jiro@mail',
            'password' => '       ',
            'password_confirmation' => '       '
        ];
        $this->postJson('/api/register', $form4)->assertStatus(422);
        $form4['password'] = '#$%123';
        $form4['password_confirmation'] = '#$%123';
        $this->postJson('/api/register', $form4)->assertStatus(422);

        // ログイン
        // 成功パターン
        $loginForm = [
            'email' => 'naoto@mail',
            'password' => 'naoto1234',
        ];
        $this->postJson('/api/login', $loginForm)->assertStatus(200);
        // パスワード不一致による認証失敗
        $loginForm2 = [
            'email' => 'naoto@mail',
            'password' => 'naoto12345',
        ];
        $this->postJson('/api/login', $loginForm2)->assertStatus(422);

        // ログアウト
        $this->postJson('/api/logout')->assertStatus(200);
    }


    // idからユーザー情報取得
    public function testShow()
    {
        $user = factory(User::class)->create();
        // 認証ユーザーでないと取得不可
        $this->getJson('/api/user/' . $user->id)
            ->assertStatus(401);
        $this->actingAs($user)
            ->getJson('/api/user/' . $user->id)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
            ]);
    }


    // ユーザーのプロフィール更新
    public function testUpdate()
    {
        $user = factory(User::class)->create();

        // 成功パターン
        $form = [
            'id' => $user->id,
            'name' => 'ゲリット・コール',
            'pr' => '去年ヤンキースに移籍しました。頑張ります。',
        ];
        $this->postJson('/api/user/edit', $form)
            ->assertStatus(401);
        $this->actingAs($user)
            ->postJson('/api/user/edit', $form)
            ->assertStatus(200);
        $this->assertDatabaseHas('users', $form);

        // 名前は３０文字以内じゃないとダメ
        $rand_name = '';
        for ($i = 0; $i < 31; $i++) {
            $rand_name = $rand_name . chr(mt_rand(65, 90));
        }
        $form['name'] = $rand_name;
        $this->actingAs($user)
            ->postJson('/api/user/edit', $form)
            ->assertStatus(422);

        // 自己紹介は１００文字以内じゃないとダメ
        $rand_pr = '';
        for ($i = 0; $i < 101; $i++) {
            $rand_pr = $rand_pr . chr(mt_rand(65, 90));
        }
        $form['name'] = 'あほ';
        $form['pr'] = $rand_pr;
        $this->actingAs($user)
            ->postJson('/api/user/edit', $form)
            ->assertStatus(422);
    }


    // ユーザーのパスワードの更新
    public function testPasswordUpdate()
    {
        // １つユーザー作成
        $userData = [
            'name' => 'なおと',
            'email' => 'naoto@mail',
            'password' => 'naoto1234',
            'password_confirmation' => 'naoto1234'
        ];
        $this->postJson('/api/register', $userData);
        $user = User::where('email', 'naoto@mail')->first();

        // 成功パターン
        $form = [
            'id' => $user->id,
            'currentPassword' => 'naoto1234',
            'newPassword' => 'naoto123456',
            'newPassword_confirmation' => 'naoto123456',
        ];
        $this->postJson('/api/user/password', $form)->assertStatus(401);
        $this->actingAs($user)
            ->postJson('/api/user/password', $form)
            ->assertStatus(200);

        // 現在のパスワードが間違っていてバリデーションエラー
        $form2 = [
            'id' => $user->id,
            'currentPassword' => 'naoto234',
            'newPassword' => 'naoto3456',
            'newPassword_confirmation' => 'naoto3456',
        ];
        $this->actingAs($user)
            ->postJson('/api/user/password', $form2)
            ->assertStatus(422);

        // 新しいパスワードが６文字未満ゆえバリデーションエラー
        $form3 = [
            'id' => $user->id,
            'currentPassword' => 'naoto123456',
            'newPassword' => 'naoto',
            'newPassword_confirmation' => 'naoto',
        ];
        $this->actingAs($user)
            ->postJson('/api/user/password', $form3)
            ->assertStatus(422);

        // 新しいパスワードの確認（confirmation）失敗によりバリデーションエラー
        $form4 = [
            'id' => $user->id,
            'currentPassword' => 'naoto123456',
            'newPassword' => 'naoto3456',
            'newPassword_confirmation' => 'naoto34567',
        ];
        $this->actingAs($user)
            ->postJson('/api/user/password', $form4)
            ->assertStatus(422);

        // パスワードは半角英数字、ハイフン、アンダーバーのみ可
        $form5 = [
            'id' => $user->id,
            'currentPassword' => 'naoto123456',
            'newPassword' => '       ',
            'newPassword_confirmation' => '       ',
        ];
        $this->actingAs($user)
            ->postJson('/api/user/password', $form5)
            ->assertStatus(422);
        $form5['newPassword'] = '#$%1234';
        $form5['newPassword_confirmation'] = '#$%1234';
        $this->actingAs($user)
            ->postJson('/api/user/password', $form5)
            ->assertStatus(422);
    }


    // 検索したユーザーを取得
    public function testGetSearchUsers()
    {
        $user = factory(User::class)->create();
        $this->getJson('/api/search/users/' . $user->name)
            ->assertStatus(401);
        $this->actingAs($user)
            ->getJson('/api/search/users/' . $user->name)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'icon' => null,
                'name' => $user->name,
                'pr' => null,
            ]);
    }
}
