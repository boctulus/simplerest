import requests
import sys

base_url = "https://produzione.familyintale.com/create-personalized-tale_p/"

# if (len(sys.argv) == 1):
#     print('Expected query string!')
#     sys.exit()

# querystr = sys.argv[1]

querystr = 'name_b=Andrea&name_p=Pablo&genderkids=m&genderparents=m&characterkids=bfb&characterparents=gfb&tale_language=es&tale_story=gu'

url = base_url + '?' + querystr

payload={}
headers = {
    'Accept': 'text/html',
    'Content-Type': 'text/plain; charset=utf-8'
}

response = requests.request("GET", url, headers=headers, data=payload)

print(response.text.encode("utf-8"))
