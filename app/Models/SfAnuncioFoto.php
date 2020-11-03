<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SfAnuncioFoto
 * @package App\Models
 * @version November 3, 2020, 3:19 am UTC
 *
 * @property \App\Models\SfAnuncio $sfAnuncio
 * @property integer $sf_anuncio_id
 * @property string $imagem
 * @property integer $principal
 * @property string $texto
 * @property integer $ordem
 * @property integer $usr_alteracao
 */
class SfAnuncioFoto extends Model
{

    public $table = 'sf_anuncio_foto';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'sf_anuncio_id',
        'imagem',
        'principal',
        'texto',
        'ordem',
        'usr_alteracao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sf_anuncio_id' => 'integer',
        'imagem' => 'string',
        'principal' => 'integer',
        'texto' => 'string',
        'ordem' => 'integer',
        'usr_alteracao' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function sfAnuncio()
    {
        return $this->belongsTo(\App\Models\SfAnuncio::class, 'sf_anuncio_id');
    }
}
