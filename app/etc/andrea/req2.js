var request = require('request');

// if (process.argv.length === 2) {
//   console.error('Expected query string!');
//   process.exit(1);
// }

// let querystr = process.argv[2];

querystr = 'name_b=Andrea&name_p=Pablo&genderkids=m&genderparents=m&characterkids=bfb&characterparents=gfb&tale_language=en&tale_story=gu'

var options = {
  'method': 'GET',
  'url': `https://produzione.familyintale.com/create-personalized-tale_p/?${querystr}`,
  'headers': {
    'Accept': 'text/html',
    'Content-Type': 'text/html'
  }
};

request(options, function (error, response) {
  if (error) throw new Error(error);
  console.log(response.body);
});


