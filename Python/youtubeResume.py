import json
import ollama
from youtube_transcript_api import YouTubeTranscriptApi
import re
import webbrowser
import os


# Ajuste o modelo, se necessário
modelo = "phi3.5:latest"  
prompt = "Faça um resumo do seguinte texto, pontuando os itens principais e descrevendo o mais detalhadamente possivel seu conteudo: "
idioma = 'pt'


# Função para extrair a transcrição do YouTube
def get_transcript(video_id, language=idioma):

    try:
        # Obtem a transcrição do video escolhido na linguagem pre-definida, une o texto fragmentado e retorna o resultado
        transcript = YouTubeTranscriptApi.get_transcript(video_id, languages=[language])
        transcript_text = " ".join([entry['text'] for entry in transcript])
        return transcript_text
    except Exception as e:
        print(f"Erro ao obter a transcrição na função get_transcript: {e}")
        return None


# Função para obter um resumo usando o Ollama
def summarize_with_ollama(text, arquivoID):

    try:
        saida = ollama.chat(model=modelo, messages=[{'role':'user','content': prompt+text}])
        json_bonito_saida = json.dumps(saida,indent=4)
        print(json_bonito_saida)

        with open(".\\data\\"+arquivoID+".txt","a") as arquivo:
            arquivo.write(saida['message']['content'])
            arquivo.write("\n\n----------------------------------------\n\n")
        return(saida['message']['content'])
    except Exception as e:
        print(f"Erro: {e}")
        return None


# Função principal
def main():
    video_url = input("Insira o URL do vídeo do YouTube: ")
    video_id = video_url.split("v=")[-1]

    transcript = get_transcript(video_id)

    if transcript:
        print("Transcrição obtida com sucesso...\nAnalisando o resultado. Aguarde...")
        
        # Enviar a transcrição para o Ollama e obter o resumo
        summary = summarize_with_ollama(filtrar_string(transcript),video_id)

       # Criar um arquivo HTML com o resumo
        if summary:
            print("\nResumo gerado: ")
            print(summary)
            criaHtml(summary, video_id)
        else:
            print("Falha ao gerar o resumo.")

    else:
        print("Não foi possível obter a transcrição.")
    


def filtrar_string(texto):
    # Remove caracteres que não são letras, números ou espaços
    texto_filtrado = re.sub(r'[^\w\s]', '', texto)
    return texto_filtrado


def criaHtml(texto, video_id):
    conteudo_html = f"""
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GazsTao YouTube Resumer</title>
        <style>
            body {{
                font-family: Arial, sans-serif;
                margin: 20px;
                background-color: #f4f4f4;
            }}

        pre {{
            padding: 15px;
            text-align: justify;
            color: #abc;
            background-color: #333;
            border: 2px solid #568ea6;
            margin: auto;
            white-space: pre-wrap;
            word-wrap: break-word; 
            border-radius: 7px;
        }}   
        </style>
    </head>
    <body>
        <h1>GazsTao YouTubeResumer</h1>
        <pre>{texto}</pre>
    </body>
    </html>
    """

    # Nome do arquivo HTML
    arquivo_html = './data/'+video_id+'.html'
    print (f"Iniciando criacao do arquivo {arquivo_html}")

    # Salvando o arquivo HTML
    with open(arquivo_html, 'w', encoding='utf-8') as f:
        f.write(conteudo_html)

    # Abrindo o arquivo HTML no navegador automaticamente
    caminho_absoluto = os.path.abspath(arquivo_html)
    webbrowser.open(f'file://{caminho_absoluto}')

if __name__ == "__main__":
    main()
