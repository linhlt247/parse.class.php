<?php
	require_once 'parse.class.php';
	$call = new ParseFriends(
		'10000910714.....', //c_user
		'48%3Ah1YDc5n8DlNoB...', //xs
		'EAAAACZAVC6ygBAM0SaYV1Gn68HLx5XXYZCcHk9uQ5oMJZBP1Is2LNzv4EmsfUlZAnB....' //access_token
	);
	$call->Parse();
?>
