<?php

$charpool = "123456789:;<=>?@ABCDEFHGHIJKLMNOPQRS";

function deviceIsConnected() {
	$erg = shell_exec('adb devices');
	if (count(explode("\n", $erg)) > 3) {
		return true;
	} else {
		return false;
	}
}

function dateHelper() {
	echo shell_exec("date");
	return true;
}

function noDeviceHelper($code = '') {
	echo "no device\nCurent code: " . $code;
}

if (deviceIsConnected()) {
	// get wordlist

	$verzeichnis = "./wordlists/";

	if (is_dir($verzeichnis)) {
		if ($handle = opendir($verzeichnis)) {
			while (($file = readdir($handle)) !== false) {
				echo "Processing wordlist: " . $file . "\n";
				foreach(explode("\n",file_get_contents($file)) as $currentcode){
					dateHelper();

					if (deviceIsConnected()) {
						echo 'Versuche ' . $currentcode . "...\n";
						$erg = shell_exec("adb shell twrp decrypt '" . $currentcode . "'");

						if (str_replace("fehlgeschlagen", "", $erg) == $erg) {
							echo 'Anomalie entdeckt: ' . $erg . "\n";
							die();
						} else {
							echo 'Code ' . $currentcode . " hat nicht funktioniert.\n";
						}
					} else {
						noDeviceHelper($currentcode);
					}

					dateHelper();
					echo "\n";
				}
			}
			closedir($handle);
		}
	}
} else {
	noDeviceHelper();
}

?>