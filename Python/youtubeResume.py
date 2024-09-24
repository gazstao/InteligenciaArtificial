import requests
from youtube_transcript_api import YouTubeTranscriptApi

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
    url = "http://localhost:11434/api/generate"  # Endpoint padrão do Ollama
    model = "phi3.5:latest"  # Ajuste o modelo, se necessário

    headers = {
        "Content-Type": "application/json"
    }

    data = {
        "model": model,
        "prompt": f"Faça um resumo conciso do seguinte texto: {text}"
    }

    try:
        response = requests.post(url, headers=headers, json=data)
        response.raise_for_status()
        summary = response.json().get('response', '')
        return summary
    except requests.exceptions.RequestException as e:
        print(f"Erro ao se comunicar com a API do Ollama: {e}")
        return None

# Função principal
def main():
    video_url = input("Insira o URL do vídeo do YouTube: ")

    # Extrai o ID do vídeo a partir da URL
    video_id = video_url.split("v=")[-1]

    # Obter a transcrição
    transcript = get_transcript(video_id)
    if transcript:
        print("Transcrição obtida com sucesso.")
        
        # Enviar a transcrição para o Ollama e obter o resumo
        summary = summarize_with_ollama(transcript)
        if summary:
            print("\nResumo gerado:")
            print(summary)
        else:
            print("Falha ao gerar o resumo.")
    else:
        print("Não foi possível obter a transcrição.")

if __name__ == "__main__":
    main()
