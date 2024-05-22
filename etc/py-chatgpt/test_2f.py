import openai
import sys

openai.api_key =  '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'

def file_get_contents(filename):
    with open(filename, encoding='utf-8') as f:
        return f.read()

# Set up the model and prompt

model_engine = "gpt-4" 

content = file_get_contents("D:\\www\\autoscraper\\app\\etc\\page.html")

struct = """
{
    "destination" : "{country or city}",
    "price" : "{price}",
    "currency" : "USD, EUR,...",
    "duration":"{number of days}",
    "description":"{Some description}",
    "other_info":"{extra information}",
    "start_dates": ["{dd-mm-yyyy}"],
    "itinerary": [
        {
            "day": "{day month}, {day month}",
            "location": "{country} - {city}",
            "description": "{description of the intinerary}"
        },
        // more itineraries
    ]
}
"""

# example = """
# { 	
#     "destination" : "div.dest", 
# 	// etc
# """

###
# print(content)
# sys.exit()

prompt = """
# Instructions: 

It's needed to get: travel items for a travel website.

Read and parse the Content.

JSON fields names in English, values in Italian.

In the "itinerario" field, add the description for every item.

In the "duration" field also add number of nights.

Fill a JSON with *DOM selector* like this:

IF there are <article>(s) and you find the information we are searching inside these articles, 
THEN with DOM selectors relative to <article>(s) 
ELSE (
    IF you a find CSS class or combination which only appears for the containing html element, 
    THEN with that selector for that information
)

# JSON structure:

{struct}

# Content:

{content}

# Notes:

- If you can't find a selector just complete with null
- Asume I will use the soup.find() method of BeautifulSoup in a Python script to do web scraping using DOM selectors you'll provide me. 

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
    temperature=0.1,
    request_timeout=2000
)

response = completion.choices[0].message['content']
print(response)
