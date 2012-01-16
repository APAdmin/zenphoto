<?php
define('OFFSET_PATH', 1);
require_once(dirname(__FILE__).'/admin-globals.php');
$extension = sanitize($_GET['extension']);
$thirdparty = isset($_GET['thirdparty']);
if ($thirdparty) {
	$path = SERVERPATH.'/'.USER_PLUGIN_FOLDER.'/'.$extension.'.php';
} else {
	$path = SERVERPATH.'/'.ZENFOLDER.'/'.PLUGIN_FOLDER.'/'.$extension.'.php';
}
$pluginStream = file_get_contents($path);
$parserr = 0;
if ($str = isolate('$plugin_description', $pluginStream)) {
	if (false === eval($str)) {
		$parserr = $parserr | 1;
		$plugin_description = gettext('<strong>Error parsing <em>plugin_description</em> string!</strong>.');
	}
} else {
	$plugin_description = '';
}
if ($str = isolate('$plugin_author', $pluginStream)) {
	if (false === eval($str)) {
		$parserr = $parserr | 2;
		$plugin_author = gettext('<strong>Error parsing <em>plugin_author</em> string!</strong>.');
	}
} else {
	$plugin_author = '';
}
if ($str = isolate('$plugin_version', $pluginStream)) {
	if (false === eval($str)) {
		$parserr = $parserr | 4;
		$plugin_version = ' '.gettext('<strong>Error parsing <em>plugin_version</em> string!</strong>.');
	}
} else {
	$plugin_version = '';
}
if ($thirdparty) {
	$whose = gettext('third party plugin');
	$path = stripSuffix($path).'/logo.png';
	if (file_exists($path)) {
		$ico = str_replace(SERVERPATH, WEBPATH, $path);
	} else {
		$ico = 'images/place_holder_icon.png';
	}
} else {
	$whose = 'Zenphoto official plugin';
	$ico = 'images/zp_gold.png';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="stylesheet" href="<?php echo WEBPATH.'/'.ZENFOLDER; ?>/admin.css" type="text/css" />
	<meta http-equiv="content-type" content="text/html; charset=<?php echo LOCAL_CHARSET; ?>" />
	<title><?php echo sprintf(gettext('%1$s %2$s: %3$s'),html_encode($_zp_gallery->getTitle()),gettext('admin'),html_encode($extension)); ?></title>
</head>
<body>
	<div id="main">
		<?php echo gettext('Plugin useage information'); ?>
		<div id="content">
			<h1><img class="zp_logoicon" src="<?php echo $ico; ?>" alt="<?php echo gettext('logo'); ?>" title="<?php echo $whose; ?>" /><?php echo html_encode($extension); ?></h1>
			<?php
			if ($thirdparty) {
				?>
				<h3><?php printf( gettext('Version: %s'), $plugin_version); ?></h3>
				<?php
				}
			?>
			<h3><?php printf(gettext('Author: %s'), html_encode($plugin_author)); ?></h3>
			<div>
			<?php
			echo $plugin_description;
			$i = strpos($pluginStream, '/*');
			$j = strpos($pluginStream, '*/');
			if ($i !== false && $j !== false) {
				$commentBlock = substr($pluginStream, $i+2, $j-$i-2);
				$lines = explode('*', $commentBlock);
				$doc = '';
				$par = false;
				$empty = false;

				foreach ($lines as $line) {
					$line = trim($line);
					if (empty($line)) {
						if (!$empty) {
							if ($par) {
								$doc .=  '</p>';
							}
							$doc .= '<p>';
							$empty = $par = true;
						}
					} else {
						if (strpos($line, '@') === 0) {
							if (!$plugin_author && substr($line,0,7) == '@author') {
								$plugin_author = trim(substr($line, 8));
							}
						} else {
							$doc .= html_encode($line).' ';
							$empty = false;
						}
					}
				}
				if ($par) {
					$doc .=  '</p>';
					echo $doc;
					$doc = '';
				}
			}
			echo $doc;
			if ($thirdparty) {
				if ($str = isolate('$plugin_URL', $pluginStream)) {
					if (false !== eval($str)) {
						printf(gettext('See also the <a href="%1$s">%2$s</a>'),$plugin_URL, $extension);
					}
				}
			} else {
				$plugin_URL = "http://www.zenphoto.org/documentation/plugins/_".PLUGIN_FOLDER."---".$extension.'.php'.".html";
				printf(gettext('See also the Zenphoto online documentation: <a href="%1$s">%2$s</a>'),$plugin_URL, $extension);
			}
			?>
			</div>
		</div>
	</div>
</body>
