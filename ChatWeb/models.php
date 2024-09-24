<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chat With Ollama</title>
    <!-- Bootstrap CSS via CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/style2.css">
</head>
<body>

    <!-- Ask Section -->
    <div class="container-fluid">
        <div class="ask-section container" id="mainContainer">
            <h1>Chat With Ollama</h1>
            <h2 style="font-size: small;">Modelos disponiveis</h2>
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
                        echo '<div class="col-3" style="margin-top:20px;"><div class="data"><b>' .$model['name'] 
                            .'</b><br><br><pre>Parameters: '.$model['details']['parameter_size']
                            .'<br>Quantization: '.$model['details']['quantization_level']
                            .'<br><br>Family: '.$model['details']['family']
                            .'<br><pre>'.$model['modified_at']
                            .'</pre></pre></div></div>';
                    }
                    } else {
                    echo 'Nenhum modelo encontrado.';
                    }
                ?>
            </div>
        </div>               

        <div class="container row">
            <a href="index.html" class="col-3"><div class="askalot">HackGPT</div></a>
            <a href="models.php" class="col-3"><div class="askalot">Lista Modelos</div></a>
            <a href="askMultiple.html" class="col-3"><div class="askalot">AskALot</div></a>
            <a href="https://github.com/gazstao/HackGPT" class="col-3" target="_blank"><div class="askalot">Sobre</div></a>
    </div>
    </div>
    <br>



    <!-- Bootstrap JS e dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts/darkmode.js"></script>

</body>
</html>