import openai
import os
import sys

# Verificar si se proporcionó el título como argumento
if len(sys.argv) == 1 or not sys.argv[1].startswith('title='):
    print("Por favor, proporciona el título como argumento en el formato 'title=xxx yyy zzz'")
    sys.exit(1)

# Obtener el título del argumento de línea de comandos
title = sys.argv[1][len('title='):]

prompt = """Write a review of a product in italian in one paragrapth for '{p_title}' and be positive"""
prompt = prompt.format(p_title=title)

print(prompt)

openai.api_key = os.getenv('OPENAI_API_KEY')
model_engine   = "gpt-3.5-turbo-1106"

# Generate a response
completion = openai.ChatCompletion.create(
    model=model_engine,
    messages=[
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": prompt},
    ],
    max_tokens=1024,
    n=1,
    stop=None,
    temperature=0.5,
    request_timeout=2000
)

response = completion.choices[0].message['content']
print(f"Answer: {generated_text}")

# Obtener e imprimir el número de tokens utilizados
tokens_used = response['usage']['total_tokens']           # 210 tokens
print(f"Número de tokens utilizados: {tokens_used}")


# Modelos y tarifas
# https://platform.openai.com/docs/models/continuous-model-upgrades



