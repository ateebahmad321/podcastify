<?php
$uri = $_SERVER['REQUEST_URI'];
// echo $uri; // Outputs: URI

$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://';

$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// echo $url; // Outputs: Full URL

?>
<a href="https://api.instagram.com/oauth/authorize?client_id=169940244426009&redirect_uri=https://solbox.dev/insta/?return_uri=<?php echo $url; ?>>&scope=user_profile,user_media&response_type=code&state=<?php echo $url; ?>"><h2>Login >
</h2></a>
