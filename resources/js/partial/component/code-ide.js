document.addEventListener('DOMContentLoaded', () => {
    const copyButtons = document.querySelectorAll('.copy-button');

    copyButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const targetId = button.dataset.clipboardTarget;
            const codeBlock = document.querySelector(targetId);

            if (codeBlock) {
                try {
                    await navigator.clipboard.writeText(codeBlock.textContent);
                    button.textContent = 'Copiado!';
                    setTimeout(() => {
                        button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg> Copiar`;
                    }, 1500);
                } catch (err) {
                    console.error('Falha ao copiar texto: ', err);
                    button.textContent = 'Erro ao copiar';
                }
            }
        });
    });

    // Função de realce de sintaxe básica (apenas para demonstração)
    const codeBlocks = document.querySelectorAll('.code-block');
    codeBlocks.forEach(block => {
        const language = block.classList.contains('language-php') ? 'php' :
                         block.classList.contains('language-javascript') ? 'javascript' :
                         block.classList.contains('language-html') ? 'html' :
                         block.classList.contains('language-css') ? 'css' : 'none';

        if (language !== 'none') {
            let code = block.textContent;
            let highlightedCode = code;

            if (language === 'javascript') {
                highlightedCode = code.replace(/\b(const|let|var|function|class|if|else|for|while|return|true|false|null|undefined|this|new)\b/g, '<span style="color: #569cd6;">$1</span>');
                highlightedCode = highlightedCode.replace(/(\/\/.*|\/\*[\s\S]*?\*\/)/g, '<span style="color: #6a9955;">$1</span>'); // Comentários
                highlightedCode = highlightedCode.replace(/('[^']*'|"[^"]*")/g, '<span style="color: #e0aaff;">$1</span>'); // Strings
            } else if (language === 'php') {
                highlightedCode = code.replace(/\b(function|class|public|protected|private|static|if|else|elseif|for|while|return|echo|print|_GET|_POST|_SESSION|_COOKIE|true|false|null)\b/g, '<span style="color: #569cd6;">$1</span>');
                highlightedCode = highlightedCode.replace(/(\/\/.*|\#.*|\/\*[\s\S]*?\*\/)/g, '<span style="color: #6a9955;">$1</span>'); // Comentários
                highlightedCode = highlightedCode.replace(/('[^']*'|"[^"]*")/g, '<span style="color: #e0aaff;">$1</span>'); // Strings
                highlightedCode = highlightedCode.replace(/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\b/g, '<span style="color: #c586c0;">$1</span>'); // Variáveis
            }
            // Adicione mais regras para outras linguagens conforme necessário

            block.innerHTML = highlightedCode;
        }
    });
});