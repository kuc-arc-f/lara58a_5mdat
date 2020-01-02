<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//
class Mdat extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'user_id',
        'date',
        'hnum',
        'lnum',
    ];

}
