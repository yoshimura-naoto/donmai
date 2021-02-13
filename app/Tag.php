<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name'
    ];

    // postsとのリレーション
    public function posts()
    {
        return $this->belongsToMany('App\Post');
    }

    // postsのdonmaisとのリレーション
    public function donmais()
    {
        return $this->hasManyThrough('App\Donmai', 'App\Post');
    }
}
