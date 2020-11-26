<?php
$access_token = 'B21DEFFE6AE5418BFCED59E7B056D05586707B21DEFFE6AE5418BFCED59E7B056D055';
$company_domain = 'hieudangz';
// Return  tickets 
$url = 'https://'.$company_domain.'.uvdesk.com/en/api/customers.json?email[]=test@webkul.com,hello@uvdesk.in';
$ch = curl_init($url);
$headers = array(
    'Authorization: Bearer '.$access_token,
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
$info = curl_getinfo($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($output, 0, $header_size);
$response = substr($output, $header_size);
if($info['http_code'] == 200) {
    echo "Tickets fetched successfully.\n";
    // echo "Response Headers are \n";
    // echo $headers."\n";
    echo "Response Body \n";
    echo $response." \n";
} else if($info['http_code'] == 404) {
    echo "Error, resource not found (http-code: 404) \n";
} else {
    echo "Headers are ".$headers;
    echo "Response are ".$response;
}
curl_close($ch);
