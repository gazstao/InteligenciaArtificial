import os
import json


def listar_arquivos_json(diretorio):
    arquivos_json = []
    for arquivo in os.listdir(diretorio):
        if arquivo.endswith(".json"):
            arquivos_json.append(arquivo) #arquivos_json.append(os.path.join(diretorio, arquivo))
    return arquivos_json


def carrega_arquivo_json(arquivo_json):
    try:
        with open(arquivo_json, 'r', encoding='utf-8') as arquivo:
            dados_json = json.load(arquivo)
        return dados_json
    except:
        print(f"Erro ao carregar dados de {arquivo_json}")


def converte_para_html(arquivo,dados_json):
    # pegar a pergunta e o modelo  
    html = f'<h3>{arquivo}</h3><ul>'
    for chave, valor in dados_json.items():
        if chave != 'message':
            html += f'<li><strong>{chave}:</strong><br> {valor}</li><br>'
        else:
            html += f"<li><strong>Resposta:</strong><br><p>{valor['content']}</p></li><br>"
    html+="</ul><hr>"
    return html.replace("\n","<br>")


def salvar_html(html, caminho_arquivo):
    try:
        with open(caminho_arquivo, 'w', encoding='utf-8') as arquivo:
            arquivo.write(html)
    except Exception as e:
        print (f"Erro: {e}")


# passo 1: obter todos os arquivos json do diretório definido
dir = "..\\Data\\"
arquivos_json = listar_arquivos_json(dir)
        
# passo 2: criar o html
style = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"><style>body {background-color:black; color: LightGreen;}</style>'
    
html = f"<html><head><title>Perguntas e Respostas para IAs</title>{style}</head><body>"

for arquivo in arquivos_json:
    print(f"adicionado html para {arquivo}")
    html += converte_para_html(arquivo[:-5], carrega_arquivo_json(dir+arquivo))

html += "</body></html>"
salvar_html(html, "muitasRespostas.html")