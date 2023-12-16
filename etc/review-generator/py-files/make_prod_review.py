import openai
import os
import sys

# Verificar si se proporcionó el título como argumento
if len(sys.argv) == 1 or not sys.argv[1].startswith('title='):
    print("Por favor, proporciona title y qty")
    sys.exit(1)

# Params de línea de comandos
title = sys.argv[1][len('title='):]
qty   = sys.argv[2][len('qty=')] if len(sys.argv) > 2 and sys.argv[2].startswith('qty=') else 1

prompt = """Write {r_qty} reviews of a product in Italian. Each review in one paragraph and be positive. Use ; as review separator. Subject: for '{p_title}'"""
prompt = prompt.format(p_title=title,r_qty=qty)

print(prompt)
# sys.exit()

openai.api_key = os.getenv('OPENAI_API_KEY')

# Model	Input	Output
# gpt-3.5-turbo-1106	    $0.0010 / 1K tokens	$0.0020 / 1K tokens
# gpt-3.5-turbo-instruct	$0.0015 / 1K tokens	$0.0020 / 1K tokens

model_engine = "gpt-3.5-turbo-instruct"

# En este código, he eliminado el uso de openai.ChatCompletion.create y he vuelto a la función openai.Completion.create, 
# que es la API estándar de OpenAI GPT-3. 
#
# Ahora, la respuesta se obtiene directamente de la llamada a esta función sin necesidad de mensajes de "usuario" y "sistema".

# Generar una respuesta utilizando la API estándar
response = openai.Completion.create(
    engine=model_engine,
    prompt=prompt,
    max_tokens=1024,
    n=1,
    stop=None,
    temperature=0.5,
    timeout=2000
)

generated_text = response['choices'][0]['text']
print(f"Answer: {generated_text}")

"""
    Tokens utilizados
    
    1 review            210 
    2                   333
    5                   581                   

"""

# Obtener e imprimir el número de tokens utilizados
tokens_used = response['usage']['total_tokens']           # 210 tokens
print(f"Tokens: {tokens_used}")


# Modelos y tarifas
# https://platform.openai.com/docs/models/continuous-model-upgrades


