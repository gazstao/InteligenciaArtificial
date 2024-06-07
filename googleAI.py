# export API_KEY=<YOUR_API_KEY>
# pip install -q -U google-generativeai
# Google Studio Setup Experiment 2024.05.03

import os
import google.generativeai as genai
import configparser


#
# Função padrão para carregar parâmetros de configuração
#

arquivo_configuracao = '../googleIA.ini' # Sera utilizado pela funcao enviaEmail(assunto,mensagem)
def le_parametros(arquivo):
    config = configparser.ConfigParser()
    with open(arquivo, 'r', encoding='utf-8') as file:
        config.read_file(file)

    parametros = {}
    for section in config.sections():
        for key in config[section]:
            parametros[key] = config[section][key]
    return parametros
parametros = le_parametros(arquivo_configuracao)

print("\nGoogle IA Studio Experiment 2024\n")
#
# Conexão ao Gemini
#

genai.configure(api_key=parametros['chave'])
model = genai.GenerativeModel('gemini-pro')

while(True):
    prompt = input("> ")
    if (prompt.lower() != "bye"):
        response = model.generate_content(prompt)
        print(response.text)
    else:
        break