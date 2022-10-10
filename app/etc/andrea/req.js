let https = require('follow-redirects').https;
let fs = require('fs');

// if (process.argv.length === 2) {
//   console.error('Expected query string!');
//   process.exit(1);
// }

// let querystr = process.argv[2];

querystr = 'name_b=Andrea&name_p=Pablo&genderkids=m&genderparents=m&characterkids=bfb&characterparents=gfb&tale_language=en&tale_story=gu'

let options = {
  'method': 'GET',
  'hostname': 'produzione.familyintale.com',
  'path': `/create-personalized-tale_p/?${querystr}`,
  'headers': {
    'Accept': 'text/plain'
  },
  'maxRedirects': 3
};

let req = https.request(options, function (res) {
  let chunks = [];

  res.on("data", function (chunk) {
    chunks.push(chunk);
  });

  res.on("end", function (chunk) {
    let body = Buffer.concat(chunks);
    console.log(body.toString());
  });

  res.on("error", function (error) {
    console.error(`Error. Detail: ${error}`);
  });
});

req.end();

