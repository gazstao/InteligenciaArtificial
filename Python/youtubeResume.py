import requests
import ollama
from youtube_transcript_api import YouTubeTranscriptApi
import re

ollama.list()
# Função para extrair a transcrição do YouTube
def get_transcript(video_id, language='pt'):
    try:
        transcript = YouTubeTranscriptApi.get_transcript(video_id, languages=[language])
        transcript_text = " ".join([entry['text'] for entry in transcript])
        return transcript_text
    except Exception as e:
        print(f"Erro ao obter a transcrição: {e}")
        return None

# Função para obter um resumo usando o Ollama API
def summarize_with_ollama(text):

    modelo = "llama3.1:latest"  # Ajuste o modelo, se necessário
    prompt = "Faça um resumo conciso do seguinte texto: "+text

    try:
        saida = ollama.chat(
            model=modelo, 
            messages=[{'role':'user','content': prompt}]
            )

        print(saida)
        with open("resumos.txt","a") as arquivo:
            arquivo.write(saida['message']['content'])
            arquivo.write("\n\n\n\n----------------------------------------\n\n\n\n")
    except Exception as e:
        print(f"Erro: {e}")
        return None

# Função principal
def main():
    video_url = input("Insira o URL do vídeo do YouTube: ")

    # Extrai o ID do vídeo a partir da URL
    video_id = video_url.split("v=")[-1]

    # Obter a transcrição
    transcript = get_transcript(video_id)
    if transcript:
        print("Transcrição obtida com sucesso...\nAnalisando o resultado. Aguarde...")
        
        # Enviar a transcrição para o Ollama e obter o resumo
        summary = summarize_with_ollama(filtrar_string(transcript))
        if summary:
            print("\nResumo gerado:")
            print(summary)
        else:
            print("Falha ao gerar o resumo.")
    else:
        print("Não foi possível obter a transcrição.")

def filtrar_string(texto):
    # Remove caracteres que não são letras, números ou espaços
    texto_filtrado = re.sub(r'[^\w\s]', '', texto)
    return texto_filtrado

if __name__ == "__main__":
    main()
