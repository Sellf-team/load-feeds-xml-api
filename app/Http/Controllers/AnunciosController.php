<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AnuncianteCarga;
use App\Models\SfAnuncio;
use App\Models\Estado;
use App\Models\Cidade;
use App\Models\AnuncianteProfissionalPacote;
use App\Models\RelatorioCargaXml;
use App\Models\ResultadoCargaXml;
use App\Models\SfAnuncioFoto;
use App\Models\TipoImovel;
use App\Services\MailService;
use App\Services\CepService;
use stdClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
            $retorno .= " Código do imóvel vazio;";
            $flagCodImovel++;
        }else
        {
            $retornoValidacoes->codigoImovel = $data->CodigoImovel;
        }
        if(mb_strlen($data->Observacao) > 3000){
            $retorno .= " A descrição do imóvel deve ter entre 0 e 3000 caracteres;";
        }
        if(!isset($data->TipoImovel) || $data->TipoImovel == '')
        {
            $retorno .= " Tipo do imóvel vazio;";
        }
        
        $validaCep = new CepService();
        if(!isset($data->CEP) || $data->CEP == '' || strlen($data->CEP) < 8){
            if(!isset($data->Cep) || $data->Cep == '' || strlen($data->Cep) < 8){
                if(!isset($data->cep) || $data->cep == '' || strlen($data->cep) < 8){
                    $retorno .= " CEP vazio;";
                }else if(!isset($data->cep) || $data->cep == '' || strlen($data->cep) < 8){
                    if(!$validaCep->validateCep($data->cep)){
                        $retorno .= " CEP inválido;";
                    }
                }
            }else if(isset($data->cep) || $data->cep == '' || strlen($data->cep) < 8){
                if(!$validaCep->validateCep($data->Cep)){
                    $retorno .= " CEP inválido;";
                }
            }
        }else if(isset($data->CEP) || $data->CEP == '' || strlen($data->CEP) < 8){
            if(!$validaCep->validateCep($data->CEP)){
                $retorno .= " CEP inválido;";
            }
        }
        if(!isset($data->SubTipoImovel) || $data->SubTipoImovel == '')
        {
            $retorno .= " Subtipo do imóvel vazio;";
        }
        if(!isset($data->CategoriaImovel) || $data->CategoriaImovel == '')
        {
            $retorno .= " Categoria do imóvel vazia;";
        }
        if(!isset($data->UF) || $data->UF == '')
        {
            if(!isset($data->Estado) || $data->Estado == ''){
                $retorno .= " UF do imóvel vazia;";
            }
        }
        if(!isset($data->Cidade) || $data->Cidade == '')
        {
            $retorno .= " UF do imóvel vazia;";
        }
        if(!isset($data->Bairro) || $data->Bairro == '')
        {
            $retorno .= " Bairro do imóvel vazio;";
        }
        if(!isset($data->Fotos) || sizeof($data->Fotos) == 0)
        {
            $retorno .= " Anúncio sem Fotos;";
        }
        if((!isset($data->PrecoVenda) || $data->PrecoVenda == '' || $data->PrecoVenda == ' ' || $data->PrecoVenda == '0' || $data->PrecoVenda == 0) &&
            (!isset($data->PrecoLocacao) || $data->PrecoLocacao == '' || $data->PrecoLocacao == ' ' || $data->PrecoLocacao == '0' || $data->PrecoLocacao == 0))
        {
            $retorno .= " Preço de venda e Preço de Locação do imóvel vazios;";
        }

        if($this->tipoImovel($data) == 1 || $this->tipoImovel($data) == 5 || $this->tipoImovel($data) == 6 || $this->tipoImovel($data) == 10){
            if(!isset($data->AreaUtil) || $data->AreaUtil == '' || $data->AreaUtil == '0' || $data->AreaUtil == 0)
            {
                $retorno .= " Área útil do imóvel vazia;";   
            }           
        }else{
            if(!isset($data->AreaTotal) || $data->AreaTotal == '' || $data->AreaTotal == '0' || $data->AreaTotal == 0)
            {
                $retorno .= " Área total do imóvel vazia;";   
            }           
        }

        if($this->tipoImovel($data) == 1 || $this->tipoImovel($data) == 5){
            if($this->tipoAnuncio($data)->id == 2){
                if($data->AreaUtil < 10 || $data->AreaUtil > 9999){
                    $retorno .= " Área útil do imóvel deve estar entre 10 e 9999;";   
                }
            }else{
                if($data->AreaUtil < 20 || $data->AreaUtil > 9999){
                    $retorno .= " Área útil do imóvel deve estar entre 20 e 9999;";   
                }
            }
        }

        if($this->tipoImovel($data) == 1 || $this->tipoImovel($data) == 5){
            if($this->tipoAnuncio($data)->id == 1){
                if($data->AreaUtil < 15 || $data->AreaUtil > 9999){
                    $retorno .= " Área útil do imóvel deve estar entre 15 e 9999;";   
                }
            }
        }

        if($this->tipoImovel($data) == 6){
            if($data->AreaUtil < 4 || $data->AreaUtil > 999999){
                $retorno .= " Área útil do imóvel deve estar entre 4 e 999999;";   
            }
        }

        if($this->tipoImovel($data) == 8 || $this->tipoImovel($data) == 14){
            if($data->AreaUtil < 2){
                $retorno .= " Área útil do imóvel não pode ser menor que 2;";   
            }
        }
        
        if($this->tipoImovel($data) == 1 || $this->tipoImovel($data) == 5 || $this->tipoImovel($data) == 7 || $this->tipoImovel($data) == 8){
            if(!isset($data->QtdBanheiros) || $data->QtdBanheiros == '')
            {
                $retorno .= " Quantidade de banheiros do imóvel vazia;";
            }
        }

        if($data->TipoImovel == 'Apartamento' || $data->TipoImovel == 'Casa' || $data->TipoImovel == 'Flat/Aparthotel')
        {
            if($data->SubTipoImovel !== 'Kitchenette/Conjugados')
            {
                if(!isset($data->QtdDormitorios) || $data->QtdDormitorios == '')
                {
                    $retorno .= " Quantidade de dormitórios do imóvel vazia;";
                }
            }
        }
        if(!isset($retornoValidacoes->codigoImovel) || $retornoValidacoes->codigoImovel == ''){
            $retornoValidacoes->codigoImovel = 0;
        }
        $retornoValidacoes->mensagem = $retorno;
        $retornoValidacoes->flagCodImovel = $flagCodImovel;
        return  $retornoValidacoes;

    }
    public function tipoAnuncio($data)
    {
        $anuncioTipo = new stdClass();
        if(isset($data->PrecoLocacao) && $data->PrecoLocacao != '' && $data->PrecoLocacao != ' ' && $data->PrecoLocacao != 0 && $data->PrecoLocacao != '0')
        {
            $anuncioTipo->id = 2;
            $anuncioTipo->valor = $data->PrecoLocacao;
        }
        elseif ((isset($data->PrecoLocacao) && $data->PrecoLocacao != '' && $data->PrecoLocacao != ' ' && $data->PrecoLocacao != '0' && $data->PrecoLocacao != 0) && (isset($data->PrecoVenda) && $data->PrecoVenda != ' ' && $data->PrecoVenda != '0' && $data->PrecoVenda != 0)){
            $anuncioTipo->id = 1;
            $anuncioTipo->valor =  $data->PrecoVenda;
        }
        else if(isset($data->PrecoVenda) && $data->PrecoVenda != '' && $data->PrecoVenda != ' ' && $data->PrecoVenda != 0 && $data->PrecoVenda != '0'){
            $anuncioTipo->id = 1;
            $anuncioTipo->valor = $data->PrecoVenda;
        }else{
            $anuncioTipo->id = 1;
            $anuncioTipo->valor = '';
        }
        return  $anuncioTipo;
    }
    public function cidadeEstado($data)
    {
        $retorno = new stdClass();
        
        $estado = Estado::where('sigla',$data->UF)->first();

        if(is_null($estado)){
            $estado = Estado::where('nome', $data->UF)->first();
        }
        
        if(is_null($estado)){
            $estado = Estado::where('sigla',$data->Estado)->first();
        }

        if(is_null($estado)){
            $estado = Estado::where('nome', $data->Estado)->first();
        }

        $cidade = $this->removeChar($data->Cidade);
        $cidade = $estado->cidades()->where('nome', ucfirst(mb_strtolower($data->Cidade)))->first();
        if(empty($cidade))
        {
            $ultimaCidade = DB::table('cidade')->orderBy('id', 'desc')->first();
            $newCidade = new Cidade();
            $newCidade->id = $ultimaCidade->id + 1;
            $newCidade->nome = $this->removeChar($data->Cidade);
            $newCidade->estado_id = $estado->id;
            $newCidade->save();
            
            $retorno->sucesso = 1;
            $retorno->id_cidade = $newCidade->id;
            $retorno->nome_cidade = $newCidade->nome;
            $retorno->id_estado = $estado->id;
        }
        else
        {
            $retorno->sucesso = 1;
            $retorno->id_cidade = $cidade->id;
            $retorno->nome_cidade = $cidade->nome;
            $retorno->id_estado = $estado->id;
        }
        
        return  $retorno;

    }
    public function statusAnuncio($anuncianteId)
    {
        $infoPacote = AnuncianteProfissionalPacote::where('anunciante_id',$anuncianteId)->first();
        if($infoPacote->unidades == 0)
        {
            return 0;
        }
        else
        {
            $infoPacote->unidades--;
            $infoPacote->save();
            return 1;
        }
    }
    public function RollbackAnuncio($anuncianteId)
    {
        $infoPacote = AnuncianteProfissionalPacote::where('anunciante_id',$anuncianteId)->first();
        $infoPacote->unidades = $infoPacote->unidades + 1;
        $infoPacote->save();
        return 0;
    }
    public function tipoImovel($data)
    {        
        if($data->TipoImovel == "Comercial/Industrial" || str_contains(mb_strtolower($data->TipoImovel), 'comercial') || str_contains(mb_strtolower($data->TipoImovel), 'espaco corporativo') || str_contains(mb_strtolower($data->TipoImovel), 'espaço corporativo')){
            return 6;
        }else if(str_contains(mb_strtolower($data->TipoImovel), 'studio') || str_contains(mb_strtolower($data->TipoImovel), 'flat') || str_contains(mb_strtolower($data->TipoImovel), 'apart') || str_contains(mb_strtolower($data->TipoImovel), 'cobertura') || str_contains(mb_strtolower($data->TipoImovel), 'apartamento')){
            return 1;
        }else if(str_contains(mb_strtolower($data->TipoImovel), 'terreno') || $data->TipoImovel == "Terreno"){
            return 12;
        }else if(str_contains(mb_strtolower($data->TipoImovel), 'casa em') || $data->TipoImovel == "Casa em Condomínio" || $data->TipoImovel == "Casa em Condominio"){
            return 7;
        }else if(str_contains(mb_strtolower($data->TipoImovel), 'sítio') || str_contains(mb_strtolower($data->TipoImovel), 'sitio') || $data->TipoImovel == "Sítio" || $data->TipoImovel == "Sitio"){
            return 14;
        }else if($data->TipoImovel == "Rural"){
            if($data->SubTipoImovel == 'Sítio' || $data->SubTipoImovel == 'Sitio'){
                return 14;
            }else if($data->SubTipoImovel == 'Chácara' || $data->SubTipoImovel == 'Chacara'){
                return 14;
            }else if($data->SubTipoImovel == 'Fazenda'){
                return 8;
            }else{
                return 14;
            }
        }else{            
            $tipoImovel = TipoImovel::where('nome', $data->TipoImovel)->first();
            if(empty($tipoImovel)){
                return '';
            }
            return $tipoImovel->id;
        }
    }
    
    public function salvarDadosXmlZap($data, $status, $anuncianteId, $resultadoCargaId)
    {
        $validaCamposObrigatorios = $this->validaCamposObrigatorios($data);
        if($validaCamposObrigatorios->flagCodImovel ==0)
        {
            $init = new RelatorioCargaXml();
            $init->resultado_id = $resultadoCargaId;
            $init->anunciante_id = $anuncianteId;
            $init->mensagem = 'Carga Iniciada';
            $init->code_error = 2;
            $init->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $init->save();
        }
        if($validaCamposObrigatorios->mensagem != '')
        {
            if($validaCamposObrigatorios->flagCodImovel ==0)
            {
                $erro = new RelatorioCargaXml();
                $erro->resultado_id = $resultadoCargaId;
                $erro->anunciante_id = $anuncianteId;
                $erro->mensagem=$validaCamposObrigatorios->mensagem;
                $erro->code_error=0;
                $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
                $erro->save();

                $erroResultado = ResultadoCargaXml::find($resultadoCargaId);
                $erroResultado->qtd_negados++;                
                $erroResultado->save();
            }

            $this->RollbackAnuncio($anuncianteId);

            return 0;
        }
        $cidadeEstado = $this->cidadeEstado($data);
        $idCidade = 0;
        $idEstado = 0;
        if($cidadeEstado->sucesso == 0)
        {
            $erro = new RelatorioCargaXml();
            $erro->resultado_id = $resultadoCargaId;
            $erro->anunciante_id = $anuncianteId;
            $erro->mensagem=$cidadeEstado->mensagem;
            $erro->code_error= 0;
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();
            
            return  0;
        }else{
            $idCidade = $cidadeEstado->id_cidade;
            $idEstado = $cidadeEstado->id_estado;
            $nomeCidade = $cidadeEstado->nome_cidade;
        }
        $tipoImovel = $this->tipoImovel($data);
        if(empty($tipoImovel))
        {
            $erro = new RelatorioCargaXml();
            $erro->resultado_id = $resultadoCargaId;
            $erro->anunciante_id = $anuncianteId;
            $erro->mensagem="Tipo de Imóvel não encontrado";
            $erro->code_error=2;
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();            
            return  0;
        }
        $tipoAnuncio = $this->tipoAnuncio($data);
        $adId = SfAnuncio::getAnunciosByProperties($anuncianteId,'-'.$data->CodigoImovel);
        if(!$adId){
            $anuncio = new SfAnuncio();
            $anuncio->data_cadastro = date('Y-m-d H:i:s');
            $anuncio->data_aprovado = date('Y-m-d H:i:s');
        }else{
            $anuncio = SfAnuncio::find($adId);
        }        
        $anuncio->status = 1;
        $anuncio->banheiros = (int) $data->QtdBanheiros;
        $anuncio->flag_exclusao = 0;
        $anuncio->flag_anunciar = 1;
        $anuncio->id_imovel_integracao = '-'.$data->CodigoImovel;
        $anuncio->titulo = $this->removeChar($data->TipoImovel. ' em '. $nomeCidade);
        $anuncio->anunciante_id = $anuncianteId;
        $anuncio->tipo_imovel_id = $tipoImovel;
        $anuncio->estado_id = $idEstado;
        $anuncio->cidade_id = $idCidade;
        $anuncio->bairro = $data->Bairro;
        $anuncio->logradouro = $data->Endereco;
        $anuncio->numero = $data->Numero;
        $anuncio->complemento = $data->Complemento;
        
        if(isset($data->CEP) || $data->CEP != ''){
            $anuncio->cep = $this->mask('##.###-###', strval($data->CEP));
        }
        
        if(isset($data->Cep) || $data->Cep != ''){
            $anuncio->cep = $this->mask('##.###-###', strval($data->Cep));            
        }        
        
        if(isset($data->cep) || $data->cep != ''){
            $anuncio->cep = $this->mask('##.###-###', strval($data->cep));
        }

        $anuncio->tipo_anuncio_id = $tipoAnuncio->id;
        $anuncio->identificador = date('Ymdhis');
        $anuncio->valor= $this->formatarValorAnuncio($tipoAnuncio->valor);
        $anuncio->descricao = $this->removeChar($data->Observacao);
        $anuncio->condominio= $this->formatarValorAnuncio($data->PrecoCondominio);
        
        if(!isset($data->AreaUtil) || $data->AreaUtil == '' || $data->AreaUtil == '0' || $data->AreaUtil == 0){
            if(!isset($data->AreaTotal) || $data->AreaTotal == '' || $data->AreaTotal == '0' || $data->AreaTotal == 0){
                $anuncio->area_util= $this->formatarAreaAnuncio($data->AreaTotal);
            }else{
                $anuncio->area_util= $data->AreaTotal;
            }
        }else{
            $anuncio->area_util= $data->AreaUtil;
        }
        
        if((int)$data->QtdDormitorios > 0){
            $anuncio->quartos = (int)$data->QtdDormitorios;
        }else{
            $anuncio->quartos = 1;
        }        

        $anuncio->suites_semar = (int)$data->QtdSuites;
        $anuncio->vagas = (int)$data->QtdVagas;
        $anuncio->elevador = (int)$data->QtdElevador;
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
            $this->RollbackAnuncio($anuncianteId);
            
            $erro = new RelatorioCargaXml();
            $erro->resultado_id = $resultadoCargaId;
            $erro->anunciante_id = $anuncianteId;
            // $erro->mensagem= 'Erro ao gravar o anúncio!';
            $erro->mensagem= $e->getMessage();
            $erro->code_error= 1;
            $erro->carga_imovel_id= $validaCamposObrigatorios->codigoImovel;
            $erro->save();


            //gravar log com  erro variavel e->getMessage();
            return  0;
        }
        return $anuncio->id;

    }
    public function salvarFotos($data,$id,$anuncianteId, $resultadoCargaId)
    {
        $codigoImovel = $data->CodigoImovel ;
        $basePath = env('URL_STORAGE', storage_path());
        $basePath = $basePath . "/" . $id . "/";
        if(is_dir($basePath)){
            $this->delete_directory($basePath);
        }
        SfAnuncioFoto::where('sf_anuncio_id', $id)->delete();

        for($i=0;$i<sizeof($data->Fotos->Foto);$i++)
        {
            $fileName = '';
            if (mb_strpos($data->Fotos->Foto[$i]->NomeArquivo, '.') !== false) {                
                $basePathSave = $id ."/" . $data->Fotos->Foto[$i]->NomeArquivo;
            }else{
                if($data->Fotos->Foto[$i]->NomeArquivo == '' || is_null($data->Fotos->Foto[$i]->NomeArquivo)){
                    $fileName = $this->extrairNomeFotoUrl($data->Fotos->Foto[$i]->URLArquivo);
                }else{
                    $fileName = $this->nomeFoto($data->Fotos->Foto[$i]->URLArquivo, $data->Fotos->Foto[$i]->NomeArquivo);
                }
                $basePathSave = $id ."/" . $fileName;
            }
            
            try
            {
                $conteudoImg = file_get_contents($data->Fotos->Foto[$i]->URLArquivo);
                
                Storage::disk('custom')->put($basePathSave, $conteudoImg);
                
                $anuncioFoto = new SfAnuncioFoto();
                $anuncioFoto->sf_anuncio_id = $id ;
                $anuncioFoto->imagem = $basePathSave;
                
                if($data->Fotos->Foto[$i]->Principal){                    
                    $anuncioFoto->principal = $data->Fotos->Foto[$i]->Principal;
                }else{
                    $anuncioFoto->principal = 0;
                }
                
                $anuncioFoto->texto = $fileName;
                $anuncioFoto->ordem = $i;
                $anuncioFoto->usr_alteracao = $anuncianteId;
                $anuncioFoto->save();
            }
            catch (\Exception $e)
            {
                $posicaoFoto = $i + 1;
                $erro = new RelatorioCargaXml();
                $erro->anunciante_id = $anuncianteId;
                $erro->resultado_id = $resultadoCargaId;
                $erro->mensagem = 'Erro ao capturar a imagem da posição '.$posicaoFoto. ' da tag Fotos do XML!';
                $erro->code_error=1;
                $erro->carga_imovel_id= $codigoImovel;
                $erro->save();
            }

            //aqui salvamos as imagens
        }
        $end = new RelatorioCargaXml();
        $end->resultado_id = $resultadoCargaId;
        $end->anunciante_id = $anuncianteId;
        $end->mensagem = 'Carga Finalizada';
        $end->code_error = 2;
        $end->carga_imovel_id= $codigoImovel;
        $end->save();
        return;
    }
    public function leituraXmlZap($anuncianteId = null)
    {
        ini_set('max_execution_time', 72000);
        if(isset($anuncianteId)){
            $data = AnuncianteCarga::where('anunciante_id', $anuncianteId)->get();
        }else{
            $data = AnuncianteCarga::where('flag_leitura', 1)->get();
        }
        $xml_conteudo ='';
        $retorno =0 ;
        for ($i=0;$i<sizeof($data);$i++)
        {
            SfAnuncio::where('anunciante_id', $data[$i]->anunciante_id)
                ->whereNotNull('id_imovel_integracao')
                ->update(['flag_exclusao' => 1]);
            
            // $anunciosAddPacote = SfAnuncio::where('flag_exclusao', 1)
            //     ->where('status', 1)->get();

            // $qtdAnunciosAddPacote = sizeof($anunciosAddPacote);

            // $infoPacote = AnuncianteProfissionalPacote::where('anunciante_id',  $data[$i]->anunciante_id)->first();
            // $infoPacote->unidades = $infoPacote->unidades + $qtdAnunciosAddPacote;
            // $infoPacote->save();

            try {                
                $xml_conteudo  = simplexml_load_file($data[$i]->url);
            } catch (\Exception $e) {
                continue;
            }

            $resultadoCarga = new ResultadoCargaXml();
            $resultadoCarga->anunciante_id = $data[$i]->anunciante_id;
            $resultadoCarga->qtd_recebidos = sizeof($xml_conteudo->Imoveis->Imovel);
            $resultadoCarga->save();
            
            $resultadoCargaId = $resultadoCarga->id;

            for ($j=0; $j<sizeof($xml_conteudo->Imoveis->Imovel);$j++)
            // for ($j=3; $j<5;$j++)
            {
                $statusAnuncio =$this->statusAnuncio($data[$i]->anunciante_id);
                $retorno = $this->salvarDadosXmlZap($xml_conteudo->Imoveis->Imovel[$j],$statusAnuncio, $data[$i]->anunciante_id, $resultadoCargaId);
                if($retorno >0)
                {
                    $salvarFotos = $this->salvarFotos($xml_conteudo->Imoveis->Imovel[$j],$retorno,$data[$i]->anunciante_id, $resultadoCargaId);
                }
                else
                {
                    $this->RollbackAnuncio($data[$i]->anunciante_id);
                }
            }

            // $anunciosDelecao = SfAnuncio::where('flag_exclusao', 1)->get();
            // $this->deletarFotosExcluidos($anunciosDelecao);
            

            // SfAnuncio::where('anunciante_id', $data[$i]->anunciante_id)
            //     ->whereNotNull('id_imovel_integracao')
            //     ->update(['flag_exclusao' => 1]);
            
            $affected = SfAnuncio::where('flag_exclusao', 1)
            ->where('anunciante_id', $data[$i]->anunciante_id)
            ->update(['status' => '-2']);

            SfAnuncio::where('flag_exclusao', 1)
            ->where('anunciante_id', $data[$i]->anunciante_id)
            ->update(['flag_exclusao' => 0]);

            if(is_null($affected)){
                $affected = 0;
            }

            $resultadoCargaAdd = ResultadoCargaXml::find($resultadoCargaId);
            $resultadoCargaAdd->qtd_excluidos = $affected;
            $resultadoCargaAdd->save();
            
            $bodyRequestMail['sellf']['sellfMail'] = getenv('SELLF_MAIL');
            $bodyRequestMail['sellf']['sellfMailPass'] = getenv('SELLF_MAIL_PASS');            
            $bodyRequestMail['advertiser'] = SfAnuncio::getAnuncianteDataById($data[$i]->anunciante_id);
            $bodyRequestMail['data'] = ResultadoCargaXml::find($resultadoCargaId);
            $bodyRequestMail['data']['errors'] = RelatorioCargaXml::getDetalhesCargaByResultadoId($resultadoCargaId);

            $mail = new MailService();
            $sendMail = $mail->sendReportMail($bodyRequestMail);
        }
        return response()->json('Success', 200);
    }

    public function deletarFotosExcluidos($anuncios){
        $arrAnunciosId = [];

        foreach ($anuncios as $a) {
           array_push($arrAnunciosId, $a->id);
        }

        
        for ($i=0; $i < sizeof($arrAnunciosId); $i++) { 
            SfAnuncioFoto::where('sf_anuncio_id', $arrAnunciosId[$i])->delete();
            $basePath = env('URL_STORAGE', storage_path());
            $basePath = $basePath . "/" . $arrAnunciosId[$i] . "";
            $this->delete_directory($basePath);            
        }

    }

    public function mask($mask, $str){
        if($str == '' || is_null($str)){
            return '';
        }
        $str = str_replace(" ","",$str);
        
        $str = str_replace('-', '', $str);
        
        $str = str_replace('.', '', $str);

        
        for($i=0;$i<strlen($str);$i++){
            $mask[strpos($mask,"#")] = $str[$i];
        }
    
        return $mask;
    
    }

    public function removeChar($str){        
        $str = str_replace("&nbsp;", "", $str);
        $str = str_replace("&;", "e", $str);
        $str = str_replace("&", "e", $str);
        $str = str_replace("✓", "", $str);
        $str = str_replace("*", "", $str);
        $str = str_replace("❖", "", $str);
        $str = str_replace("", "", $str);        
        $str = str_replace("#", "", $str);        
        $str = str_replace("•", "", $str);        
        $str = str_replace("", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("eaacute;", "á", $str);
        $str = str_replace("&aacute;", "á", $str);
        $str = str_replace("&atilde;", "ã", $str);
        $str = str_replace("eatilde;", "ã", $str);
        $str = str_replace("&auml;", "ä", $str);
        $str = str_replace("eauml;", "ä", $str);
        $str = str_replace("eagrave;", "à", $str);
        $str = str_replace("&agrave;", "à", $str);
        $str = str_replace("&acirc;", "â", $str);
        $str = str_replace("eacirc;", "â", $str);
        
        $str = str_replace("eAacute;", "Á", $str);
        $str = str_replace("&Aacute;", "Á", $str);
        $str = str_replace("&Atilde;", "Ã", $str);
        $str = str_replace("eAtilde;", "Ã", $str);
        $str = str_replace("&Auml;", "Ä", $str);
        $str = str_replace("eAuml;", "Ä", $str);
        $str = str_replace("eAgrave;", "À", $str);
        $str = str_replace("&Agrave;", "À", $str);
        $str = str_replace("&Agrave;", "À", $str);
        $str = str_replace("&Acirc;", "Â", $str);
        $str = str_replace("eAcirc;", "Â", $str);
        
        $str = str_replace("eeacute;", "é", $str);
        $str = str_replace("&eacute;", "é", $str);
        $str = str_replace("&euml;", "ë", $str);
        $str = str_replace("eeuml;", "ë", $str);
        $str = str_replace("eeuml;", "ë", $str);
        $str = str_replace("eegrave;", "è", $str);
        $str = str_replace("&egrave;", "è", $str);
        $str = str_replace("&egrave;", "è", $str);
        $str = str_replace("&ecirc;", "ê", $str);
        $str = str_replace("&eecirc;", "ê", $str);
        
        $str = str_replace("eEacute;", "É", $str);
        $str = str_replace("&Eacute;", "É", $str);
        $str = str_replace("&Euml;", "Ë", $str);
        $str = str_replace("eEuml;", "Ë", $str);
        $str = str_replace("eEgrave;", "È", $str);
        $str = str_replace("&Egrave;", "È", $str);
        $str = str_replace("&Ecirc;", "Ê", $str);
        $str = str_replace("eEcirc;", "Ê", $str);
        
        $str = str_replace("eiacute;", "í", $str);
        $str = str_replace("&iacute;", "í", $str);
        $str = str_replace("&iacute;", "í", $str);
        $str = str_replace("&iuml;", "ï", $str);
        $str = str_replace("eiuml;", "ï", $str);
        $str = str_replace("eigrave;", "ì", $str);
        $str = str_replace("&igrave;", "ì", $str);
        $str = str_replace("eicirc;", "î", $str);
        $str = str_replace("&icirc;", "î", $str);
        
        $str = str_replace("eIacute;", "Í", $str);
        $str = str_replace("&Iacute;", "Í", $str);
        $str = str_replace("&Iuml;", "Ï", $str);
        $str = str_replace("eIuml;", "Ï", $str);
        $str = str_replace("eIgrave;", "Ì", $str);
        $str = str_replace("&Igrave;", "Ì", $str);
        $str = str_replace("&Icirc;", "Î", $str);
        $str = str_replace("eIcirc;", "Î", $str);
        
        $str = str_replace("eoacute;", "ó", $str);
        $str = str_replace("&oacute;", "ó", $str);
        $str = str_replace("&ouml;", "ö", $str);
        $str = str_replace("eouml;", "ö", $str);
        $str = str_replace("eograve;", "ò", $str);
        $str = str_replace("&ograve;", "ò", $str);
        $str = str_replace("&ocirc;", "ô", $str);
        $str = str_replace("eocirc;", "ô", $str);
        $str = str_replace("&otilde;", "õ", $str);
        $str = str_replace("eotilde;", "õ", $str);
        
        $str = str_replace("eOacute;", "Ó", $str);
        $str = str_replace("&Oacute;", "Ó", $str);
        $str = str_replace("&Ouml;", "Ö", $str);
        $str = str_replace("eOuml;", "Ö", $str);
        $str = str_replace("eOgrave;", "Ò", $str);
        $str = str_replace("&Ograve;", "Ò", $str);
        $str = str_replace("&Ocirc;", "Ô", $str);
        $str = str_replace("eOcirc;", "Ô", $str);
        $str = str_replace("&Otilde;", "Õ", $str);
        $str = str_replace("eOtilde;", "Õ", $str);
        
        $str = str_replace("euacute;", "ú", $str);
        $str = str_replace("&uacute;", "ú", $str);
        $str = str_replace("&uuml;", "ü", $str);
        $str = str_replace("euuml;", "ü", $str);
        $str = str_replace("eugrave;", "ù", $str);
        $str = str_replace("&ugrave;", "ù", $str);
        $str = str_replace("&ucirc;", "û", $str);
        $str = str_replace("eucirc;", "û", $str);
        
        $str = str_replace("eUacute;", "Ú", $str);
        $str = str_replace("&Uacute;", "Ú", $str);
        $str = str_replace("&Uuml;", "Ü", $str);
        $str = str_replace("eUuml;", "Ü", $str);
        $str = str_replace("eUgrave;", "Ù", $str);
        $str = str_replace("&Ugrave;", "Ù", $str);
        $str = str_replace("&Ucirc;", "Û", $str);
        $str = str_replace("eUcirc;", "Û", $str);
        
        $str = str_replace("&ntilde;", "ñ", $str);
        $str = str_replace("entilde;", "ñ", $str);
        
        $str = str_replace("&Ntilde;", "Ñ", $str);
        $str = str_replace("eNtilde;", "Ñ", $str);
        
        $str = str_replace("eccedil;", "ç", $str);
        $str = str_replace("&ccedil;", "ç", $str);
        
        $str = str_replace("eCcedil;", "Ç", $str);
        $str = str_replace("&Ccedil;", "Ç", $str);

        return $str;
    }

    public function removerAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
    }

    public function nomeFoto($url, $nomeArquivo){
        $urlParsed = parse_url($url);
        $pathParts = pathinfo($urlParsed['path']);
        $extension = $pathParts['extension'];
        return $nomeArquivo.'.'.$extension;
    }

    public function extrairNomeFotoUrl($url){
        $nomeArquivo = parse_url($url);
        $nomeArquivo = pathinfo($nomeArquivo['path']);
        $nomeArquivo = $nomeArquivo['filename'].'.'.$nomeArquivo['extension'];
        return $nomeArquivo;
    }

    public function delete_directory($dirname) {
        $dir_handle = false;
        if (is_dir($dirname)){
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle){
            return false;
        }
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    unlink($dirname."/".$file);
                else
                    $this->delete_directory($dirname.'/'.$file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function formatarValorAnuncio($valor){
        $valor = str_replace('.', '', $valor);
        $valor = explode(',', $valor);
        $valor = $valor[0];
        if($valor == ''){
            $valor = 0;
        }
        return $valor;
    }
    
    public function formatarAreaAnuncio($valor){
        return preg_replace("/[^0-9]/", "", $valor);
    }


}