<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'resultado_id',
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
        'resultado_id' => 'integer',
        'carga_imovel_id' => 'string',
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
        'resultado_id' => 'required',
        'carga_imovel_id' => 'required',
        'anunciante_id' => 'required',
        'mensagem' => 'required',
        'code_error' => 'required',
        'created_at' => 'required',
        'updated_at' => 'required'
    ];

    public static function getDetalhesCargaByResultadoId($resultadoCargaId){
        $details = DB::table('relatorio_carga_xml')
            ->select('*')
            ->where('relatorio_carga_xml.resultado_id', $resultadoCargaId)
            ->where('relatorio_carga_xml.code_error', 0)
            ->get(); 
        return $details;
    }
}
