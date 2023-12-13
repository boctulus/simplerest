import openai
import sys

openai.api_key =  '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'

def file_get_contents(filename):
    with open(filename) as f:
        return f.read()

# Set up the model and prompt

"""
gpt-3.5-turbo

This model's maximum context length is 4097 tokens. However, your messages resulted in 13585 tokens. Please reduce the length of the messages.

gpt-4

This model's maximum context length is 8192 tokens. However, your messages resulted in 13583 tokens. Please reduce the length of the messages.

gpt-4-32k-0314

This model's maximum context length is 8192 tokens. However, your messages resulted in 13583 tokens. Please reduce the length of the messages.

gpt-4-0314

This model's maximum context length is 8192 tokens. However, your messages resulted in 13583 tokens. Please reduce the length of the messages.

"""

model_engine = "gpt-4" 

# content = "http://sprunge.us/FN921u"  
content = file_get_contents("D:\\www\\autoscraper\\app\\etc\\page.html")

struct = """{
    "destination" : "{country or city}",
    "price" : "{price}",
    "currency" : "",
    "duration":"{number of days}",
    "description":"",
    "other_info":"{extra information}",
    "start_dates": ["{dd-mm-yyyy}"],
    "itinerary": [
        {
            "day": "{day month}, {day month}",
            "location": "{country} – {city}",
            "description": ""
        },
        // more itineraries
    ]
}"""



###
# print(content)
# sys.exit()

prompt = """I will give you the HTML from a website and I want you make a web scraper script in Python capable to read the fields I\'ll specify following the JSON this structure:

       {struct}

        - The code should be robust, modular and easy to extend.

        - Use defensive programming avoiding fatal errors during web scraping.

        - In soup.find() BE SURE the class exists! If not, just complete with null for the corresponding field.
        
        - If you can't have a match for a field, complete with null but if all are null, discard.

        - Do web scraping to this:

        {content}

        """

prompt = prompt.format(struct=struct, content=content)

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
    temperature=0,
)

response = completion.choices[0].message['content']
print(response)
