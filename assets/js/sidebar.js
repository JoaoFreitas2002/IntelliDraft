const { registerPlugin } = wp.plugins;
const { PluginSidebar } = wp.editPost;
const { PanelBody, PanelRow, TextControl, TextareaControl, Button, Notice, SelectControl, HelpText } = wp.components;
const { createElement, useState, useEffect } = wp.element;

const IntelliDraftSidebar = () => {
    const [postTitle, setPostTitle] = useState('');
    const [postTopics, setPostTopics] = useState('');
    const [postTone, setPostTone] = useState('formal');
    const [postLanguage, setPostLanguage] = useState('english');
    const [errorMessage, setErrorMessage] = useState('');
    const [loadingMessage, setLoadingMessage] = useState(false);
    const [iconColor, setIconColor] = useState('black');

    const handleIconClick = () => {
        //setIconColor((prevColor) => (prevColor === 'black' ? 'white' : 'black'));
    };

    const handleTopicsChange = (value) => {
        setPostTopics(value);
        const words = value.split(',').map(word => word.trim());
        if (words.length > 3) {
            setErrorMessage('You can only input up to three words separated by commas.');
        } else {
            setErrorMessage('');
        }
    };

    const handleSubmit = () => {
        if (!errorMessage) {
            try {
                setLoadingMessage(true);
                fetch(intellidraft.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'intellidraft_generate_content',
                        nonce: intellidraft.nonce,
                        title: postTitle,
                        topics: postTopics,
                        tone: postTone,
                        language: postLanguage
                    })
                })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        return Promise.reject('Network response was not ok.');
                    })
                    .then(data => {
                        if (data.success) {
                            const { body } = data.data;

                            const blocks = wp.blocks.rawHandler({ HTML: body });

                            wp.data.dispatch('core/editor').insertBlocks(blocks);

                            wp.data.dispatch('core/editor').editPost({ title: postTitle });

                            displayNotice('warning', 'Post updated with generated content!');

                            setLoadingMessage(false);

                        } else {
                            displayNotice('error', data.data.message);
                            setLoadingMessage(false);
                        }
                    })
                    .catch(error => {
                        displayNotice('error', 'Fetch error:' + error);
                        setLoadingMessage(false);
                    });
            } catch (error) {
                displayNotice('error', error);
                setLoadingMessage(false);
            }
        }
    };

    const displayNotice = (type, message) => {
        wp.data.dispatch('core/notices').createNotice(
            type,
            message,
            { type: 'snackbar' }
        );
    }

    return createElement(
        PluginSidebar,
        {
            name: 'intellidraft-sidebar',
            title: 'IntelliDraft',
            icon: createElement('svg', {
                width: '24',
                height: '24',
                viewBox: '0 0 216 256',
                xmlns: 'http://www.w3.org/2000/svg',
                onClick: handleIconClick,
                style: { cursor: 'pointer', fill: iconColor },
                className: 'intellidraft-sidebar-icon',
                children: createElement('path', {
                    d: 'M117.2,102.9c-4.6,11-17.2,16.1-28.2,11.5c-11-4.6-16.1-17.2-11.5-28.2c4.6-11,17.2-16.1,28.2-11.5 C116.7,79.3,121.8,91.9,117.2,102.9C117.2,102.9,117.2,102.9,117.2,102.9z M213.9,154.6c0,4.3-3.5,7.8-7.8,7.8c0,0,0,0,0,0h-12.7 v26.8c0,20.8-16.9,37.7-37.7,37.7c0,0,0,0,0,0h-15V254h-102v-81.1C-2.8,140.2-10,80.1,22.6,38.5C40.7,15.5,68.4,2,97.7,2 c43.4,0,83.4,32.6,91.7,68.4c1.9,8,4,28.8,4,28.8l19.9,52.4C213.7,152.5,213.9,153.5,213.9,154.6L213.9,154.6z M138.2,87.8l12.2-7.6 l-5.1-12.5l-14.1,3.2c-2.7-3.8-6-7.2-9.8-9.9l3.3-14l-12.5-5.2l-7.7,12.2c-4.6-0.8-9.4-0.8-14-0.1L83,41.5l-12.6,5.1l3.2,14 c-3.8,2.7-7.2,6-9.9,9.8l-14-3.3l-5.3,12.5l12.2,7.7c-0.8,4.6-0.8,9.4-0.1,14L44.3,109l5.1,12.5l14-3.2c2.7,3.8,6,7.2,9.8,9.9 l-3.3,14l12.5,5.2l7.7-12.2c4.6,0.8,9.4,0.8,14,0.1l7.6,12.2l12.5-5.1l-3.2-14.1c3.8-2.7,7.2-6,9.9-9.8l14,3.3l5.2-12.5l-12.2-7.7 C139,97.1,139,92.4,138.2,87.8z',
                }),
            }),
            className: 'intellidraft-sidebar'
        },
        createElement(
            PanelBody,
            {
                title: 'Generate Post',
                initialOpen: true,
                className: 'intellidraft-sidebar-panel'
            },
            createElement(
                PanelRow,
                null,
                createElement(TextControl, {
                    label: 'Post Title',
                    value: postTitle,
                    onChange: (value) => setPostTitle(value)
                })
            ),
            createElement(
                PanelRow,
                null,
                createElement(TextControl, {
                    label: 'Topics',
                    value: postTopics,
                    onChange: handleTopicsChange,
                    help: "Please separate each word with a comma and limit to three words."
                }),
            ),
            createElement(
                PanelRow,
                null,
                createElement(SelectControl, {
                    label: 'Select Tone',
                    selected: postTone,
                    options: [
                        { label: 'Formal', value: 'formal' },
                        { label: 'Professional', value: 'professional' },
                        { label: 'Friendly', value: 'friendly' },
                    ],
                    onChange: (value) => setPostTone(value)
                })
            ),
            createElement(
                PanelRow,
                null,
                createElement(SelectControl, {
                    label: 'Select Language',
                    selected: postLanguage,
                    options: [
                        { label: 'English', value: 'english' },
                        { label: 'Portuguese', value: 'portuguese' },
                    ],
                    onChange: (value) => setPostLanguage(value)
                })
            ),
            errorMessage && createElement(
                Notice,
                {
                    status: 'error',
                    isDismissible: false
                },
                errorMessage
            ),
            loadingMessage && createElement(
                Notice,
                {
                    status: 'info',
                    isDismissible: false
                },
                'Generating...'
            ),
            createElement(
                PanelRow,
                null,
                createElement(Button, {
                    isPrimary: true,
                    onClick: handleSubmit,
                    style: { marginTop: '8px' },
                    help: "If the request gives error, please try again."
                }, 'Generate Post')
            )
        )
    );
};

registerPlugin('intellidraft-sidebar', {
    render: IntelliDraftSidebar
});
