<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chat With Ollama by GazsTao</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">

</head>
<body>

    <!-- Ask Section -->
    <div class="container-fluid">
        <div class="ask-section container">
            <h1>Chat With Ollama</h1>

            <div class="form-section container">
            <form id="ollamaRequest">

                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-8">
                    <!-- campo de pergunta -->
                    <input type="text" class="form-control" id="prompt" name="prompt" required>
                    </div>

                    <!-- campo de escolha do modelo -->
                    <div class="col-2">

                        <?php
                            // O codigo vai ler o JSON, iterar sobre a lista de modelos, e gerar as opcoes da caixa de selecao com base no campo "name" de cada objeto dentro da lista "models".
                            $url = "http://localhost:11434/api/tags";
                            $response = file_get_contents($url);
                            $models = json_decode($response, true);
                            
                            // Verificando se ha modelos
                            if (isset($models['models']) && is_array($models['models'])) {
                            echo '<select class="form-control" id="model" name="model">';
                            
                            // Iterando sobre os modelos e criando opcoes
                            foreach ($models['models'] as $model) {
                                $selected = ($model['name'] === 'phi3:latest') ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($model['name']) . '" ' . $selected . '>' . htmlspecialchars($model['name']) . '</option>';
                            }
                            echo '</select>';
                            } else {
                            echo 'Nenhum modelo encontrado.';
                            }
                        ?>
                        
                        <br>
                    </div>
                </div>
                <button type="submit" class="btn btn-custom" id="submitButton">Perguntar</button>
            </form>
            <pre class="detalhes">Pergunte qualquer coisa para o modelo selecionado.</pre>
            </div>
    </div>

    <div class="container-fluid" id="responseContainer" style="display: none;">
        <div class="container">

            <!-- a pergunta -->
            <div class="ask-answer"><p id="askText"></p></div><br>

            <!-- aqui comeca a resposta -->
            <div class="answer"><p id="responseText"></p></div>
        </div><br>
        <div class="container data">
            <div class="row align-items-center">
                <div class="col-3">Model<pre id="responseModel"></pre></div>
                <div class="col-6">Created At<pre id="responseCreatedAt"></pre></div>
                <div class="col-3">System by<pre>Gazstao</pre></div>
                <div class="col-3">Done<pre id="responseDone"></pre></div>
                <div class="col-3">Done Reason<pre id="responseDoneReason"></pre></div>
                <div class="col-3">Total Duration<pre id="responseTotalDuration"></pre></div>
                <div class="col-3">Load Duration<pre id="responseLoadDuration"></pre></div>
                <div class="col-3">Prompt Eval Count<pre id="responsePromptEvalCount"></pre></div>
                <div class="col-3">Prompt Eval Duration<pre id="responsePromptEvalDuration"></pre></div>
                <div class="col-3">Eval Count<pre id="responseEvalCount"></pre></div>
                <div class="col-3">Eval Duration<pre id="responseEvalDuration"></pre></div>
            </div>
        </div>
    </div>
    <br>
    <div class="container footer" style="text-align: center;">
        <div class="row align-items-center" style="margin: auto;">
            <div class="col-2"><a href="index">Chat</a></div>
            <div class="col-2"><a href="lista">Lista de Modelos</a> </div>
        </div>
    </div>
    <br>

    <!-- Bootstrap JS e dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    

    <script>
        document.getElementById('ollamaRequest').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita o envio padrao do formulario

            const prompt = document.getElementById('prompt').value;
            const model = document.getElementById('model').value;

            const data = {
                model: model,
                prompt: prompt,
                stream: false
            };

            var pergunta = document.getElementById('askText');
            var button = document.getElementById('submitButton');
            var statusMessage = document.getElementById('statusMessage');
            var prompte = document.getElementById('prompt');

            // Desabilita o botao
            button.disabled = true;
            button.innerText = 'Aguarde a resposta por favor...';
            pergunta.innerText = prompt;

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
                document.getElementById('responseTotalDuration').textContent = data.total_duration;
                document.getElementById('responseLoadDuration').textContent = data.load_duration;
                document.getElementById('responsePromptEvalCount').textContent = data.prompt_eval_count;
                document.getElementById('responsePromptEvalDuration').textContent = data.prompt_eval_duration;
                document.getElementById('responseEvalCount').textContent = data.eval_count;
                document.getElementById('responseEvalDuration').textContent = data.eval_duration;
                document.getElementById('responseText').innerHTML = convertText(data.response);
            })
            .catch((error) => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Rehabilita o botao apos a resposta
                button.disabled = false;
                button.innerText = 'Perguntar';
                prompte.value = '';
            });
        });

        function convertText(text) {
            // Converter quebras de linha em <br>
            let htmlText = text.replace(/\n/g, "<br>")
                            .replace(/\*\s\*\*/g, "<b>")
                            .replace(/\*\*/g, "</b>"); 
            return htmlText;
        }
    </script>
</body>
</html>
