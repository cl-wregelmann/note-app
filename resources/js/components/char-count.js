import quill from "./editor_config.js";

const counter = document.createElement('span');
counter.id = 'char-count';

const editorContainer = document.querySelector('.editor-container');
editorContainer.appendChild(counter);

function updateCount() {
    const length = quill.getText().trim().length;
    counter.textContent = length + ' characters';
}

updateCount();

quill.on('text-change', updateCount);
