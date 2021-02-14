<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuchiGood extends Model
{
    protected $table = 'guchi_goods';

    protected $fillable = [
        'user_id',
        'guchi_id',
    ];


    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // グチとのリレーション
    public function guchi()
    {
        return $this->belongsTo('App\Guchi');
    }
}
