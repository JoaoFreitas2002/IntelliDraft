<div class="wrap">
    <h2>IntelliWriter</h2>
    <br>
    <h2 class="nav-tab-wrapper">
        <a href="javascript:void(0);" id="tab-general" class="nav-tab nav-tab-active">General</a>
        <a href="javascript:void(0);" id="tab-chatgpt" class="nav-tab">ChatGPT</a>
    </h2>
    <form action="options.php" method="post">
        <?php settings_fields('intelliwriter_settings_group'); ?>
        <div id="content-general" class="tab-content active">
            <?php
            do_settings_sections('intelliwriter');
            ?>
        </div>
        <div id="content-chatgpt" class="tab-content">
            <p>Visit <a href="https://platform.openai.com/account/api-keys" target="_blank">platform.openai.com/account/api-keys</a> to get your API Key.</p>
            <?php
            do_settings_sections('intelliwriter_api_cgpt');
            ?>
        </div>
        <?php
        submit_button();
        ?>
    </form>
    <h2>Support this Plugin</h2>
    <p>If you find this plugin useful, please consider making a donation to support its development and maintenance.</p>
    <form action="https://www.paypal.com/donate" method="post" target="_blank">
        <input type="hidden" name="hosted_button_id" value="PYMR2NFEHADAN" />
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
        <img alt="" border="0" src="https://www.paypal.com/en_PT/i/scr/pixel.gif" width="1" height="1" />
    </form>
</div>