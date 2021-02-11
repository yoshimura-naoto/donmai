<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplyGood extends Model
{
    protected $table = 'reply_goods';

    protected $fillable = [
        'user_id',
        'reply_id',
    ];


    // コメントへの返信（replies）とのリレーション
    public function reply()
    {
        return $this->belongsTo('App\Reply');
    }

    // ユーザー（users）とのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
