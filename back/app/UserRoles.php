<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class UserRoles extends Model
{
    protected $table = 'user_roles';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','permisos'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
