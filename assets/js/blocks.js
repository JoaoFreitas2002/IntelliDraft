const { createHigherOrderComponent } = wp.compose;
const { createElement, Fragment, useState } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, TextareaControl, Button } = wp.components;
const { addFilter } = wp.hooks;

const blockNames = ['core/heading', 'core/paragraph', 'core/code'];

const addInspectorControl = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        if (!blockNames.includes(props.name)) {
            return createElement(BlockEdit, props);
        }

        const { setAttributes } = props;
        const [inputText, setInputText] = useState('');
        const [apiResponse, setApiResponse] = useState('');

        const callChatGptApi = async () => {
            try {
                const response = await fetch(extendedHeadingBlock.ajax_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'chatgpt_generate_content',
                        nonce: extendedHeadingBlock.nonce,
                        prompt: inputText,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    setApiResponse(data.data);
                } else {
                    console.error('Error:', data.data);
                }
            } catch (error) {
                console.error('Error calling ChatGPT API:', error);
            }
        };

        const updateBlockContent = () => {
            setAttributes({ content: apiResponse });
        };

        const textAreaStyle = {
            minHeight: '100px',
            maxHeight: '300px',
            display: 'block',
            marginBottom: '10px'
        };

        const buttonContainerStyle = {
            display: 'flex',
            gap: '10px',
            marginTop: '10px',
            flexDirection: 'column',
            alignItems: 'stretch'
        };

        return createElement(
            Fragment,
            {},
            createElement(BlockEdit, props),
            createElement(
                InspectorControls,
                {},
                createElement(
                    PanelBody,
                    { title: 'IntelliWriter' },
                    createElement(TextareaControl, {
                        label: 'Prompt',
                        value: inputText,
                        style: textAreaStyle,
                        onChange: (value) => setInputText(value),
                    }),
                    createElement(TextareaControl, {
                        label: 'Output',
                        value: apiResponse,
                        style: textAreaStyle,
                        onChange: (apiValue) => setApiResponse(apiValue),
                    }),
                    createElement('div',
                        { style: buttonContainerStyle },
                        createElement(Button, {
                            isPrimary: true,
                            onClick: callChatGptApi,
                            style: { justifyContent: 'center' }
                        }, 'Generate Content'),
                        createElement(Button, {
                            isSecondary: true,
                            onClick: updateBlockContent,
                            style: { justifyContent: 'center' }
                        }, 'Update Block Content')
                    )
                )
            )
        );
    };
}, 'addInspectorControl');

addFilter('editor.BlockEdit', 'extended-heading-block/inspector-control', addInspectorControl);