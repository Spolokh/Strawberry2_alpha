<h2>Письмо с сайта <?=$config["http_script_dir"]; ?> от <?=$name; ?></h2>

<?=preg_replace( "/\n/",'<br/>', trim($comment) ); ?>

---------------------------------------------------

<p><i>IP: <?=$ip; ?></i></p>