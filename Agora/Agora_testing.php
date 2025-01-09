<?php
namespace App\Agora;

class Agora
{
    public static function Create ($name)
    {
        $customerKey = env('AGORA_CUSTOMER_KEY');
        $secretKey = env('AGORA_SECRET_KEY');

        $credentials = "{$customerKey}:{$secretKey}";

        $base64 = base64_encode($credentials);

        $header = "Authorization: Basic {$base64}";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.agora.io/dev/v1/project",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"name": "'.$name.'", "enable_sign_key": true}',
            CURLOPT_HTTPHEADER => [
                $header, 'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
