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
Instructions: 

It's needed to get: travel items for a travel website.

Read and parse the Content.

JSON fields names in English, values in Italian.

In the "itinerario" field, add the description for every item.

In the "duration" field also add number of nights.

Fill a JSON following this JSON structure:

{struct}

JSON example:

{example}

Content:

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
    request_timeout=300
)

response = completion.choices[0].message['content']
print(response)

"""
PS D:\www\autoscraper\app\etc\py> py.exe .\test_2e.py
{
    "destination": "Italia - Chongqing",
    "price": "",
    "currency": "",
    "duration": "11 giorni",
    "description": "Viaggio attraverso l'Italia e Chongqing, con visite a vari villaggi e luoghi di interesse culturale.",
    "other_info": "",
    "start_dates": ["18 Aprile"],
    "itinerary": [
        {
            "day": "1° giorno, 18 aprile",
            "location": "Italia - Chongqing",
            "description": "Partenza con volo di linea per Chongqing, con scalo europeo o via Pechino. Pasti e pernottamento a bordo."
        },
        {
            "day": "2° giorno, 19 aprile",
            "location": "Chongqing",
            "description": "Arrivo nel pomeriggio e pratiche doganali. Incontro con un nostro incaricato e trasferimento in albergo. Cena e pernottamento."
        },
        {
            "day": "3° giorno, 20 aprile",
            "location": "Chongqing - Dazu - Chongqing",
            "description": "Gita di Km 100 a Dazu, attraverso la campagna del Sichuan. Visita delle statue Tang e Song a Dazu e dei bassorilievi a Baoding. Pranzo cinese in ristorante locale. Rientro a Chongqing. Cena libera e pernottamento."
        },
        {
            "day": "4° giorno, 21 aprile",
            "location": "Chongqing - Guiyang",
            "description": "Trasferimento in stazione e partenza in treno per Guiyang (due ore e trenta minuti). Pranzo in ristorante. Pomeriggio visita dell'antica città Qingyangong. Sistemazione in albergo. Cena inclusa e pernottamento."
        },
        {
            "day": "5° giorno, 22 aprile",
            "location": "Guiyang - Anshun - Guiyang",
            "description": "Partenza per Anshun e visita delle cascate di Huangguoshu, la cascata più grande in Asia. Visita dell'antico villaggio Tianlong. Sistemazione in albergo. Pernottamento."
        },
        {
            "day": "6° giorno, 23 aprile",
            "location": "Guiyang - Kaili",
            "description": "Trasferimento in pullman a Kaili. Pranzo in ristorante. Visita del villaggio Langde, considerato un museo vivente della cultura Miao. Cena e pernottamento."
        },
        {
            "day": "7° giorno, 24 aprile",
            "location": "Sister's Meal Festival",
            "description": "Escursione a Shidong e giornata dedicata ad assistere il Sister's Meal Festival. Pranzo in ristorante. Rientro in albergo e cena libera. Pernottamento."
        },
        {
            "day": "8° giorno, 25 aprile",
            "location": "Kaili - Rongjiang",
            "description": "Partenza in direzione di Rongjiang. Visita del villaggio Miao di Shiqiao e delle terrazze di riso di Gaoyao. Sistemazione in albergo e pernottamento."
        },
        {
            "day": "9° giorno, 26 aprile",
            "location": "Rongjiang - Zhaoxing",
            "description": "Partenza in direzione di Zhaoxing con sosta e visita al villaggio di Basha, Yingtan Dong e Zhaoxing. Pranzo e cena inclusi. Sistemazione in albergo e pernottamento."
        },
        {
            "day": "10° giorno, 27 aprile",
            "location": "Zhaoxing - Guiyang - Chongqing",
            "description": "Partenza in direzione di Guiyang e trasferimento in treno (circa 2 ½ ore) per Chongqing. Pranzo libero. Arrivo e sistemazione in albergo. Cena e pernottamento."
        },
        {
            "day": "11° giorno, 28 aprile",
            "location": "Chongqing - Italia",
            "description": "Trasferimento in aeroporto e volo di linea per l'Italia, con scalo europeo o via Pechino."
        }
    ]
}
"""