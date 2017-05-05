<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

/*用户关系(关注) model*/
class UserRelation extends Model
{
    protected $table = 'user_relation';
    protected $primaryKey = 'relation_id';
    protected $guarded = ['relation_id'];
    public $timestamps = false;
}
