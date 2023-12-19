import openai
import os
import sys

# Verificar si se proporcionó el título como argumento
if len(sys.argv) == 1:
    print("Por favor, proporciona qty")
    sys.exit(1)

# Params de línea de comandos. 
# Ej: script.php 5
qty   = sys.argv[1] if len(sys.argv) >1 else 1

prompt = f"Write {qty} positive reviews in Italian for E-commerce that sells clothes, accessories such as bags and shoes for men, women and children. At the end inside brackets the reviewer full name and gender like  [Luca Rizzo, male]. Paying attention if reviewer should be female or male"

system_message = "Format the output as a PHP unidimensional array (like a list). Avoid extra comments"

# Agregar un punto al final si no lo tiene
if not prompt.endswith('.'):
    prompt += '.'

prompt = prompt + ' ' + system_message

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
    timeout=3000
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


