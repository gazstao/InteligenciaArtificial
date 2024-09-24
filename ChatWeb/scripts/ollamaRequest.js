// https://github.com/ollama/ollama/blob/main/docs/api.md

document.getElementById('ollamaRequest').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita o envio padrao do formulario

    const prompt = document.getElementById('prompt').value;
    const model = document.getElementById('model').value;

    const data = {
        model: model,
        prompt: prompt,
        stream: false
    };

    var pergunta = document.getElementById('pergunta');
    var statusMessage = document.getElementById('statusMessage');
    var prompte = document.getElementById('prompt');

    // Desabilita o botao
    var button = document.getElementById('submitButton');
    button.disabled = true;
    button.innerText = 'Aguarde a resposta por favor...';

    pergunta.innerText = prompt;
    document.getElementById('responseText').innerHTML = 'Processando a resposta. Aguarde por favor ...';
    document.getElementById('pergunta').style.display = 'block';
    document.getElementById('respC2').style.display = 'block';
    //document.getElementsByClassName('bordered').display = 'block';

    fetch('http://localhost:11434/api/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('responseContainer').style.display = 'block';
        document.getElementById('responseModel').textContent = data.model;
        document.getElementById('responseCreatedAt').textContent = data.created_at;
        document.getElementById('responseDone').textContent = data.done;
        document.getElementById('responseDoneReason').textContent = data.done_reason;
        document.getElementById('responseLoadDuration').textContent = data.load_duration/1000000000 + " s";
        document.getElementById('responsePromptEvalDuration').textContent = data.prompt_eval_duration/1000000000 + " s";
        document.getElementById('responseEvalDuration').textContent = data.eval_duration/1000000000 + " s";
        document.getElementById('responseTotalDuration').textContent = data.total_duration/1000000000 + " s";
        document.getElementById('responsePromptEvalCount').textContent = data.prompt_eval_count + " tokens";
        document.getElementById('responseEvalCount').textContent = data.eval_count + " tokens";
        document.getElementById('responseText').innerHTML = convertText(data.response);
    })
    .catch((error) => {
        console.error('Error:', error);
    })
    .finally(() => {
        // Reabilita o botao apos a resposta
        button.disabled = false;
        button.innerText = 'Perguntar';
    });
});

function convertText(text) {
    // Converter quebras de linha em <br>
    let htmlText = text.replace(/\n/g, "<br>")
                    .replace(/\*\s\*\*/g, "<b>")
                    .replace(/\*\*/g, "</b>"); 
    return htmlText;
}