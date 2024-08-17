<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chat With Ollama</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #fff;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .resposta {
            background-color: #eeeeee;
            font-family: 'Arial', sans-serif;
            color: #333;
            word-wrap: break-word;
        }

        .pre {
            white-space: pre-wrap;
            word-wrap: break-word; 
            color: #FFF
        }

        .container {
            margin-top: 50px;
        }

        .ask-section {
            padding: 60px 0;
            text-align: center;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .data {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }

    </style>
</head>
<body>

    <!-- Ask Section -->
    <div class="container-fluid">
        <div class="ask-section container">
            <h1>Chat With Ollama</h1>

            <div class="form-section container">
                <form id="ollamaRequest">

                    <div class="row">
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
                            ?><br>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-custom" id="submitButton">Perguntar</button>
                </form>
            </div><br>
    </div>

    <!-- aqui comeca a resposta -->
    <div class="container-fluid" id="responseContainer" style="display: none;">
        <div class="resposta container">
            <!-- campo de resposta em html -->
            <p id="responseText"></p>
        </div>
        <div class="container data">
            <div class="row align-items-center">
                <div class="col">Model<pre id="responseModel"></pre></div>
                <div class="col">Created At<pre id="responseCreatedAt"></pre></div>
                <div class="col">Done<pre id="responseDone"></pre></div>
                <div class="col">Done Reason<pre id="responseDoneReason"></pre></div>
                <div class="col">Total Duration<pre id="responseTotalDuration"></pre></div>
            </div>
            <div class="row align-items-center">
                <div class="col">Load Duration<pre id="responseLoadDuration"></pre></div>
                <div class="col">Prompt Eval Count<pre id="responsePromptEvalCount"></pre></div>
                <div class="col">Prompt Eval Duration<pre id="responsePromptEvalDuration"></pre></div>
                <div class="col">Eval Count<pre id="responseEvalCount"></pre></div>
                <div class="col">Eval Duration<pre id="responseEvalDuration"></pre></div>
            </div>
        </div>
    </div>

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

            var button = document.getElementById('submitButton');
            var statusMessage = document.getElementById('statusMessage');

            // Desabilita o botao
            button.disabled = true;
            button.innerText = 'Aguarde a resposta por favor...';

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
            });

        });

        function convertText(text) {
            
            // Converter quebras de linha em <br>
            let htmlText = text
//                .replace(/</g, "&lt;") // Escapar tags HTML (< e >)
//                .replace(/>/g, "&gt;")
                .replace(/\n/g, "<br>"); // Converter \n em <br>

            // Exibir o resultado convertido
            return htmlText;
        }
    </script>
</body>
</html>