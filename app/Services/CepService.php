<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;

class CepService{
    function validateCep($cep){
        $cep = utf8_encode($cep);
        $cep = trim($cep);        
        $cep = str_replace(' ', '', $cep);
        $cep = str_replace('_', '', $cep);
        $cep = str_replace('/', '', $cep);
        $cep = str_replace('-', '', $cep);
        $cep = str_replace('(', '', $cep);
        $cep = str_replace(')', '', $cep);
        $cep = str_replace('.', '', $cep);        
        $client = new Client();
        $url = 'https://viacep.com.br/ws/'.$cep.'/json/unicode/';            
        try {
            $send = $client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'     => '*/*'
                ]
            ]);    
            
            if($send->getStatusCode() == 200){
                $json = json_decode($send->getBody()->getContents(), true); 
                if(isset($json['erro'])){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        } catch (Exception $e) {
            return true;
        }
        

        if(isset($json['erro'])){
            return false;
        }else{
            return true;
        }

    }

    function getCityCep($cep){
        $cep = utf8_encode($cep);
        $cep = trim($cep);        
        $cep = str_replace(' ', '', $cep);
        $cep = str_replace('_', '', $cep);
        $cep = str_replace('/', '', $cep);
        $cep = str_replace('-', '', $cep);
        $cep = str_replace('(', '', $cep);
        $cep = str_replace(')', '', $cep);
        $cep = str_replace('.', '', $cep);        
        $client = new Client();
        $url = 'https://viacep.com.br/ws/'.$cep.'/json/unicode/';            
        try {
            $send = $client->get($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept'     => '*/*'
                ]
            ]);    
            
            if($send->getStatusCode() == 200){
                $json = json_decode($send->getBody()->getContents(), true); 
                if(isset($json['erro'])){
                    return false;
                }else{
                    return $json['localidade'];
                }
            }else{
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        

        if(isset($json['erro'])){
            return false;
        }else{
            return true;
        }
    }

}