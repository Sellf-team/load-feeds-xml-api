<?php

namespace App\Http\Controllers;
use App\Models\AnuncianteCarga;
use App\Models\SfAnuncio;
use App\Models\Estado;
use App\Models\AnuncianteProfissionalPacote;
use App\Models\RelatorioCargaXml;
use App\Models\SfAnuncioFoto;
use App\Models\TipoImovel;
use stdClass;
use Illuminate\Support\Facades\Storage;
class AnunciosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }
    public function validaCamposObrigatorios($data)
    {
        $retornoValidacoes = new StdClass();
        $retornoValidacoes->mensagem = '';
        $retorno = '';
        $flagCodImovel=0;
        if(!isset($data->CodigoImovel) || $data->CodigoImovel == '')
        {
            $retorno .= "Código do imóvel vazio!;";
            $flagCodImovel++;
        }
        else
        {
            $retornoValidacoes->codigoImovel = $data->CodigoImovel;
        }
        if(!isset($data->TipoImovel) || $data->TipoImovel == '')
        {
            $retorno .= "Tipo do imóvel vazio!;";
        }
        if(!isset($data->SubTipoImovel) || $data->SubTipoImovel == '')
        {
            $retorno .= "Subtipo do imóvel vazio!;";
        }
        if(!isset($data->CategoriaImovel) || $data->CategoriaImovel == '')
        {
            $retorno .= "Categoria do imóvel vazia!;";
        }
        if(!isset($data->UF) || $data->UF == '')
        {
            $retorno .= "UF do imóvel vazia!;";
        }
        if(!isset($data->Cidade) || $data->Cidade == '')
        {
            $retorno .= "UF do imóvel vazia!;";
        }
        if(!isset($data->Bairro) || $data->Bairro == '')
        {
            $retorno .= "Bairro do imóvel vazio!;";
        }
        if((!isset($data->PrecoVenda) || $data->PrecoVenda == '') &&
            (!isset($data->PrecoLocacao) || $data->PrecoLocacao == ''))
        {
            $retorno .= "Preço de venda e Preço de Locação do imóvel vazio!;";
        }
        if(!isset($data->AreaUtil) || $data->AreaUtil == '')
        {
            $retorno .= "Area útil do imóvel vazia!;";
        }
        if(!isset($data->QtdDormitorios) || $data->QtdDormitorios == '')
        {
            $retorno .= "Quantidade de dormitórios do imóvel vazia!;";
        }
        $retornoValidacoes->mensagem = $retorno;
        $retornoValidacoes->flagCodImovel = $flagCodImovel;
        return  $retornoValidacoes;

    }
    public function tipoAnuncio($data)
    {
        $anuncioTipo = new stdClass();
        if(!isset($data->PrecoLocacao) && $data->PrecoLocacao != '')
        {
            $anuncioTipo->id =2;
            $anuncioTipo->valor =(float)$data->PrecoLocacao;


        }
        else if(!isset($data->Venda) && $data->PrecoVenda != '')
        {
            $anuncioTipo->id =1;
            $anuncioTipo->valor =(float)$data->PrecoVenda;

        }
        return  $anuncioTipo;
    }
    public function cidadeEstado($data)
    {
        $retorno = new stdClass();
        $estado = Estado::where('sigla',$data->UF)->first();
        if(empty($estado))
        {
            //gravar log aqui de nao encontrar estado
            $retorno->sucesso = 0;
            $retorno->mensagem = 'Estado não encontrado;';
        }
        else
        {
            $cidade = $estado->cidades()->where('nome',$data->Cidade)->first();
            if(empty($cidade))
            {
                $retorno->sucesso = 0;
                $retorno->mensagem = 'Cidade não encontrada;';
            }
            else
            {
                $retorno->sucesso = 1;
                $retorno->id_cidade = $cidade->id;
                $retorno->id_estado = $estado->id;
            }
        }
        return  $retorno;

    }
    public function statusAnuncio($anuncianteId)
    {
        $infoPacote = AnuncianteProfissionalPacote::where('anunciante_id',$anuncianteId)->first();
        if($infoPacote->unidades ==0)
        {
            return 0;
        }
        else
        {
            return 1;
            $infoPacote->unidades--;
            $infoPacote->save();
        }
    }
    public function RollbackAnuncio($anuncianteId)
    {
        $infoPacote = AnuncianteProfissionalPacote::where('anunciante_id',$anuncianteId)->first();
        $infoPacote->unidades--;
        $infoPacote->save();
        return 0;
    }
    public function tipoImovel($data)
    {
       $tipoImovel = TipoImovel::where('nome',$data->TipoImovel)->first();
       return $tipoImovel;
    }
    public function salvarDadosXmlZap($data,$status,$anuncianteId)
    {

        $validaCamposObrigatorios = $this->validaCamposObrigatorios($data);
        if($validaCamposObrigatorios->flagCodImovel ==0)
        {
            $init = new RelatorioCargaXml();
            $init->anunciante_id = $anuncianteId;
            $init->mensagem = 'Carga Iniciada';
            $init->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $init->save();
        }
        if($validaCamposObrigatorios->mensagem != '')
        {
            if($validaCamposObrigatorios->flagCodImovel ==0)
            {
                $erro = new RelatorioCargaXml();
                $erro->anunciante_id = $anuncianteId;
                $erro->mensagem=$validaCamposObrigatorios->mensagem;
                $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
                $erro->save();
            }
            return 0;
        }
        $cidadeEstado = $this->cidadeEstado($data);
        if($cidadeEstado->sucesso = 0)
        {
            $erro = new RelatorioCargaXml();
            $erro->anunciante_id = $anuncianteId;
            $erro->mensagem=$cidadeEstado->mensagem;
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();
            return  0;
        }
        $tipoImovel = $this->tipoImovel($data);
        if(empty($tipoImovel))
        {
            $erro = new RelatorioCargaXml();
            $erro->anunciante_id = $anuncianteId;
            $erro->mensagem="Tipo de Imóvel não encontrado";
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();
            return  0;
        }
        $tipoAnuncio = $this->tipoAnuncio($data);
        $anuncio = new SfAnuncio();
        $anuncio->id_imovel_integracao = $data->CodigoImovel;
        $anuncio->anunciante_id = $anuncianteId;
        $anuncio->tipo_imovel_id = $tipoImovel->id;
        $anuncio->status = $status;
        $anuncio->estado_id = $cidadeEstado->id_estado;
        $anuncio->cidade_id = $cidadeEstado->id_cidade;
        $anuncio->Bairro = $data->Bairro;
        $anuncio->numero = $data->Numero;
        $anuncio->complemento = $data->Complemento;
        $anuncio->cep = $data->CEP;
        $anuncio->tipo_anuncio_id = $tipoAnuncio->id;
        $anuncio->valor= $tipoAnuncio->valor;
        $anuncio->condominio=(float) $data->PrecoCondominio;
        $anuncio->area_util= (float)$data->AreaUtil;
        $anuncio->quartos = (int)$data->QtdDormitorios;
        $anuncio->suites_semar = (int)$data->QtdSuites;
        $anuncio->banheiros = (int)$data->QtdBanheiros;
        $anuncio->vagas = (int)$data->QtdVagas;
        $anuncio->elevador = (int)$data->QtdElevador;
        $anuncio->descricao = $data->Observacao;
        $anuncio->ar_condicionado = (int)$data->ArCondicionado;
        $anuncio->armarios_cozinha = (int)$data->ArmarioCozinha;
        $anuncio->churrasqueira_condominio = (int)$data->Churrasqueira;
        $anuncio->playground = (int)$data->Playground;
        $anuncio->quadra_condominio = (int)$data->QuadraPoliEsportiva;
        $anuncio->quintal = (int)$data->Quintal;
        $anuncio->salao_festas = (int)$data->SalaoFestas;
        $anuncio->sauna_condominio = (int)$data->Sauna;
        $anuncio->tem_varanda = (int)$data->Varanda;
        $anuncio->quarto_servico = (int)$data->QuartoWCEmpregada;
        $anuncio->banheira_hidro = (int)$data->Hidromassagem;
        $anuncio->area_servico = (int)$data->AreaServico;
        $anuncio->valor_mensal = (float)$data->ValorMensal;
        try
        {

            $anuncio->save();
        }
        catch(\Exception $e)
        {
            $erro = new RelatorioCargaXml();
            $erro->anunciante_id = $anuncianteId;
            $erro->mensagem=$e;
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();

            //gravar log com  erro variavel e->getMessage();
            return  0;
        }

        return $anuncio->id;

    }
    public function salvarFotos($data,$id,$anuncianteId)
    {
        $codigoImovel = $data->CodigoImovel ;
        // $basePath = env('URL_STORAGE', storage_path());
        // $basePath = $basePath . "/" . $id . "/";
        for($i=0;$i<sizeof($data->Fotos->Foto);$i++)
        {
            $basePathSave = $id ."/" . $data->Fotos->Foto[$i]->NomeArquivo;
            try
            {
                $conteudoImg = file_get_contents($data->Fotos->Foto[$i]->URLArquivo);
                Storage::disk('custom')->put($basePathSave, $conteudoImg);
                $anuncioFoto  = new SfAnuncioFoto();
                $anuncioFoto->sf_anuncio_id = $id ;
                $anuncioFoto->imagem = $basePathSave;
                $anuncioFoto->principal = $data->Fotos->Foto[$i]->Principal;
                $anuncioFoto->texto = $data->Fotos->Foto[$i]->NomeArquivo;
                $anuncioFoto->ordem = $i;
                // $anuncioFoto->usr_alteracao = $data->Fotos->Foto[$i]->Alterada;
                $anuncioFoto->save();


            }
            catch (\Exception $e)
            {
                $erro = new RelatorioCargaXml();
                $erro->anunciante_id = $anuncianteId;
                $erro->mensagem=$e;
                $erro->carga_imovel_id= $codigoImovel;
                $erro->save();

            }

            //aqui salvamos as imagens
        }
        $end = new RelatorioCargaXml();
        $end->anunciante_id = $anuncianteId;
        $end->mensagem = 'Carga Finalizada';
        $end->carga_imovel_id= $codigoImovel;
        $end->save();
        return;
    }
    public function leituraXmlZap()
    {

        $data = AnuncianteCarga::all();
        $xml_conteudo ='';
        $retorno =0 ;
        for ($i=0;$i<sizeof($data);$i++)
        {
            SfAnuncio::where('anunciante_id',$data[$i]->anunciante_id)->update(['flag_exclusao' => 1]);
            $xml_conteudo  = simplexml_load_file($data[$i]->url);

            for ($j=0; $j<sizeof($xml_conteudo->Imoveis->Imovel);$j++)
            {
                $statusAnuncio =$this->statusAnuncio($data[$i]->anunciante_id);
                $retorno = $this->salvarDadosXmlZap($xml_conteudo->Imoveis->Imovel[$j],$statusAnuncio,$data[$i]->anunciante_id);
                if($retorno >0)
                {
                    $salvarFotos = $this->salvarFotos($xml_conteudo->Imoveis->Imovel[$j],$retorno,$data[$i]->anunciante_id);
                }
                else
                {
                    if($statusAnuncio == 1)
                    {
                        $this->RollbackAnuncio($data[$i]->anunciante_id);
                    }
                }

            }
            SfAnuncio::where('flag_exclusao', 1)->delete();
        }
        return 200;
    }


}
