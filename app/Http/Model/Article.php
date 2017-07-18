<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'article';
    protected $primaryKey = 'article_id';
    protected $guarded = ['article_id'];
    public $timestamps = false;

}
