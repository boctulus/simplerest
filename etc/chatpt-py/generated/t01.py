import requests
from bs4 import BeautifulSoup

#URL of the website to scrape
url = 'http://sprunge.us/FN921u'

#Making a request to the website
response = requests.get(url)

#Parsing the HTML content
soup = BeautifulSoup(response.content, 'html.parser')

#Data to be scraped
destination = soup.find('h1', class_='title-main').text
price = soup.find('span', class_='price-number').text
currency = soup.find('span', class_='price-currency').text
duration = soup.find('span', class_='duration-number').text
description = soup.find('p', class_='description-text').text
other_info = soup.find('div', class_='other-info').text
start_dates = [date.text for date in soup.find_all('div', class_='start-date')]

#Data to be scraped from the itinerary
itinerary = []
for day in soup.find_all('div', class_='day'):
    day_info = {
        'day': day.find('div', class_='day-number').text,
        'location': day.find('div', class_='day-location').text,
        'description': day.find('div', class_='day-description').text
    }
    itinerary.append(day_info)

#Creating the JSON structure
data = {
    'destination': destination,
    'price': price,
    'currency': currency,
    'duration': duration,
    'description': description,
    'other_info': other_info,
    'start_dates': start_dates,
    'itinerary': itinerary
}

#Printing the json data
print(data)