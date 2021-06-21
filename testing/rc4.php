<?php 
include_once(__DIR__ . '/../src/Jazor/Crypt/RC4.php');

$rc4 = new \Jazor\Crypt\RC4(base64_decode('MEUCIQCmGo+mXK9ngmtATboEO6MoL+DOEmUX42IG2q5jiPnZGQIgC/tbnfTGgVbAuImm+Cvci21lTL8QQySHFNHdVFuVhlc='));

$encrypted = $rc4->encrypt('hell world!你好，世界！');
$decrypted = $rc4->decrypt($encrypted);

var_dump(base64_encode($encrypted));

var_dump($decrypted);


?>