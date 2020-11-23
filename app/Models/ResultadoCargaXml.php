<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ResultadoCargaXml
 * @package App\Models
 * @version November 3, 2020, 3:13 am UTC
 *
 * @property integer $carga_imovel_id
 * @property integer $anunciante_id
 * @property string $erro
 * @property string|\Carbon\Carbon $created_at
 * @property string|\Carbon\Carbon $updated_at
 */
class ResultadoCargaXml extends Model
{

    public $table = 'resultado_carga_xml';

    public $timestamps = true;


    public $connection = "mysql";

    public $fillable = [
        'anunciante_id',
        'qtd_excluidos',
        'qtd_recebidos',
        'qtd_negados',
        'flag_enviado',
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
        'qtd_excluidos' => 'integer',
        'qtd_recebidos' => 'integer',
        'qtd_negados' => 'integer',
        'flag_enviado' => 'integer',
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
        'qtd_recebidos' => 'required',
        'created_at' => 'required',
        'updated_at' => 'required'
    ];


}
