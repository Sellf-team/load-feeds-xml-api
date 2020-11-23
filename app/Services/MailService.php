<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class MailService{

    public function sendReportMail($bodyRequestMail){
        try {
            $client = new Client();

            $send = $client->post(getenv('URL_REPORT_MAIL_API'), [
                RequestOptions::JSON => $bodyRequestMail
            ]);

            $data = [];
            
            $json = json_decode($send->getBody()->getContents(), true); 
            if($json['statusCode'] == 200){
                $data = [
                    'message'=> $json['message'],
                    'statusCode' => $json['statusCode']
                ]; 
            }else{
                throw new Exception("Error on send mail API call!");
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error on send mail API call! | ' .$e->getMessage(). '. Line: ' . $e->getLine(),
                'statusCode'=> 500
            ];
        }
        return $data;
    }
    
}