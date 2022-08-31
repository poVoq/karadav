<?php

namespace KaraDAV;

require_once __DIR__ . '/_inc.php';

$users = new Users;
$install_password = DB::getInstallPassword();

$error = 0;

if (!empty($_POST['login']) && !empty($_POST['password'])) {
	if ($users->login($_POST['login'], $_POST['password'])) {
		$url = null;

		if (!empty($_POST['nc']) && $_POST['nc'] == 'redirect') {
			$url = $users->appSessionCreateAndGetRedirectURL();
		}
		elseif (!empty($_POST['nc'])) {
			$users->appSessionCreate($_POST['nc']);
			$error = -1;
		}
		else {
			$url = './';
		}

		var_dump($url); exit;

		if ($url) {
			header('Location: ' . $url);
			exit;
		}
	}
	else {
		$error = 1;
	}
}

html_head('Login');

if ($error == -1) {
	echo '<p class="confirm">You are logged in, you can close this window or tab and go back to the app.</p>';
	exit;
}

if ($error) {
	echo '<p class="error">Invalid login or password</p>';
}

if ($install_password) {
	printf('<p class="info">Your default user is:<br />
		demo / %1$s<br>
		<em>(this is only visible by you and will disappear when you close your browser)</em></p>', $install_password);
}

echo '
<form method="post" action="">';

if (isset($_GET['nc'])) {
	printf('<input type="hidden" name="nc" value="%s" />', htmlspecialchars($_GET['nc']));
	echo '<p class="info">The NextCloud app is trying to access your data. Please login to continue.</p>';
}

echo '
<fieldset>
	<legend>Login</legend>
	<dl>
		<dt><label for="f_login">Login</label></dt>
		<dd><input type="text" name="login" id="f_login" required /></dd>
		<dt><label for="f_password">Password</label></dt>
		<dd><input type="password" name="password" id="f_password" required /></dd>
	</dl>
	<p><input type="submit" value="Submit" /></p>
</fieldset>
</form>
';

html_foot();
