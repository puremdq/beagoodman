<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{


    protected $table = 'file';
    protected $primaryKey = 'file_key';

    protected $fillable = ['file_key','file_path'];
    public $timestamps = false;



}
