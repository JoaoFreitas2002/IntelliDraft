<?php
if (!defined('ABSPATH')) {
    exit;
}

$integration = get_option('intellidraft_integration', 'chatgpt');
$title_prompt = get_option('intellidraft_title_prompt', '');
$content_prompt = get_option('intellidraft_content_prompt', '');
$excerpt_prompt = get_option('intellidraft_excerpt_prompt', '');
$cgpt_api_key = get_option('intellidraft_cgpt_api_key', '');
$cgpt_model = get_option('intellidraft_cgpt_model', 'gpt-3.5-turbo');
$cgpt_temperature = get_option('intellidraft_cgpt_temperature', '0.7');
$cgpt_max_tokens = get_option('intellidraft_cgpt_max_tokens', '2048');
$chatgpt = new IntelliDraft_CGPT_Api();
$models = $chatgpt->get_models();
?>

<div class="wrap" id="intellidraft-settings">
    <h2>IntelliDraft</h2>
    <h2 class="nav-tab-wrapper">
        <a href="" class="nav-tab nav-tab-active" data-tab="general-settings">General</a>
        <a href="" class="nav-tab" data-tab="chatgpt-settings">Chat GPT</a>
    </h2>
    <div>
        <form method="post" action="options.php">
            <?php settings_fields('intellidraft_settings'); ?>

            <!-- General Tab -->
            <div id="general-settings" class="tab-content active">
                <div class="rows">
                    <label for="intellidraft_integration">Integration</label>
                    <select name="intellidraft_integration" id="intellidraft_integration">
                        <option value="chatgpt" <?php selected($integration, 'chatgpt'); ?>>ChatGPT</option>
                    </select>
                </div>
                <div class="rows">
                    <label for="intellidraft_title_prompt">Title Prompt</label>
                    <div class="prompts">
                        <textarea class="input-wide" name="intellidraft_title_prompt" id="intellidraft_title_prompt" rows="4" cols="50"><?php echo esc_textarea($title_prompt); ?></textarea>
                        <span class="intellidraft-tooltip-btn" title="Prompts represent the exact request sent to the AI. The variables between curly braces will be replaced by the content of the corresponding field. Prompts are saved in the database.">?</span>
                    </div>
                </div>
                <div class="rows">
                    <label for="intellidraft_content_prompt">Content Prompt</label>
                    <div class="prompts">
                        <textarea class="input-wide" name="intellidraft_content_prompt" id="intellidraft_content_prompt" rows="4" cols="50"><?php echo esc_textarea($content_prompt); ?></textarea>
                        <span class="intellidraft-tooltip-btn" title="Prompts represent the exact request sent to the AI. The variables between curly braces will be replaced by the content of the corresponding field. Prompts are saved in the database.">?</span>
                    </div>
                </div>
                <div class="rows">
                    <label for="intellidraft_excerpt_prompt">Excerpt Prompt</label>
                    <div class="prompts">
                        <textarea class="input-wide" name="intellidraft_excerpt_prompt" id="intellidraft_excerpt_prompt" rows="4" cols="50"><?php echo esc_textarea($excerpt_prompt); ?></textarea>
                        <span class="intellidraft-tooltip-btn" title="Prompts represent the exact request sent to the AI. The variables between curly braces will be replaced by the content of the corresponding field. Prompts are saved in the database.">?</span>
                    </div>
                </div>
            </div>

            <!-- ChatGPT Tab -->
            <div id="chatgpt-settings" class="tab-content">
                <div class="rows">
                    <label for="intellidraft_cgpt_api_key">API Key</label>
                    <input type="text" name="intellidraft_cgpt_api_key" id="intellidraft_cgpt_api_key" value="<?php echo esc_attr($cgpt_api_key); ?>" class="input-wide" />
                </div>
                <div class="rows">
                    <label for="intellidraft_cgpt_model">Model</label>
                    <select name="intellidraft_cgpt_model" id="intellidraft_cgpt_model">
                        <?php if ($models): ?>
                            <?php foreach ($models as $model): ?>
                                <option value="<?php echo esc_attr($model['id']); ?>" <?php selected($cgpt_model, $model['id']); ?>>
                                    <?php echo esc_html($model['id']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (empty($cgpt_api_key)): ?>
                        <p class="description">Enter your API key and save to fetch available models.</p>
                    <?php endif; ?>
                </div>
                <div class="rows">
                    <label for="intellidraft_cgpt_temperature">Temperature</label>
                    <input type="number" step="0.1" min="0" max="1" name="intellidraft_cgpt_temperature" id="intellidraft_cgpt_temperature" value="<?php echo esc_attr($cgpt_temperature); ?>" />
                </div>
                <div class="rows">
                    <label for="intellidraft_cgpt_max_tokens">Max Tokens</label>
                    <input type="number" min="1" name="intellidraft_cgpt_max_tokens" id="intellidraft_cgpt_max_tokens" value="<?php echo esc_attr($cgpt_max_tokens); ?>" />
                </div>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tab');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                tabs.forEach(t => t.classList.remove('nav-tab-active'));
                contents.forEach(c => c.classList.remove('active'));

                tab.classList.add('nav-tab-active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    });
</script>