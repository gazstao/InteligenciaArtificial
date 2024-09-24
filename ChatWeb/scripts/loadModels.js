// URL da API
const url = "http://192.168.0.114:11434/api/tags";
const defaultModel = "phi3.5:latest";

// Função para carregar os modelos e preencher a seleção
async function loadModels() {
  try {
    // Fazendo a requisição para a API
    const response = await fetch(url);
    
    // Verificando se a resposta é OK
    if (!response.ok) {
      throw new Error("Erro ao buscar modelos");
    }

    // Lendo os dados da resposta como JSON
    const data = await response.json();

    // Verificando se há modelos
    if (data.models && Array.isArray(data.models)) {
      // Criando o elemento select
      const select = document.createElement("select");
      select.className = "form-control";
      select.id = "model";
      select.name = "model";

      // Iterando sobre os modelos e criando opções
      data.models.forEach((model) => {
        const option = document.createElement("option");
        option.value = model.name;
        option.textContent = model.name;

        // Definindo a opção como selecionada, se necessário
        if (model.name === defaultModel) {
          option.selected = true;
        }

        // Adicionando a opção ao select
        select.appendChild(option);
      });

      // Adicionando o select ao DOM (ex: dentro de um elemento com id "select-container")
      document.getElementById("select-container").appendChild(select);
    } else {
      // Caso nenhum modelo seja encontrado
      document.getElementById("select-container").textContent = "Nenhum modelo encontrado.";
    }
  } catch (error) {
    // Exibindo mensagem de erro em caso de falha na requisição
    console.error("Erro:", error);
    document.getElementById("select-container").textContent = "Erro ao carregar modelos.";
  }
}

// Chamando a função para carregar os modelos
loadModels();
