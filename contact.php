<?php
function encrypt_decrypt($string, $action = 'encrypt')
{
  $encrypt_method = 'AES-256-CBC';
  $secret_key = 'AA33CDCC2AART935136HH9B63C27'; // user define private key
  $secret_iv = '2fgf5DF5g12'; // user define secret key
  $key = hash('sha256', $secret_key);
  $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
  if ($action == 'encrypt') {
    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
  } else if ($action == 'decrypt') {
    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
  }

  return $output;
}

function http_post($url, $data)
{
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);
  curl_close($curl);

  return $response;
}

if ($_POST) {
  if (!empty($_POST['name1']) && !empty($_POST['email']) && !empty($_POST['content'])) {
    $response = http_post(
      'https://demo.balance-r.work/external/balancer/contact',
      [
        'name1' => strip_tags($_POST['name1']),
        'email' => encrypt_decrypt(strip_tags($_POST['email'])),
        'content' => strip_tags($_POST['content']),
      ]
    );

    $response = json_decode($response, true);
    if (empty($response['errors'])) {
      header('HTTP/1.1 200 OK');
      echo 'OK';
      exit;
    }
  }
}

header('HTTP/1.1 400 Bad Request');
exit;