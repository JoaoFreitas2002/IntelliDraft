<?php

defined('ABSPATH') || exit;

use Orhanerday\OpenAi\OpenAi;

final class IntelliWriter_CGPT_Api
{

    private $api_key;
    private $open_ai;
    private $model;

    public function __construct()
    {
        $options = get_option('intelliwriter_api_settings');
        $this->api_key = isset($options['intelliwriter_cgpt_api_key']) ? $options['intelliwriter_cgpt_api_key'] : '';
        $this->model = isset($options['intelliwriter_cgpt_model']) ? $options['intelliwriter_cgpt_model'] : '';
        $this->startAPI();
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
            ]
        ]);

        $response = json_decode($chat);

        return ($response->choices[0]->message->content);
    }
}
