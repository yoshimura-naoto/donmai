<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentGood extends Model
{
    protected $table = 'comment_goods';

    protected $fillable = [
        'user_id',
        'comment_id',
    ];


    // コメント（comments）とのリレーション
    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }
}
