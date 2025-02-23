<?php

defined('ABSPATH') || exit;

require_once __DIR__ . '/block-converter.php';

class IntelliDraft_Generate_Post
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function add_admin_menu()
    {
        $post_types = get_post_types(array('public' => true));
        $excluded_post_types = array('', 'elementor_library');

        foreach ($post_types as $post_type) {

            if (in_array($post_type, $excluded_post_types)) {
                continue;
            }

            $parent_slug = ($post_type === 'post') ? 'edit.php' : 'edit.php?post_type=' . $post_type;
            add_submenu_page(
                $parent_slug,
                'Generate New',
                'Generate New',
                'read',
                'intellidraft_content_generator_' . $post_type,
                array($this, 'create_post_page'),
                2
            );
        }
    }

    public function create_post_page()
    {
        include plugin_dir_path(__FILE__) . '/../views/generate-post-page.php';
    }

    public function enqueue_admin_assets($hook)
    {
        if (strpos($hook, 'intellidraft_content_generator') !== false) {

            wp_enqueue_style('intellidraft-generate-post', plugin_dir_url(__FILE__) . '../../assets/css/generate-post.css', null, '1.0.0');

            wp_enqueue_script('intellidraft-generate-post', plugin_dir_url(__FILE__) . '../../assets/js/generate-post.js', null, '1.0.0', true);

            wp_localize_script('intellidraft-generate-post', 'intellidraft', [
                'rest_url' => rest_url('intellidraft/v1'),
                'nonce' => wp_create_nonce('wp_rest'),
                'post_type' => str_replace('intellidraft_content_generator_', '', sanitize_key($_GET['page'])),
            ]);
        }
    }

    public function register_rest_routes()
    {
        // Endpoint to generate content
        register_rest_route('intellidraft/v1', '/generate', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_content'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'topics' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'language' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_key',
                ],
                'style' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_key',
                ],
                'tone' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_key',
                ],
            ],
        ]);

        // Endpoint to create post
        register_rest_route('intellidraft/v1', '/create-post', [
            'methods' => 'POST',
            'callback' => [$this, 'create_post'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'title' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'content' => [
                    'required' => true,
                    'sanitize_callback' => 'wp_kses_post',
                ],
                'excerpt' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_textarea_field',
                ],
                'post_type' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_key',
                ],
                'post_status' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }

    /**
     * Replace variables in a prompt with provided values, case-insensitively.
     *
     * @param string $prompt The prompt string with placeholders.
     * @param array $values Associative array of variable names and their values.
     * @return string The prompt with all variables replaced.
     */
    private function replace_variables($prompt, $values)
    {
        return preg_replace_callback(
            '/\{\{([a-zA-Z_]+)\}\}/i',
            function ($matches) use ($values) {
                $key = strtolower($matches[1]);
                return isset($values[$key]) ? $values[$key] : $matches[0]; // Keep original if not found
            },
            $prompt
        );
    }

    public function generate_content(WP_REST_Request $request)
    {
        $api = new IntelliDraft_CGPT_Api();
        if (!$api->is_initialized()) {
            return new WP_Error('api_error', 'ChatGPT API not initialized. Set your API key in settings.', ['status' => 400]);
        }

        if (get_option('intellidraft_title_prompt', '') == '' or get_option('intellidraft_content_prompt', '') == '' or get_option('intellidraft_excerpt_prompt', '') == '') {
            return new WP_Error('api_error', 'Some prompts are empty! Set your prompts in settings.', ['status' => 400]);
        }

        // Extract parameters
        $params = [
            'topics' => $request->get_param('topics'),
            'language' => $request->get_param('language'),
            'writing_style' => $request->get_param('style'),
            'writing_tone' => $request->get_param('tone'),
            'post_type' => $request->get_param('post_type'),
        ];

        // Get prompts with fallbacks
        $prompts = [
            'title' => get_option('intellidraft_title_prompt', ''),
            'content' => get_option('intellidraft_content_prompt', ''),
            'excerpt' => get_option('intellidraft_excerpt_prompt', ''),
        ];

        //title
        $title_prompt = get_option('intellidraft_title_prompt', '');
        $title_prompt = $this->replace_variables($prompts['title'], $params);
        $title = $api->generate_chat([['role' => 'user', 'content' => $title_prompt]]);
        if (!$title) {
            return new WP_Error('generation_failed', 'Failed to generate title.', ['status' => 500]);
        }

        // Add title to params for content and excerpt
        $params['title'] = $title;

        // Generate content
        // Always append this Markdown text to the content prompt
        $append_text = "Format the entire response in Markdown. Do not include the title in the response body.";
        $content_prompt = $this->replace_variables($prompts['content'], $params) . $append_text;
        $content = $api->generate_chat([['role' => 'user', 'content' => $content_prompt]]);

        // Generate excerpt
        $excerpt_prompt = $this->replace_variables($prompts['excerpt'], $params);
        $excerpt = $api->generate_chat([['role' => 'user', 'content' => $excerpt_prompt]]);

        return rest_ensure_response([
            'title' => $title,
            'content' => $content,
            'excerpt' => $excerpt,
        ]);
    }

    public function create_post(WP_REST_Request $request)
    {

        $parsedown = new Parsedown();
        $converter = new \Tyme\BlockConverter();

        $title = $request->get_param('title');
        $content = $request->get_param('content');
        $excerpt = $request->get_param('excerpt');
        $post_type = $request->get_param('post_type');
        $post_status = $request->get_param('post_status');

        // Convert Markdown to HTML
        $content_html = $parsedown->text($content);
        $content_html = $this->unwrap_images_from_paragraphs($content_html);
        $post_content = $content_html; // Default to HTML

        // Check if Gutenberg (block editor) is active
        $is_block_editor = $this->is_block_editor_active($post_type);
        if ($is_block_editor) {
            try {
                $block_markup = $converter->convert_blocks($content_html);
                $post_content = $block_markup;
            } catch (Exception $e) {
                error_log('IntelliDraft_Generate_Post: Error converting to Gutenberg blocks - ' . $e->getMessage());
                $post_content = "<!-- wp:html -->\n$content_html\n<!-- /wp:html -->"; // Fallback to HTML block
            }
        }

        $post_data = [
            'post_title' => $title,
            'post_content' => $post_content,
            'post_excerpt' => $excerpt,
            'post_status' => $post_status ?: 'draft',
            'post_type' => $post_type,
        ];

        $post_id = wp_insert_post($post_data);
        if ($post_id) {
            return rest_ensure_response(['edit_link' => get_edit_post_link($post_id)]);
        } else {
            return new WP_Error('create_failed', 'Failed to create post.', ['status' => 500]);
        }
    }

    /**
     * Remove <p> wrappers from <img> tags in Parsedown HTML output
     *
     * @param string $html HTML content from Parsedown
     * @return string Adjusted HTML with unwrapped <img> tags
     */
    private function unwrap_images_from_paragraphs($html)
    {
        // Match <p> tags containing only an <img> tag (with optional whitespace)
        $pattern = '/<p>\s*(<img[^>]+>)\s*<\/p>/i';
        $html = preg_replace($pattern, '$1', $html);
        return $html;
    }

    /**
     * Check if the block editor (Gutenberg) is active for the given post type
     *
     * @param string $post_type The post type to check
     * @return bool True if block editor is active, false if Classic Editor
     */
    private function is_block_editor_active($post_type)
    {
        // Check if block editor is globally enabled (WordPress 5.0+)
        if (!function_exists('use_block_editor_for_post_type')) {
            return false; // Pre-WordPress 5.0, no block editor
        }

        // Check if block editor is enabled for this post type
        $is_block_editor = use_block_editor_for_post_type($post_type);

        // Check if Classic Editor plugin is active and forcing classic editor
        if (class_exists('Classic_Editor')) {
            $editor_setting = get_option('classic-editor-replace', 'classic');
            if ($editor_setting === 'classic') {
                $is_block_editor = false; // Classic Editor plugin forcing classic globally
            } elseif ($editor_setting === 'block') {
                $is_block_editor = true; // Classic Editor plugin forcing block globally
            } else {
                // 'no-replace' mode: check per-post or default to post type setting
                $is_block_editor = $is_block_editor && !get_post_meta($post_id ?? 0, 'classic-editor-remember', true);
            }
        }

        return $is_block_editor;
    }
}
