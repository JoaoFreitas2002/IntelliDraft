<?php
$post_type = str_replace('intellidraft_content_generator_', '', sanitize_key($_GET['page']));
$post_type_object = get_post_type_object($post_type);
// $post_type = get_post_type_object(get_post_type());
?>

<div class="intellidraft">
    <h1 class="generate-post">Generate New <?php echo $post_type_object ? $post_type_object->labels->singular_name : 'Post'; ?></h1>
    <div class="container">
        <div class="column column-4">
            <div class="inner-container">
                <h3>Topics</h3>
                <textarea class="text-area" id="topic-input" placeholder="Enter topics (e.g., AI, Technology)"></textarea>
                <button class="full-width-button" id="generate-button">Generate</button>
                <span id="generate-loading" style="display: none;margin-top:10px">
                    <img src="<?php echo plugin_dir_url(__FILE__) . '../../assets/imgs/loading.svg'; ?>" alt="Loading" class="loading-gif"> Generating...
                </span>
            </div>
            <div class="inner-container content-params-container">
                <h3>Content Params</h3>
                <div class="content-params">
                    <label for="language">Language:</label>
                    <select id="language">
                        <option value="English">English</option>
                        <option value="Spanish">Spanish</option>
                        <option value="French">French</option>
                    </select>
                </div>
                <div class="content-params">
                    <label for="style">Writing Style:</label>
                    <select id="style">
                        <option value="Creative">Creative</option>
                        <option value="Technical">Technical</option>
                        <option value="Formal">Formal</option>
                    </select>
                </div>
                <div class="content-params">
                    <label for="tone">Writing Tone:</label>
                    <select id="tone">
                        <option value="Cheerful">Cheerful</option>
                        <option value="Serious">Serious</option>
                        <option value="Inspirational">Inspirational</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="column column-8">
            <h2 class="generate-post">Result</h2>
            <div class="inner-container">
                <div class="w-100">
                    <h3>Title</h3>
                    <input type="text" class="text-input" id="title">
                </div>
                <div class="w-100">
                    <h3>Excerpt</h3>
                    <textarea class="text-area" id="excerpt"></textarea>
                </div>
                <div class="w-100">
                    <h3>Content</h3>
                    <textarea class="text-area" id="content"></textarea>
                    <p class="form-info">You can modify the content before using "Create Post". Markdown is supported, and will be converted to HTML when the post is created.</p>
                </div>
                <div class="w-100">
                    <div class="content-params">
                        <label for="post-status">Post Status:</label>
                        <select id="post-status">
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                            <option value="pending">Pending Review</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <button class="full-width-button" id="create-button">Create <?php echo $post_type_object ? $post_type_object->labels->singular_name : 'Post'; ?></button>
                    <span id="create-loading" style="display: none;">
                        <img src="<?php echo plugin_dir_url(__FILE__) . '../../assets/imgs/loading.svg'; ?>" alt="Loading" class="loading-gif"> Saving...
                    </span>
                </div>
            </div>
            <div id="message"></div>
        </div>
    </div>
</div>