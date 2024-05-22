import openai
import os

openai.api_key =  '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' # os.getenv('OPENAI_API_KEY')

# Set up the model and prompt
model_engine = "gpt-3.5-turbo-instruct"


# Elefante https://www.viaggidellelefante.it
# Tucano. https://www.tucanoviaggi.com/
# Kel 12 https://kel12.com/

# "https://www.viaggidellelefante.it"
content = "http://sprunge.us/FN921u" 

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
completion = openai.Completion.create(
    engine=model_engine,
    prompt=prompt,
    max_tokens=1024,
    n=1,
    stop=None,
    temperature=0,
)

response = completion.choices[0].text
print(response)