<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AnuncianteProfissionalPacote
 * @package App\Models
 * @version November 3, 2020, 3:21 am UTC
 *
 * @property integer $anunciante_id
 * @property integer $pacote_id
 * @property integer $unidades
 * @property integer $periodo
 * @property string|\Carbon\Carbon $data
 * @property integer $voucher_id
 * @property integer $usr_alteracao
 */
class AnuncianteProfissionalPacote extends Model
{

    public $table = 'anunciante_profissional_pacote';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'anunciante_id',
        'pacote_id',
        'unidades',
        'periodo',
        'data',
        'voucher_id',
        'usr_alteracao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'anunciante_id' => 'integer',
        'pacote_id' => 'integer',
        'unidades' => 'integer',
        'periodo' => 'integer',
        'data' => 'datetime',
        'voucher_id' => 'integer',
        'usr_alteracao' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'anunciante_id' => 'required'
    ];


}
