import ollama
import sys
import configparser
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# define as variáveis iniciais para envio de email
# EXEMPLO DE ARQUIVO parametros.ini
# [smtp]
# smtp_server = smtp.mail.yahoo.com.br
# smtp_port = 587
# usuario = user@yahoo.com.br
# senha = senha
#[mensagem]
#para = destinatario@yahoo.com.br
arquivo_configuracao = '../parametros.ini'


# Le parametros de configuração do arquivo de configuração 
def le_parametros(arquivo):
    print (f"Lendo {arquivo} ...")
    config = configparser.ConfigParser()
    with open(arquivo, 'r', encoding='utf-8') as file:
        config.read_file(file)

    parametros = {}
    for section in config.sections():
        for key in config[section]:
            parametros[key] = config[section][key]
    
    print(f"Parametros carregados...")
    return parametros


# Envia email 
def enviaEmail(assunto, mensagem):
    parametros = le_parametros(arquivo_configuracao)

    msg = MIMEMultipart()
    msg['From'] = parametros['usuario']
    msg['To'] = parametros['para']
    msg['Subject'] = assunto 

    msg.attach(MIMEText(mensagem, 'plain'))

    print("Enviando email...")
    server = smtplib.SMTP(parametros['smtp_server'],parametros['smtp_port'])
    server.starttls()
    server.login(parametros['usuario'], parametros['senha'])
    server.sendmail(parametros['usuario'],parametros['para'], msg.as_string())

    server.quit()
    print('Email enviado com sucesso!')


# Lista os nomes dos modelos existentes no ollama, com um índice
def listaSimples(lista):
    for idx, model in enumerate(lista['models']):
        print(f"Model {idx+1}:", model['name'])


# Access information about each model
def listaModelos(lista):
    for model in lista['models']:
        size = model['size']
        size = size/(1024*1024*1024)
        print("Model name:", model['name'])
        print(f"Size: {size} GB")
        print("Modified at:", model['modified_at'])
        print("Digest:", model['digest'])
        print("Family:", model['details']['family'])
        print("Format:", model['details']['format'])
        print("Parameter size:", model['details']['parameter_size'])
        print("Quantization level:", model['details']['quantization_level'])
        print()


def configuraModelo(modelo_escolhido):
    pergunta = input(">")
    saida = ollama.chat(model=modelo_escolhido['name'], messages=[{'role': 'user', 'content': pergunta}])
    print(saida['created_at'])
    print(saida['message']['content'])
    assunto = f"{pergunta} - {modelo_escolhido['name']}"
    enviaEmail(assunto, saida['message']['content'])


# Função para exibir o menu e permitir ao usuário escolher um modelo
def menu(lista):
    while True:
        print("\nEscolha um modelo:")
        listaSimples(lista)
        print("0.  Sair")
        print("99. Detalhar")
        escolha = input("Digite o número do modelo ou 0 para sair: ")

        if escolha == '0':
            print("Saindo...")
            sys.exit()

        if escolha == '99':
            listaModelos(lista)

        try:
            escolha = int(escolha)
            if escolha < 0 or escolha > len(lista['models']):
                raise ValueError
            else:
                modelo_escolhido = lista['models'][escolha - 1]
                print("\nDetalhes do modelo escolhido:")
                listaModelos({'models': [modelo_escolhido]})
                configuraModelo(modelo_escolhido)
        except ValueError:
                print("")

# Lista todos os modelos instalados
#def contarModelos(lista):
#    num = len(lista['models'])
#    print(f"Existem {num} modelos instalados.")

# Obtém a lista de modelos
lista = ollama.list()

# Exibe o menu
menu(lista)