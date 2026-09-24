const url = process.argv[2] ?? process.env.SIMPLEREST_QUERY_URL;

if (!url) {
  throw new Error(
    'Provide a QUERY endpoint URL as the first argument or SIMPLEREST_QUERY_URL.'
  );
}

const payload = {
  test: 'http-query-smoke',
  timestamp: new Date().toISOString()
};

const response = await fetch(url, {
  method: 'QUERY',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(payload)
});

const responseBody = await response.text();

if (!response.ok) {
  throw new Error(
    `QUERY ${url} returned HTTP ${response.status}: ${responseBody}`
  );
}

console.log(JSON.stringify({
  method: 'QUERY',
  url,
  status: response.status,
  acceptQuery: response.headers.get('accept-query'),
  response: responseBody
}, null, 2));
