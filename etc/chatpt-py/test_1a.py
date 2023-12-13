import openai
import os

openai.api_key =  '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' # os.getenv('OPENAI_API_KEY')

model_engine = "gpt-3.5-turbo-instruct"
prompt = """Complete the questions.

INPUT:

Mi nombre es Pablo y vivo en Los Angeles, tengo 32 años.

QUESTIONS:

Nombre: ...
Ciudad: ...
Edad: ...

"""

# Generate a response
completion = openai.Completion.create(
    engine=model_engine,
    prompt=prompt,
    max_tokens=1024,
    n=1,
    stop=None,
    temperature=0.5,
)


# Nombre: Pablo
# Ciudad: Los Angeles
# Edad: 32

response = completion.choices[0].text
print(response)