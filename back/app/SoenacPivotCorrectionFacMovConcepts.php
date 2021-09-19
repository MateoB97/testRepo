<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class SoenacPivotCorrectionFacMovConcepts extends Model
{
    protected $table = 'soenac_pivot_correction_fac_mov_concepts';

    protected $fillable = [
        'correction_id',
        'fac_mov_id'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

}
