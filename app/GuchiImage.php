<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GuchiImage extends Model
{
    protected $table = 'guchi_images';

    protected $fillable = [
        'guchi_id',
        'path',
    ];


    // グチとのリレーション
    public function guchi()
    {
        return $this->belongsTo('App\Guchi');
    }
}
