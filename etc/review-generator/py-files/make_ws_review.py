import openai
import os
import sys


model_engine = "gpt-3.5-turbo-instruct"
max_tokens   = 1024

# Verificar si se proporcionó el título como argumento
if len(sys.argv) == 1:
    print("Por favor, proporciona qty")
    sys.exit(1)

# Params de línea de comandos. 
# Ej: script.php 5
qty   = sys.argv[1] if len(sys.argv) >1 else 1

# print(qty)
# sys.exit()

prompt = f"Write {qty} reviews in Italian for E-commerce that sells clothes, accessories such as bags and shoes for men, women and children. If the reviewer is a male finish with [m] and it's a female ends the sentences with [f]"
system_message = "Format the output as a PHP unidimensional array (like a list). Avoid extra comments"

# print(prompt)
# sys.exit()

openai.api_key = os.getenv('OPENAI_API_KEY')

# Modelos y tarifas

# https://openai.com/pricing

# Model	Input	Output

# davinci-002	                $0.0020 / 1K tokens
# babbage-002	                $0.0004 / 1K tokens  -- muy malo

# gpt-3.5-turbo-1106	        $0.0010 / 1K tokens	$0.0020 / 1K tokens
# gpt-3.5-turbo-instruct	    $0.0015 / 1K tokens	$0.0020 / 1K tokens

# gpt-4	$0.03 / 1K tokens	    $0.0600 / 1K tokens
# gpt-4-32k	$0.06 / 1K tokens	$0.1200 / 1K tokens

# Agregar un punto al final si no lo tiene
if not prompt.endswith('.'):
    prompt += '.'

# Generar una respuesta utilizando la API estándar
response = openai.Completion.create(
    engine=model_engine,
    prompt=prompt + ' ' + system_message,
    max_tokens=max_tokens,  # fijo aunque el modelo soportaria hasta 4096
    n=1,
    stop=None,
    temperature=0.9,
    timeout=2500
)

generated_text = response['choices'][0]['text']
print(f"Answer: {generated_text}")

"""
    Tokens utilizados           v2

    1 review                
    2                   280     
    3                   --      361
    5                   400
    10                  820 
    15 - 21             1024 
    37 - 50             2075
"""

# Obtener e imprimir el número de tokens utilizados
tokens_used = response['usage']['total_tokens']           # 210 tokens
print(f"Tokens: {tokens_used}")





