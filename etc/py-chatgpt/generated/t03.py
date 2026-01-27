import json
from bs4 import BeautifulSoup

html = '''<?xml version="1.0" encoding="utf-8" standalone="yes"???????????>
... (the provided HTML content) ...
</html>'''

soup = BeautifulSoup(html, 'html.parser')

# Extracting data
destination = soup.find('div', {'property': 'name'}).text.strip()
price = soup.find('div', {'data-': ''}).text.strip()
currency = 'EUR'
duration = soup.find('span', {'property': 'name'}).text.strip().split(' ')[1]
description = soup.find('p').text.strip()
other_info = soup.find('div', {'id': '_cond'}).text.strip()
start_dates = [soup.find('div', {'property': 'name'}).find_next('div').text.strip()]

itinerary = []
for article in soup.find_all('article'):
    day = article.find('div').text.strip()
    location = article.find('p').text.strip()
    description = article.find('p').find_next('p').text.strip()
    itinerary.append({
        'day': day,
        'location': location,
        'description': description
    })

# Creating JSON structure
travel_data = {
    'destination': destination,
    'price': price,
    'currency': currency,
    'duration': duration,
    'description': description,
    'other_info': other_info,
    'start_dates': start_dates,
    'itinerary': itinerary
}

# Converting to JSON
travel_data_json = json.dumps(travel_data, ensure_ascii=False, indent=2)

print(travel_data_json)