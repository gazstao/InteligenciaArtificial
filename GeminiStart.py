# Programa Esqueleto para Estudos de IA - Google Gemini - https://aistudio.google.com/
# pip install -q -U google-generativeai   OU   pip install -r requirements.txt

# Import the Python SDK
import google.generativeai as genai
import os

# Crie uma variável de ambiente com o nome GOOGLE_API_KEY e o valor da chave obtida em https://aistudio.google.com/app/apikey
# No Windows: na barra de pesquisa abra Editar as Variáveis de Ambiente de Sistema, 
# e em Variáveis de Ambiente crie a chave com o nome GOOGLE_API_KEY, e sua chave como valor

GOOGLE_API_KEY=os.environ['GOOGLE_API_KEY']
genai.configure(api_key=GOOGLE_API_KEY)

# lista todos os modelos disponíveis
# for m in genai.list_models():
#  if 'generateContent' in m.supported_generation_methods:
#    print(m.name)


# Faz uma pergunta 
# texto pra texto

model = genai.GenerativeModel('gemini-pro')

while(True):
    pergunta = input("> ")
    if (pergunta.lower() != "bye"):
        response = model.generate_content(pergunta)
        print(response.text)
    else:
       break
