<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>CSS Prefix Spawner</title>
</head>
<style>
html, body {
	margin: 0;
	padding: 0;
	font-family: Calibri, 'Lucida Grande', Arial, Verdana, sans-serif;
}
html {
	height: 100%;
	overflow-x: hidden;
	overflow-y: scroll;
	background-color: #531a2a;
	background-repeat: no-repeat;
	background-image: -moz-radial-gradient(25% 25%, circle farthest-side, #bf3b60 0%, #531a2a 50%);
	background-image: -webkit-radial-gradient(25% 25%, circle farthest-side, #bf3b60 0%, #531a2a 50%);
	color: #FFF;
}
body {
	min-height: 100%;
	padding-bottom: 10em;
}
#container {
	width: 980px;
	margin: 0 auto;
	padding: 10px;
	text-align: center;
}
input, textarea {
	width: 900px;
	padding: 5px;
	border-left: 1px solid #531a2a;
	border-bottom: 1px solid #bf3b60;
	border-right: 1px solid #bf3b60;
	border-top: 1px solid #531a2a;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	font-family: monospace, sans-serif;
	font-size: 1em;
}
textarea {
	height: 400px;
}
h1 {
	font-size: 5em;
}
p {
	font-size: 2em;
}
label {
	font-weight: bold;
}
button {
	margin: 1em 0;
	padding: 0 1em;
	background-color: #bf3b60;
	color: #FFF;
	border: none;
	-webkit-border-radius: 3em;
	-moz-border-radius: 3em;
	border-radius: 3em;
	font-family: Calibri, 'Lucida Grande', Arial, Verdana, sans-serif;
	font-size: 4em;
	text-transform: uppercase;
	cursor: pointer;
}
hr {
	margin: 4em 0;
	background-color: #bf3b60;
	color: #bf3b60;
}
img {
	margin: 0 10px;
	border: 1px solid #FFF;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	vertical-align: middle;
}
a {
	color: #FFF;
}
</style>
<body>
<div id="container">
	<h1>CSS Prefix Spawner</h1>
	<h2>You code for one vendor, we spawn the rest!</h2>
	<p>Tired of having to repeat all vendor prefixes over and over again just for getting your cross-browser dosis of CSS 3 eyecandy? Bother no more, my friend! Paste your single-vendor CSS or point to the file and we will automatically add the rest.</p>
	<form action="." method="post">
	<?php
	include('csstidy/class.csstidy.php');
	$source = '';
	$input_url = '';
	$textarea_css = '';
	$css = new csstidy();
	$css->set_cfg('remove_bslash', false);
	$css->set_cfg('compress_colors', false);
	$css->set_cfg('compress_font-weight', false);
	$css->set_cfg('lowercase_s', false);
	$css->set_cfg('optimise_shorthands', 0);
	$css->set_cfg('remove_last_;', false);
	$css->set_cfg('case_properties', 0);
	$css->set_cfg('sort_properties', false);
	$css->set_cfg('sort_selectors', false);
	$css->set_cfg('merge_selectors', 0);
	$css->set_cfg('discard_invalid_properties', false);
	$css->set_cfg('css_level', 'CSS3');
	$css->set_cfg('timestamp', false);
	$css->load_template('csstidy/template1.tpl', true);
	if(isset($_POST['submit'])) 
	{
		if($source == '' && isset($_POST['url']) && $_POST['url'] != '') 
		{
			$input_url = $_POST['url'];
			$source = @file_get_contents($input_url);
			if($source == '') $input_url = '';
		}
		if($source == '' && isset($_POST['css']) && $_POST['css'] != '') 
		{
			$source = $textarea_css = $_POST['css'];
		}
		if($source != '')
		{
			$css->parse($source);
			function spawn_prefixes()
			{
				global $css;
				$vendor_prefixes = array(
					'-webkit-',
					'-moz-',
					'-o-',
					'-ms-'
				);
				$property_regex = '/('.implode('|',$vendor_prefixes).')([a-z\-]+)/';
				$value_regex = '/('.implode('|',$vendor_prefixes).')(.+)/i';
				array_push($vendor_prefixes,'');
			
				foreach($css->css as $media => $selectors)
				{
					foreach($selectors as $selector => $commands)
					{
						foreach($commands as $property => $value)
						{
							if(preg_match($property_regex, $property, $matches) > 0)
							{
								foreach($vendor_prefixes as $vendor_prefix)
								{
									if(!array_key_exists($vendor_prefix.$matches[2], $commands))
									{
										$css->css_add_property($media,$selector,$vendor_prefix.$matches[2],$value);
										#$commands[$vendor_prefix.$matches[2]] = $value;
									}
								}
							}
							if(preg_match($value_regex, $value, $matches) > 0)
							{
								foreach($vendor_prefixes as $vendor_prefix)
								{
									if(!in_array($vendor_prefix.$matches[2], $commands))
									{
										$css->css_add_property($media,$selector,$property,$vendor_prefix.$matches[2]);
										#$commands[$property] = $vendor_prefix.$matches[2];
									}
								}
							}
						}
					}
				}
			}
			
			spawn_prefixes();
			echo '<label>Result:<br />
			<textarea name="result">'.htmlentities($css->print->plain()).'</textarea></label><br />
			<hr /><br />';
		}
	}
	echo '<label>Source URL:<br />
	<input name="url" value="'.htmlentities($input_url).'" /></label><br />
	<p>or</p>
	<label>Source CSS:<br />
	<textarea name="css">'.htmlentities($textarea_css).'</textarea></label><br />';
	?>
	<button type="submit" name="submit">Spawn!</button>
	</form>
	<img src="schepp.jpg" alt="Schepp" width="60" height="60" /> &copy; 2011 Christian &quot;Schepp&quot; Schaefer / <a href="http://twitter.com/derSchepp">@derSchepp</a>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-12745512-5']);
  _gaq.push(['_trackPageview']);
  _gaq.push(['_trackPageLoadTime']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>