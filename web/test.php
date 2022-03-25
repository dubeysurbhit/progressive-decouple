<?php
$location = 'Jaipur';

$queryString = http_build_query([
  'access_key' => '68127c675b9eccc88aa092e15d2838e4',
  'query' => $location,
]);

$ch = curl_init(sprintf('%s?%s', 'https://api.weatherstack.com/current', $queryString));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$json = curl_exec($ch);
curl_close($ch);

$api_result = json_decode($json, true);

echo "Current temperature in $location is {$api_result['current']['temperature']}℃", PHP_EOL;
?>