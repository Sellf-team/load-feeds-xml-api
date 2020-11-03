<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SfAnuncio
 * @package App\Models
 * @version November 2, 2020, 2:31 pm UTC
 *
 * @property string|\Carbon\Carbon $data_cadastro
 * @property string $titulo
 * @property string $descricao
 * @property integer $tipo_imovel_id
 * @property integer $anunciante_id
 * @property number $valor
 * @property number $valor_de
 * @property number $valor_desconto
 * @property number $premio
 * @property number $valor_premio
 * @property string $dns
 * @property string $cep
 * @property string $logradouro
 * @property string $numero
 * @property string $bairro
 * @property integer $estado_id
 * @property integer $cidade_id
 * @property string $whatsapp
 * @property string $nome
 * @property string $imagem
 * @property integer $status
 * @property string $identificador
 * @property integer $aprovado_por_id
 * @property string|\Carbon\Carbon $data_aprovado
 * @property integer $area_util
 * @property integer $quartos
 * @property integer $quartos_semar
 * @property integer $quartos_ar
 * @property integer $suites_semi_semar
 * @property integer $suites_semi_ar
 * @property integer $suites_ar
 * @property integer $suites_semar
 * @property integer $banheiros
 * @property integer $vagas
 * @property integer $vagas_presas_descobertas
 * @property integer $vagas_presas_cobertas
 * @property integer $vagas_livres_descobertas
 * @property integer $vagas_livres_cobertas
 * @property number $iptu
 * @property integer $iptu_periodo
 * @property number $condominio
 * @property string $video
 * @property string $audio
 * @property string $foto360
 * @property integer $andar
 * @property integer $aceita_pet
 * @property integer $mobiliado
 * @property integer $franquia_id
 * @property string $lat
 * @property string $lng
 * @property string $verificado
 * @property integer $idade_imovel
 * @property integer $check_pagar_depois
 * @property integer $ocultar_telefone
 * @property integer $armarios_banheiro
 * @property integer $armarios_cozinha
 * @property integer $banheira_hidro
 * @property integer $area_servico
 * @property integer $tem_varanda
 * @property integer $area_privativa
 * @property integer $quarto_servico
 * @property integer $banheiro_servico
 * @property integer $tem_piscina_privativa
 * @property integer $espaco_gourmet_privativo
 * @property integer $quintal
 * @property integer $box_banheiro
 * @property integer $ar_condicionado
 * @property integer $elevador
 * @property integer $gas_canalizado
 * @property integer $playground
 * @property integer $academia
 * @property integer $tem_piscina_condominio
 * @property integer $salao_festas
 * @property integer $espaco_gourmet_condominio
 * @property integer $quadra_condominio
 * @property integer $sauna_condominio
 * @property integer $lavanderia_condominio
 * @property integer $churrasqueira_condominio
 * @property string $complemento
 * @property number $valor_mensal
 * @property string|\Carbon\Carbon $data_liberado
 * @property string $nome_edificio
 * @property integer $tipo_anunciante_id
 * @property integer $ordem
 * @property string|\Carbon\Carbon $data_premiacao
 * @property integer $tipo_anuncio_id
 * @property integer $usr_alteracao
 * @property integer $id_imovel_integracao
 */
class SfAnuncio extends Model
{

    public $table = 'sf_anuncio';

    public $timestamps = false;


    public $connection = "mysql";

    public $fillable = [
        'data_cadastro',
        'titulo',
        'descricao',
        'tipo_imovel_id',
        'anunciante_id',
        'valor',
        'valor_de',
        'valor_desconto',
        'premio',
        'valor_premio',
        'dns',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'estado_id',
        'cidade_id',
        'whatsapp',
        'nome',
        'imagem',
        'status',
        'identificador',
        'aprovado_por_id',
        'data_aprovado',
        'area_util',
        'quartos',
        'quartos_semar',
        'quartos_ar',
        'suites_semi_semar',
        'suites_semi_ar',
        'suites_ar',
        'suites_semar',
        'banheiros',
        'vagas',
        'vagas_presas_descobertas',
        'vagas_presas_cobertas',
        'vagas_livres_descobertas',
        'vagas_livres_cobertas',
        'iptu',
        'iptu_periodo',
        'condominio',
        'video',
        'audio',
        'foto360',
        'andar',
        'aceita_pet',
        'mobiliado',
        'franquia_id',
        'lat',
        'lng',
        'verificado',
        'idade_imovel',
        'check_pagar_depois',
        'ocultar_telefone',
        'armarios_banheiro',
        'armarios_cozinha',
        'banheira_hidro',
        'area_servico',
        'tem_varanda',
        'area_privativa',
        'quarto_servico',
        'banheiro_servico',
        'tem_piscina_privativa',
        'espaco_gourmet_privativo',
        'quintal',
        'box_banheiro',
        'ar_condicionado',
        'elevador',
        'gas_canalizado',
        'playground',
        'academia',
        'tem_piscina_condominio',
        'salao_festas',
        'espaco_gourmet_condominio',
        'quadra_condominio',
        'sauna_condominio',
        'lavanderia_condominio',
        'churrasqueira_condominio',
        'complemento',
        'valor_mensal',
        'data_liberado',
        'nome_edificio',
        'tipo_anunciante_id',
        'ordem',
        'data_premiacao',
        'tipo_anuncio_id',
        'usr_alteracao',
        'id_imovel_integracao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'data_cadastro' => 'datetime',
        'titulo' => 'string',
        'descricao' => 'string',
        'tipo_imovel_id' => 'integer',
        'anunciante_id' => 'integer',
        'valor' => 'float',
        'valor_de' => 'float',
        'valor_desconto' => 'float',
        'premio' => 'float',
        'valor_premio' => 'float',
        'dns' => 'string',
        'cep' => 'string',
        'logradouro' => 'string',
        'numero' => 'string',
        'bairro' => 'string',
        'estado_id' => 'integer',
        'cidade_id' => 'integer',
        'whatsapp' => 'string',
        'nome' => 'string',
        'imagem' => 'string',
        'status' => 'integer',
        'identificador' => 'string',
        'aprovado_por_id' => 'integer',
        'data_aprovado' => 'datetime',
        'area_util' => 'integer',
        'quartos' => 'integer',
        'quartos_semar' => 'integer',
        'quartos_ar' => 'integer',
        'suites_semi_semar' => 'integer',
        'suites_semi_ar' => 'integer',
        'suites_ar' => 'integer',
        'suites_semar' => 'integer',
        'banheiros' => 'integer',
        'vagas' => 'integer',
        'vagas_presas_descobertas' => 'integer',
        'vagas_presas_cobertas' => 'integer',
        'vagas_livres_descobertas' => 'integer',
        'vagas_livres_cobertas' => 'integer',
        'iptu' => 'float',
        'iptu_periodo' => 'integer',
        'condominio' => 'float',
        'video' => 'string',
        'audio' => 'string',
        'foto360' => 'string',
        'andar' => 'integer',
        'aceita_pet' => 'integer',
        'mobiliado' => 'integer',
        'franquia_id' => 'integer',
        'lat' => 'string',
        'lng' => 'string',
        'verificado' => 'string',
        'idade_imovel' => 'integer',
        'check_pagar_depois' => 'integer',
        'ocultar_telefone' => 'integer',
        'armarios_banheiro' => 'integer',
        'armarios_cozinha' => 'integer',
        'banheira_hidro' => 'integer',
        'area_servico' => 'integer',
        'tem_varanda' => 'integer',
        'area_privativa' => 'integer',
        'quarto_servico' => 'integer',
        'banheiro_servico' => 'integer',
        'tem_piscina_privativa' => 'integer',
        'espaco_gourmet_privativo' => 'integer',
        'quintal' => 'integer',
        'box_banheiro' => 'integer',
        'ar_condicionado' => 'integer',
        'elevador' => 'integer',
        'gas_canalizado' => 'integer',
        'playground' => 'integer',
        'academia' => 'integer',
        'tem_piscina_condominio' => 'integer',
        'salao_festas' => 'integer',
        'espaco_gourmet_condominio' => 'integer',
        'quadra_condominio' => 'integer',
        'sauna_condominio' => 'integer',
        'lavanderia_condominio' => 'integer',
        'churrasqueira_condominio' => 'integer',
        'complemento' => 'string',
        'valor_mensal' => 'float',
        'data_liberado' => 'datetime',
        'nome_edificio' => 'string',
        'tipo_anunciante_id' => 'integer',
        'ordem' => 'integer',
        'data_premiacao' => 'datetime',
        'tipo_anuncio_id' => 'integer',
        'usr_alteracao' => 'integer',
        'id_imovel_integracao' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
