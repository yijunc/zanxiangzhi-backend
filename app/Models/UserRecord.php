<?php
/**
 * Created by PhpStorm.
 * User: zhaoning
 * Date: 2018/5/19
 * Time: 下午4:51
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class UserRecord extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'device_id', 'type', 'status','created_at','updated_at'
    ];

    protected $hidden = [];
}