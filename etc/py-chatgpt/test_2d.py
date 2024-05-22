import openai
import sys

openai.api_key =  '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'

def file_get_contents(filename):
    with open(filename, encoding='utf-8') as f:
        return f.read()

# Set up the model and prompt

"""
gpt-3.5-turbo

This model's maximum context length is 4097 tokens. However, your messages resulted in 13585 tokens. Please reduce the length of the messages.

gpt-4

This model's maximum context length is 8192 tokens. However, your messages resulted in 13583 tokens. Please reduce the length of the messages.

"""

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

example = """
{ 	
    "destination" : "the world", 
	"price" : "3000", 
	"currency" : "EUR",
	"duration":"8days", 
	"description":"travel throughout the indian coast....", 
	"other_info":"the price is comprehensive of ....", 
	"start_dates": ["6 Giugno 2023"], 
	"itinerary": [
		{
			"day": "1° giorno, 06 giugno",
			"location": "Italia – Delhi",
			"description": "Partenza dall’Italia con volo di linea per Delhi."
		},
		{
			"day": "2° giorno, 07 giugno",
			"location": "Delhi",
			"description": "Arrivo al mattino. Incontro con un nostro incaricato e trasferimento in albergo. Pranzo in albergo. Pomeriggio, visita di Delhi."
		}
  	]
}
"""

###
# print(content)
# sys.exit()

prompt = """
Get data from this website and return a JSON with only the needed value to store it as a travel items for a travel website,

JSON fields names in English, value in Italian,

in the "itinerario" field, add the description for every item,

in the "duration" field also add number of nights,

return a Python script capable to do the job.

Use this JSON structure:

{struct}

JSON example:

{example}

- Use defensive programming avoiding fatal errors during web scraping.

- In case of use BeautifulSoup, be sure when use soup.find() the selector exists! If not, just complete with null for the corresponding field.

- If you can't have a match for a field, complete with null but if all are null, discard.

- Do web scraping to this:

{content}

"""

prompt = prompt.format(struct=struct, content=content, example=example)

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
