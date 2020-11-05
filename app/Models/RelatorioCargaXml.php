<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RelatorioCargaXml
 * @package App\Models
 * @version November 3, 2020, 3:13 am UTC
 *
 * @property integer $carga_imovel_id
 * @property integer $anunciante_id
 * @property string $erro
 * @property string|\Carbon\Carbon $created_at
 * @property string|\Carbon\Carbon $updated_at
 */
class RelatorioCargaXml extends Model
{

    public $table = 'relatorio_carga_xml';

    public $timestamps = true;


    public $connection = "mysql";

    public $fillable = [
        'carga_imovel_id',
        'anunciante_id',
        'mensagem',
        'code_error',
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
        'carga_imovel_id' => 'integer',
        'anunciante_id' => 'integer',
        'mensagem' => 'string',
        'code_error' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'carga_imovel_id' => 'required',
        'anunciante_id' => 'required',
        'mensagem' => 'required',
        'code_error' => 'required',
        'created_at' => 'required',
        'updated_at' => 'required'
    ];


}
