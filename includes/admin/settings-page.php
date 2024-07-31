<div class="wrap" id="intellidraft-settings">
    <h2>IntelliDraft</h2>
    <br>
    <form action="options.php" method="post">
        <?php settings_fields('intellidraft_settings_group'); ?>
        <div id="content-chatgpt" class="tab-content">
            <p>Visit <a href="https://platform.openai.com/account/api-keys" target="_blank">platform.openai.com/account/api-keys</a> to get your API Key.</p>
            <?php
            do_settings_sections('intellidraft_api_cgpt');
            ?>
        </div>
        <?php
        submit_button();
        ?>
    </form>
</div>