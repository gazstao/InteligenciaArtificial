<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama Chat by Gazstao</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .resposta {
            background-color: #cccccc;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            margin-top: 50px;
        }
        .navbar {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .hero-section {
            padding: 60px 0;
            text-align: center;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <div class="container-fluid">
        <div class="hero-section container">
            <h1>Chat With Ollama</h1>
            <p>by Gazstao</p>
            <div class="form-section container">
                <form id="ollamaRequest">
                <!-- Campo de Pergunta -->
                    <input type="text" class="form-control" id="prompt" name="prompt" required>
                    <?php
                        // O codigo vai ler o JSON, iterar sobre a lista de modelos, e gerar as opcoes da caixa de selecao com base no campo "name" de cada objeto dentro da lista "models".
                        
                        // URL da API
                        $url = "http://localhost:11434/api/tags";
                        
                        // Fazendo a requisicao GET
                        $response = file_get_contents($url);
                        
                        // Decodificando o JSON
                        $models = json_decode($response, true);
                        
                        // Verificando se ha modelos
                        if (isset($models['models']) && is_array($models['models'])) {
                        echo '<select class="form-control" id="model" name="model">';
                        
                        // Iterando sobre os modelos e criando opcoes
                        foreach ($models['models'] as $model) {
                            $selected = ($model['name'] === 'phi3:latest') ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($model['name']) . '" ' . $selected . '>' . htmlspecialchars($model['name']) . '</option>';
                            //echo '<option value="' . htmlspecialchars($model['name']) . '">' . htmlspecialchars($model['name']) . '</option>';
                        }
                        echo '</select>';
                        } else {
                        echo 'Nenhum modelo encontrado.';
                        }
                    ?>
                    <button type="submit" class="btn btn-custom" id="submitButton">Perguntar</button>
                </form>
            </div>

        <div id="responseContainer" class="response-container" style="display: none;">
            <div class="response-item resposta">
                <label id="responseText"></label>
            </div>
            <div class="container">
                <div class="response-item">
                    <label>Model:</label>
                    <pre id="responseModel"></pre>
                </div>
                <div class="response-item">
                    <label>Created At:</label>
                    <pre id="responseCreatedAt"></pre>
                </div>
                <div class="response-item">
                    <label>Done:</label>
                    <pre id="responseDone"></pre>
                </div>
                <div class="response-item">
                    <label>Done Reason:</label>
                    <pre id="responseDoneReason"></pre>
                </div>
                <div class="response-item">
                    <label>Total Duration:</label>
                    <pre id="responseTotalDuration"></pre>
                </div>
                <div class="response-item">
                    <label>Load Duration:</label>
                    <pre id="responseLoadDuration"></pre>
                </div>
                <div class="response-item">
                    <label>Prompt Eval Count:</label>
                    <pre id="responsePromptEvalCount"></pre>
                </div>
                <div class="response-item">
                    <label>Prompt Eval Duration:</label>
                    <pre id="responsePromptEvalDuration"></pre>
                </div>
                <div class="response-item">
                    <label>Eval Count:</label>
                    <pre id="responseEvalCount"></pre>
                </div>
                <div class="response-item">
                    <label>Eval Duration:</label>
                    <pre id="responseEvalDuration"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependências -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        document.getElementById('ollamaRequest').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita o envio padrão do formulário

            const prompt = document.getElementById('prompt').value;
            const model = document.getElementById('model').value;

            const data = {
                model: model,
                prompt: prompt,
                stream: false
            };

            var button = document.getElementById('submitButton');
            var statusMessage = document.getElementById('statusMessage');

            // Desabilita o botão
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
                // Atualiza o contêiner com a resposta
                // const responseContainer = document.getElementById('responseContainer');
                // responseContainer.style.display = 'block'; // Exibe o contêiner
                // responseContainer.textContent = JSON.stringify(data, null, 2); // Formata a resposta JSON
                document.getElementById('responseContainer').style.display = 'block';
                document.getElementById('responseModel').textContent = data.model;
                document.getElementById('responseCreatedAt').textContent = data.created_at;
                document.getElementById('responseText').textContent = data.response;
                document.getElementById('responseDone').textContent = data.done;
                document.getElementById('responseDoneReason').textContent = data.done_reason;
                document.getElementById('responseTotalDuration').textContent = data.total_duration;
                document.getElementById('responseLoadDuration').textContent = data.load_duration;
                document.getElementById('responsePromptEvalCount').textContent = data.prompt_eval_count;
                document.getElementById('responsePromptEvalDuration').textContent = data.prompt_eval_duration;
                document.getElementById('responseEvalCount').textContent = data.eval_count;
                document.getElementById('responseEvalDuration').textContent = data.eval_duration;
           
            })
            .catch((error) => {
                console.error('Error:', error);
            })
            .finally(() => {
                // Rehabilita o botão após a resposta
                button.disabled = false;
                button.innerText = 'Perguntar';
            });

        });
    </script>
</body>
</html>

    <!--script>
        function submitForm() {

            // Cria o FormData para enviar o formulário via AJAX
            var form = document.getElementById('chatForm');
            var formData = new FormData(form);

            // Envia a requisição AJAX
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Mostra a resposta
                statusMessage.innerText = 'Pergunta enviada com sucesso!';
                statusMessage.style.color = 'green';
            })
            .catch(error => {
                // Mostra uma mensagem de erro
                statusMessage.innerText = 'Ocorreu um erro ao enviar a pergunta.';
                statusMessage.style.color = 'red';
            })
            .finally(() => {
                // Rehabilita o botão após a resposta
                button.disabled = false;
                button.innerText = 'Perguntar';
            });
        }
    </script>
</body>
</html-->