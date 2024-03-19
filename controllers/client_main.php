<?php

class ClientMain extends AppController
{
    public function index()
    {
        $company_id = Configure::get('Blesta.company_id');
        Loader::loadModels($this, ['Companies']);
        $private_key = $this->Companies->getSetting($company_id, 'event_stream.private_key');
        $allow_origin = $this->Companies->getSetting($company_id, 'event_stream.allow_origin');
        $user_data = json_encode([
            'client_id' => $this->Session->read('blesta_client_id'),
            'staff_id' => $this->Session->read('blesta_staff_id'),
            'time' => time(),
        ]);
        $headers = ['Content-Type: application/json'];
        if (!empty($allow_origin->value)) {
            $headers[] = 'Access-Control-Allow-Origin: ' . $allow_origin->value;
            $headers[] = 'Access-Control-Allow-Credentials: true';
            $headers[] = 'Access-Control-Allow-Methods: GET, OPTIONS';
        }
        if (!empty($private_key->value)) {
            $sign_result = openssl_sign($user_data, $signature, $private_key->value, OPENSSL_ALGO_SHA256);
            if ($sign_result && !empty($signature)) {
                $headers[] = 'X-Event-Stream-Signature: ' . base64_encode($signature);
            }
        }
        foreach ($headers as $header) {
            header($header);
        }
        echo($user_data);
        exit();
    }
}