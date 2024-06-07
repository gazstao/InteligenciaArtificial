#!/usr/bin/env python3
# USO:  python olmail2.1.py
# 2024.04.22
#
# Após fazer a pergunta, o programa irá verificar todos os modelos de linguagem instalados através do ollama. 
# A mesma pergunta será feita para todos os modelos instalados.
# Após a realização da consulta, os resultados são enviados por email, configurado de acordo com o arquivo parametros.ini
# VERSÃO 2.1 - saída em pdf também

"""
Após fazer a pergunta, o usuário deverá responder se cada modelo será utilizado na produção do texto final. A mesma pergunta será feita para todos os modelos. Após a realização da consulta, os resultados são enviados por email.

"""

from fpdf import FPDF
import ollama
import re
import json
import configparser
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart


# Le parametros de configuração do arquivo de configuração 
# EXEMPLO DE ARQUIVO parametros.ini
# [smtp]
# smtp_server = smtp.mail.yahoo.com.br
# smtp_port = 587
# usuario = user@yahoo.com.br
# senha = senha
#[mensagem]
#para = destinatario@yahoo.com.br

arquivo_configuracao = '../parametros.ini' # Sera utilizado pela funcao enviaEmail(assunto,mensagem)

def le_parametros(arquivo):
    print (f"Lendo {arquivo} ...")
    config = configparser.ConfigParser()
    with open(arquivo, 'r', encoding='utf-8') as file:
        config.read_file(file)

    parametros = {}
    for section in config.sections():
        for key in config[section]:
            parametros[key] = config[section][key]

    return parametros

# Classe para criar arquivo PDF
class PDF(FPDF):

    def header(self):
        self.set_font("Arial", "B", 12)
        self.cell(0, 10, "Resposta" , align="C", ln=True)

    def footer(self):
        self.set_y(-15)
        self.set_font("Arial", "I", 8)
        self.cell(0, 10, f"Página {self.page_no()}", align="C")



# Envia email 
def enviaEmail(assunto, mensagem):
    # carrega os parametros smtp_server, smtp_port, usuario, senha e para (destinatario da mensagem) do arquivo escolhido
    parametros = le_parametros(arquivo_configuracao)


    # Montar a mensagem
    msg = MIMEMultipart()
    msg['From'] = parametros['usuario']
    msg['To'] = parametros['para']
    msg['Subject'] = assunto 
    msg.attach(MIMEText(mensagem, 'plain'))

    # Enviar o email
    server = smtplib.SMTP(parametros['smtp_server'],parametros['smtp_port'])
    server.starttls()
    server.login(parametros['usuario'], parametros['senha'])
    server.sendmail(parametros['usuario'],parametros['para'], msg.as_string())
    server.quit()

    print(f'Email para {parametros["para"]} enviado com sucesso!')


# Lista os nomes dos modelos existentes no ollama, com um índice
def listaSimples(lista):
    for idx, model in enumerate(lista['models']):
        print(f"Model {idx+1}:", model['name'])


# Acessa informações sobre cada modelo, e verifica se será utilizado no processo criando o campo model['utiliza']
def listaModelos(lista):
    for model in lista['models']:
        size = model['size']
        size = size/(1024*1024*1024)
        print("Model name:", model['name'])
        print(f"Size: {round(size,3)} GB")
        print("Modified at:", model['modified_at'])
        print("Digest:", model['digest'])
        print("Family:", model['details']['family'])
        print("Format:", model['details']['format'])
        print("Parameter size:", model['details']['parameter_size'])
        print("Quantization level:", model['details']['quantization_level'])
        print()
        model['utiliza'] = input ("Utiliza o modelo (s/n)?")


# Envia um prompt específico para um modelo específico, 
# A resposta é enviada por email, sendo o assunto o prompt e o corpo do email a resposta

def utilizaModelo(modelo_escolhido, prompt):
    saida = ollama.chat(model=modelo_escolhido['name'], messages=[{'role': 'user', 'content': prompt}])
    print(saida['created_at'])
    print(saida['message']['content'])
    assunto = f"{prompt} - {modelo_escolhido['name']}"
    enviaEmail(assunto, saida['message']['content'])


# Função para exibir o menu e permitir ao usuário escolher um modelo
def menu(lista):
    print("Aguardando entrada:")
    while True:
        prompt = input(">")
        if (prompt.lower()=="/bye"):
            break
        mensagem = ""
        listaModelos(lista)
        for modelo_atual in lista['models']:
            print(f"Modelo {modelo_atual['name']} ", end="")
            if (modelo_atual['utiliza'].lower() == 's'):
                print("será utilizado.\n")
                print(prompt)
                saida = ollama.chat(model=modelo_atual['name'], messages=[{'role': 'user', 'content': prompt}])
                print(saida['created_at'])
                print(saida['message']['content'])

                nomeArquivo = prompt+" - "+modelo_atual['name']+" - "+saida['created_at']
                nomeArquivo = "..\\Data\\"+re.sub(u'[^a-zA-Z0-9áéíóúÁÉÍÓÚâêîôÂÊÎÔãõÃÕçÇ -]', '', nomeArquivo)+".json"
                with open(nomeArquivo, "w", encoding='utf-8') as arquivo:
                    #json.dump(saida['message'], arquivo, ensure_ascii=False)
                    json.dump(saida, arquivo, ensure_ascii=False)
                arquivo.close()

                nova_mensagem = "\n\n\n"+modelo_atual['name']+"\n"+saida['created_at']+"\n\n"+saida['message']['content']+"\n"
                mensagem = mensagem+nova_mensagem
                print(nova_mensagem)
            else:
                print("não será utilizado.")
        enviaEmail(prompt, mensagem)
        pdf = PDF()
        pdf.add_page()
        pdf.set_font("Arial", size=12)
        pdf.cell(200, 10, txt=mensagem, ln=True)
        pdf.output("meu_documento.pdf")

            

# Obtém a lista de modelos
try:
    lista = ollama.list()
    menu(lista)
except Exception as e:
    print(f"Não foi possível carregar o sistema: {e}")
