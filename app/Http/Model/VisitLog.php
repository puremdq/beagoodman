<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Model\UserRelation;

class VisitLog extends Model
{


    protected $table = 'visit_log';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = false;

}
