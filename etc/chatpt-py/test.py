import openai
import os

# pip install chatgpt --user
# pip install openai --user
# pip install beautifulsoup4 --user

openai.api_key =  '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' # os.getenv('OPENAI_API_KEY')

completion = openai.ChatCompletion.create( # Change the function Completion to ChatCompletion
  model = 'gpt-3.5-turbo',
  messages = [ # Change the prompt parameter to the messages parameter
    {'role': 'user', 'content': 'Hello!'}
  ],
  temperature = 0
)

print(completion['choices'][0]['message']['content']) # Change how you access the message content