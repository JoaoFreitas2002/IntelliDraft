<?php

defined('ABSPATH') || exit;

use Orhanerday\OpenAi\OpenAi;

final class IntelliDraft_CGPT_Api
{
    private $api_key;
    private $open_ai;
    private $model;

    public function __construct()
    {
        $options = get_option('intellidraft_api_settings');
        $this->api_key = isset($options['intellidraft_cgpt_api_key']) ? IntelliDraft_CGPT_Api::api_key_decrypt($options['intellidraft_cgpt_api_key']) : '';
        $this->model = isset($options['intellidraft_cgpt_model']) ? $options['intellidraft_cgpt_model'] : '';
        $this->startAPI();
    }

    public static function api_key_decrypt($api_key)
    {
        $encryption_key = hash('sha256', 'secret');
        $iv = substr($encryption_key, 0, 16);
        return openssl_decrypt(base64_decode($api_key), 'AES-256-CBC', $encryption_key, 0, $iv);
    }

    public static function api_key_encrypt($api_key)
    {
        $encryption_key = hash('sha256', 'secret');
        $iv = substr($encryption_key, 0, 16);
        return base64_encode(openssl_encrypt($api_key, 'AES-256-CBC', $encryption_key, 0, $iv));
    }

    private function startAPI()
    {
        if (!empty($this->api_key)) {
            $this->open_ai = new OpenAi($this->api_key);
        }
    }

    public function get_models()
    {
        if ($this->open_ai != null) {
            $models = $this->open_ai->listModels();
        } else {
            $models = '{"object": "list","data": []}';
        }

        $models = json_decode($models);
        return $models;
    }

    public function generate_content($prompt)
    {

        $chat = $this->open_ai->chat([
            'model' => $this->model,
            'messages' => [
                [
                    "role" => "user",
                    "content" => $prompt
                ]
            ],
            //'max_tokens' => 500,
            'temperature' => 0.7,
        ]);

        $response_data = json_decode($chat);
        return $response_data;
    }
}
