<?php

defined('ABSPATH') || exit;

use Orhanerday\OpenAi\OpenAi;

final class IntelliDraft_CGPT_Api
{
    private $client;
    private $config;

    // Default configuration
    private const DEFAULT_CONFIG = [
        'api_key_option' => 'intellidraft_cgpt_api_key',
        'model_option' => 'intellidraft_cgpt_model',
        'temperature_option' => 'intellidraft_cgpt_temperature',
        'max_tokens_option' => 'intellidraft_cgpt_max_tokens',
        'default_model' => '',
        'default_temperature' => 0.7,
        'default_max_tokens' => 2048,
    ];

    /**
     * Constructor with optional custom config
     *
     * @param array $config Custom configuration overrides
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge(self::DEFAULT_CONFIG, $config);

        $api_key = get_option($this->config['api_key_option'], '');
        if (!empty($api_key)) {
            // Ensure library is loaded
            if (!class_exists('Orhanerday\OpenAi\OpenAi')) {
                throw new RuntimeException('OpenAI library not found. Ensure composer autoload is included.');
            }
            $this->client = new OpenAi($api_key);
        }

        $this->load_config();
    }

    /**
     * Load configuration from WordPress options
     */
    private function load_config(): void
    {
        $this->config['api_key'] = get_option($this->config['api_key_option'], '');
        $this->config['model'] = get_option($this->config['model_option'], $this->config['default_model']);
        $this->config['temperature'] = floatval(get_option($this->config['temperature_option'], $this->config['default_temperature']));
        $this->config['max_tokens'] = intval(get_option($this->config['max_tokens_option'], $this->config['default_max_tokens']));
    }

    /**
     * Check if the API client is initialized
     *
     * @return bool
     */
    public function is_initialized(): bool
    {
        return !empty($this->config['api_key']) && $this->client instanceof OpenAi;
    }

    /**
     * Get available models from ChatGPT API with caching
     *
     * @return array|null
     */
    public function get_models(): ?array
    {
        if (!$this->is_initialized()) {
            return null;
        }

        $transient_key = 'intellidraft_cgpt_models';
        $cached_models = get_transient($transient_key);
        if ($cached_models !== false) {
            return $cached_models;
        }

        try {
            $response = $this->client->listModels();
            $data = json_decode($response, true);
            $models = $data['data'] ?? [];
            set_transient($transient_key, $models, HOUR_IN_SECONDS); // Cache for 1 hour
            return $models;
        } catch (Exception $e) {
            error_log('IntelliDraft_CGPT_Api: Error fetching models - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate chat response using ChatGPT API
     *
     * @param array $messages Array of message objects [{'role' => 'user', 'content' => 'text'}]
     * @param array $options Optional overrides for model, temperature, max_tokens, etc.
     * @return string|null
     */
    public function generate_chat(array $messages, array $options = []): ?string
    {
        if (!$this->is_initialized()) {
            return null;
        }

        $params = $this->merge_params($options, [
            'messages' => $messages,
        ]);

        try {
            $response = $this->client->chat($params);
            $data = json_decode($response, true);
            $content = $data['choices'][0]['message']['content'] ?? null;
            // Remove leading and trailing quotes if present
            if ($content && str_starts_with($content, '"') && str_ends_with($content, '"')) {
                $content = substr($content, 1, -1);
            }
            return $content;
        } catch (Exception $e) {
            error_log('IntelliDraft_CGPT_Api: Error generating chat - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Merge default params with overrides
     *
     * @param array $overrides Custom parameters
     * @param array $additional Additional required parameters
     * @return array
     */
    private function merge_params(array $overrides, array $additional = []): array
    {
        return array_merge([
            'model' => $this->config['model'],
            'temperature' => $this->config['temperature'],
            'max_tokens' => $this->config['max_tokens'],
        ], $additional, array_filter($overrides, function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    /**
     * Get configuration value
     *
     * @param string $key Config key
     * @return mixed
     */
    public function get_config(string $key)
    {
        return $this->config[$key] ?? null;
    }

    /**
     * Set configuration value (for testing or runtime adjustments)
     *
     * @param string $key Config key
     * @param mixed $value New value
     */
    public function set_config(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Get the OpenAI client instance (for advanced usage)
     *
     * @return OpenAi|null
     */
    public function get_client(): ?OpenAi
    {
        return $this->client;
    }
}
