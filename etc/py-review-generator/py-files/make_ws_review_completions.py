import openai
import os
import sys

'''
Los modelos de chat, como "gpt-3.5-turbo-1106", deben ser utilizados con la API de Chat Completions 
en lugar de la API de Completions estándar.
'''

model_engine = "gpt-3.5-turbo-1106"
max_tokens   = 220

# Verificar si se proporcionó el título como argumento
if len(sys.argv) == 1:
    print("Por favor, proporciona qty")
    sys.exit(1)

# Params de línea de comandos. 
# Ej: script.php 5
qty   = sys.argv[1] if len(sys.argv) >1 else 1

prompt = f"Write {qty} reviews in Italian for E-commerce that sells clothes, accessories such as bags and shoes for men, women and children. If the reviewer is a male finish with [m] and it's a female ends the sentences with [f]"
system_message = "Format the output as a PHP unidimensional array (like a list). Avoid extra comments"

# Generar una respuesta utilizando la API de Chat Completions
response = openai.ChatCompletion.create(
    model=model_engine,
    messages=[
        {"role": "system", "content": system_message},
        {"role": "user", "content": prompt}
    ],
    max_tokens=max_tokens,
    temperature=0.9,
    timeout=2500
)

generated_text = response['choices'][0]['message']['content']
print(f"Answer: {generated_text}")

"""
    Tokens utilizados           v2 del prompt
    
    3                   201     249
    5                   400
    10                  820 
    15 - 21             1024 
    37 - 50             2075
"""


# Obtener e imprimir el número de tokens utilizados
tokens_used = response['usage']['total_tokens']
print(f"Tokens: {tokens_used}")