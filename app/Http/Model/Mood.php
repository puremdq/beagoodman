<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{


    protected $table = 'mood';
    protected $primaryKey = 'mood_id';
    protected $guarded = ['mood_id'];
    public $timestamps = false;

}
