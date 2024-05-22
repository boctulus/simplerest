import openai
import os

openai.api_key =  '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' # os.getenv('OPENAI_API_KEY')

input = "http://sprunge.us/UvrRJX"
model_engine = "gpt-3.5-turbo-instruct"
prompt = """Complete the questions.

INPUT:

{input}

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


# ?

response = completion.choices[0].text
print(response)