<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;



/**
 * Class AnuncianteCarga
 * @package App\Models
 * @version November 2, 2020, 2:41 pm UTC
 *
 * @property integer $anunciante_id
 * @property string $url
 * @property string|\Carbon\Carbon $created_at
 * @property string|\Carbon\Carbon $updated_at
 */
class AnuncianteCarga extends Model
{

    public $table = 'anunciante_carga_xml';

    public $timestamps = false;



    public $fillable = [
        'anunciante_id',
        'url',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'anunciante_id' => 'integer',
        'url' => 'string',
        'flag_leitura' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'anunciante_id' => 'required',
        'url' => 'required',
        'created_at' => 'required',
        'updated_at' => 'required'
    ];


}
