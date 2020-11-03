<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cidade
 * @package App\Models
 * @version November 2, 2020, 4:17 pm UTC
 *
 * @property \App\Models\Estado $estado
 * @property string $nome
 * @property integer $estado_id
 */
class Cidade extends Model
{

    public $table = 'cidade';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'nome',
        'estado_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome' => 'string',
        'estado_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'estado_id' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function estado()
    {
        return $this->belongsTo(\App\Models\Estado::class, 'estado_id');
    }
}
