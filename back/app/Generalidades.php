<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class Generalidades extends Model
{
    protected $table = 'generalidades';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['prod_cuarto_dela','prod_cuarto_tras','prod_canal'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
