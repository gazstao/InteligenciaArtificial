<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chat With Ollama by GazsTao</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" type="text/css">

</head>
<body>

    <!-- Ask Section -->
    <div class="container-fluid">
        <div class="ask-section container">
            <h1>Chat With Ollama</h1>
            <p style="font-size: small;">Modelos disponiveis</p>
            <div class="col-12 darker">
                <?php
                    // O codigo vai ler o JSON, iterar sobre a lista de modelos, e gerar as opcoes da caixa de selecao com base no campo "name" de cada objeto dentro da lista "models".
                    $url = "http://localhost:11434/api/tags";
                    $response = file_get_contents($url);
                    $models = json_decode($response, true);
                    
                    // Verificando se ha modelos
                    if (isset($models['models']) && is_array($models['models'])) {
                    echo '<div class="row align-items-center">';
                    // Iterando sobre os modelos e criando opcoes
                    foreach ($models['models'] as $model) {
                        echo '<div class="col-3"><div class="data">' .$model['name'] 
                            .'<pre>Parameters: '.$model['details']['parameter_size']
                            .'<pre>Quantization: '.$model['details']['quantization_level']
                            .'<pre>Family: '.$model['details']['family']
                            .'</pre><pre class="detalhes">'.$model['modified_at']
                            .'</pre></div></div>';
                    }
                    } else {
                    echo 'Nenhum modelo encontrado.';
                    }
                ?>
            </div>
        </div>               
    </div>

    <!-- footer -->
    <div class="container-fluid">
        <div class="container footer">
            <div class="row">
                <div class="col-1"><a href="index">Chat</a>
                </div>
                <div class="col-2"><a href="lista">Lista de Modelos</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS e dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>