<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TipoImovel
 * @package App\Models
 * @version November 3, 2020, 4:49 am UTC
 *
 * @property string $nome
 */
class TipoImovel extends Model
{

    public $table = 'tipo_imovel';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'nome'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nome' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
