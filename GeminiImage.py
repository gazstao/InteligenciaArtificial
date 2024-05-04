# Programa Esqueleto para Estudos de IA - Google Gemini - https://aistudio.google.com/
# pip install -q -U google-generativeai   OU   pip install -r requirements.txt

# Import the Python SDK
import google.generativeai as genai
import os
import tkinter as tk
from tkinter import filedialog
from PIL import Image


# Crie uma variável de ambiente com o nome GOOGLE_API_KEY e o valor da chave obtida em https://aistudio.google.com/app/apikey
# No Windows: na barra de pesquisa abra Editar as Variáveis de Ambiente de Sistema, 
# e em Variáveis de Ambiente crie a chave com o nome GOOGLE_API_KEY, e sua chave como valor

GOOGLE_API_KEY=os.environ['GOOGLE_API_KEY']
genai.configure(api_key=GOOGLE_API_KEY)

# lista todos os modelos disponíveis
# for m in genai.list_models():
#  if 'generateContent' in m.supported_generation_methods:
#    print(m.name)


# Caixa de seleção de arquivo 
def selecionar_arquivo():
    """
    Abre uma caixa de diálogo de seleção de arquivo e retorna o caminho do arquivo selecionado.
    """
    caminho_arquivo = filedialog.askopenfilename(
        initialdir=".",
        title="Selecione um arquivo",
        filetypes=(("Todos os arquivos", "*.*"), ("Arquivos de texto", "*.txt")))
  
    if caminho_arquivo:
        return caminho_arquivo
    else:
        print("Nenhum arquivo selecionado.")

# Carrega o arquivo 
def load_image(caminho_arquivo):
    try:
        # Abre a imagem usando a biblioteca PIL
        image = Image.open(caminho_arquivo)
        return image
    except Exception as e:
        print("Erro ao carregar a imagem:", e)
        return None

# Seleciona uma Imagem e Faz uma pergunta 
# texto e imagem pra texto

model = genai.GenerativeModel('gemini-pro-vision')

# Obter o caminho do arquivo
caminho_arquivo_selecionado = selecionar_arquivo()
if caminho_arquivo_selecionado:
    print(f"Você selecionou o arquivo: {caminho_arquivo_selecionado}")
    image = load_image(caminho_arquivo_selecionado)
    # pede o prompt para enviar junto com a imagem, pode ser vazio
    pergunta = input("\n> ")
    response = model.generate_content(image)
    response = model.generate_content(["Pode me responder em português e com riqueza de detalhes o que você vê nessa imagem?", image], stream=True)
    response.resolve()
    print(response.text)
