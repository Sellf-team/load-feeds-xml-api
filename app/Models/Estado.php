<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Estado
 * @package App\Models
 * @version November 2, 2020, 4:16 pm UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection $cidades
 * @property string $nome
 * @property string $sigla
 */
class Estado extends Model
{

    public $table = 'estado';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'nome',
        'sigla'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome' => 'string',
        'sigla' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function cidades()
    {
        return $this->hasMany(\App\Models\Cidade::class,'estado_id','id');
    }
}
