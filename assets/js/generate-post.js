document.addEventListener('DOMContentLoaded', function () {
    const generateButton = document.getElementById('generate-button');
    const createButton = document.getElementById('create-button');
    const topicInput = document.getElementById('topic-input');
    const languageSelect = document.getElementById('language');
    const styleSelect = document.getElementById('style');
    const toneSelect = document.getElementById('tone');
    const titleInput = document.getElementById('title');
    const excerptTextarea = document.getElementById('excerpt');
    const contentTextarea = document.getElementById('content');
    const postStatusSelect = document.getElementById('post-status');
    const generateLoading = document.getElementById('generate-loading');
    const createLoading = document.getElementById('create-loading');
    const messageDiv = document.getElementById('message');

    generateButton.addEventListener('click', function (e) {
        e.preventDefault();
        const topics = topicInput.value.trim();
        if (!topics) {
            showMessage('Please enter topics.', 'error');
            return;
        }

        generateLoading.style.display = 'inline';
        generateButton.disabled = true;
        messageDiv.innerHTML = '';

        const data = {
            topics: topics,
            language: languageSelect.value,
            style: styleSelect.value,
            tone: toneSelect.value,
            post_type: intellidraft.post_type
        };

        fetch(intellidraft.rest_url + '/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': intellidraft.nonce,
            },
            body: JSON.stringify(data),
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(result => {
                titleInput.value = result.title;
                excerptTextarea.value = result.excerpt;
                contentTextarea.value = result.content;
                createButton.disabled = false;
            })
            .catch(error => {
                const errorMsg = error.message || 'Failed to generate content.';
                showMessage(errorMsg, 'error');
            })
            .finally(() => {
                generateLoading.style.display = 'none';
                generateButton.disabled = false;
            });
    });

    createButton.addEventListener('click', function (e) {
        e.preventDefault();
        const title = titleInput.value.trim();
        if (!title) {
            showMessage('Please generate a title first.', 'error');
            return;
        }

        createLoading.style.display = 'inline';
        createButton.disabled = true;
        messageDiv.innerHTML = '';

        const data = {
            title: title,
            content: contentTextarea.value,
            excerpt: excerptTextarea.value,
            post_type: intellidraft.post_type,
            post_status: postStatusSelect.value,
        };

        fetch(intellidraft.rest_url + '/create-post', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': intellidraft.nonce,
            },
            body: JSON.stringify(data),
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(result => {
                showMessage('Post saved as ' + postStatusSelect.value + '. <a href="' + result.edit_link + '">Edit it here</a>.', 'success');
            })
            .catch(error => {
                const errorMsg = error.message || 'Failed to save post.';
                showMessage(errorMsg, 'error');
            })
            .finally(() => {
                createLoading.style.display = 'none';
                createButton.disabled = false;
            });
    });

    function showMessage(message, type) {
        const div = document.createElement('div');
        div.className = `notice notice-${type}`;
        div.innerHTML = `<p>${message}</p>`;
        messageDiv.appendChild(div);
    }
});