import openai
import os
import sys


'''
Los modelos de chat, como "gpt-3.5-turbo-1106", deben ser utilizados con la API de Chat Completions 
en lugar de la API de Completions estándar.
'''

model_engine = "gpt-3.5-turbo-1106"

# Verificar si se proporcionó el título como argumento
if len(sys.argv) < 2:
    print("Por favor, proporciona qty")
    sys.exit(1)

# Params de línea de comandos
qty_str = [arg for arg in sys.argv if arg.startswith('qty=')]
qty     = int(qty_str[0][len('qty='):]) if qty_str else 1

# gender   = sys.argv[1][len('gender='):]

max_tokens = 50 + (25 * qty)

if (max_tokens > (2048 - 50)):
    print("Demasiados tokens. Re-preguntar") 
    sys.exit(1)

prompt = f"Write {qty} positive reviews in Italian for E-commerce that sells clothes, accessories such as bags and shoes for men, women and children"

system_message = "Format the output as a Python unidimensional array. Avoid extra comments"

print(prompt)

# Generar una respuesta utilizando la API de Chat Completions
response = openai.ChatCompletion.create(
    model=model_engine,
    messages=[
        {"role": "system", "content": system_message},
        {"role": "user", "content": prompt}
    ],
    max_tokens=max_tokens,
    temperature=0.9,
    timeout=9500
)

generated_text = response['choices'][0]['message']['content']
print(f"Answer: {generated_text}")

"""
    Tokens utilizados           v2 del prompt
    
    3                   201     249
    30                          675
    50                          1100 - 1350

"""


# Obtener e imprimir el número de tokens utilizados
tokens_used = response['usage']['total_tokens']
print(f"Tokens: {tokens_used}")