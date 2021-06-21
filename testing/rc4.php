<?php 
include_once(__DIR__ . '/../src/Jazor/Crypt/RC4.php');

$rc4 = new \Jazor\Crypt\RC4(base64_decode('MEUCIQCmGo+mXK9ngmtATboEO6MoL+DOEmUX42IG2q5jiPnZGQIgC/tbnfTGgVbAuImm+Cvci21lTL8QQySHFNHdVFuVhlc='));
$now = microtime(true);
for($I = 0; $i < 1000; $i++){
	$encrypted = $rc4->encrypt('MEUCIQCmGo+mXK9ngmtATboEO6MoL+DOEmUX42IG2q5jiPnZGQIgC/tbnfTGgVbAuImm+Cvci21lTL8QQySHFNHdVFuVhlc=');
	$decrypted = $rc4->decrypt($encrypted);

	//var_dump(base64_encode($encrypted));

	//var_dump($rc4->decrypt($encrypted));
}

echo microtime(true) - $now;
?>