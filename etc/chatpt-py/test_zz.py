import openai
import sys

openai.api_key =  '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'

def file_get_contents(filename):
    with open(filename, encoding='utf-8') as f:
        return f.read()

# Set up the model and prompt

model_engine = "gpt-4" 

"""
    (!)

I'm sorry, but as an AI language model, I am unable to access external websites or URLs. 
However, if you provide the information from the URL, I can help you process it and answer your questions.

"""

prompt = """
I will provide information via URL (your data source). I need you read that and process.

http://sprunge.us/dsShZc

Questions:

What is the name of the father?
Age of the boy?

"""

# Generate a response
completion = openai.ChatCompletion.create(
    model=model_engine,
    messages=[
        {"role": "system", "content": "You are a helpful assistant."},
        {"role": "user", "content": prompt},
    ],
    # max_tokens=1024,
    n=1,
    stop=None,
    temperature=0.1,
    request_timeout=800
)

response = completion.choices[0].message['content']
print(response)
