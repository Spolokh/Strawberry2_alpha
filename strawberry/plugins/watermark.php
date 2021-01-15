<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Watermark
Plugin URI:     http://cutenews.ru
Description:	Ставим вотемарк, шоб знали чьи колоши.
Application: 	Strawberry
Author:			FI-DD
Author URI:		http://english.cutenews.ru/forum/profile.php?mode=viewprofile&u=2
*/

add_filter('options', 'watermark_AddToOptions');

function watermark_AddToOptions($options){

	$options[] = array(
	            'name'     => 'Watermark',
	            'url'      => 'plugin=watermark',
	            'category' => 'files'
	        	);

return $options;
}

add_action('plugins' ,'watermark_CheckAdminOptions');

function watermark_CheckAdminOptions(){

	if ($_GET['plugin'] == 'watermark'){
		watermark_AdminOptions();
	}
}

function watermark_AdminOptions(){

	global $PHP_SELF, $config;

    $folder = cute_parse_url($config['path_image_upload']);
    $folder = $folder['abs'];

	if ($_POST){
		echo_r($_POST);
		@extract($_POST, EXTR_SKIP);

		foreach ($images as $image => $value){
	        //Add watermark (text)
	        if ($watermark and $watermark_text){
	            if ($watermark_font == 'none'){
	                @add_watermark($folder.'/'.$image, $watermark_text, $hotspot1, ($textcolor ? $textcolor : 'FFFFFF'), ($textsize ? $textsize : '12'));
	            } else {
	                @add_watermark($folder.'/'.$image, $watermark_text, $hotspot1, ($textcolor ? $textcolor : 'FFFFFF'), ($textsize ? $textsize : '12'), data_directory.'/watermark/'.$watermark_font);
	            }
	        }

	        //Add watermark (image)
	        if ($merge){
	            @mergePix($folder.'/'.$image, data_directory.'/watermark/'.$watermark_image, $folder.'/'.$image, $hotspot2, ($merge_transition ? $merge_transition : '40'));
	        }
        }

		header('Location: '.$PHP_SELF.'?plugin=watermark');
	}

	echoheader('options', 'Watermark');
	@mkdir(data_directory.'/watermark', 0777);
?>

<form action="<?=$PHP_SELF; ?>?plugin=watermark" method="post" name="images">
<b><?=('Настройки вотермарка'); ?></b>
<table border="0" cellpading="0" cellspacing="0" width="350" class="panel">
 <tr>
  <td>
<?
	$handle = opendir(data_directory.'/watermark');
	while ($file = readdir($handle)){
	    
		$ext = explode('.', $file);
		$ext = strtolower(end($ext));

	    if ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'){
	        $watermarks[] = $file;
	    }
	    if ($ext == 'ttf'){
	        $fonts[] = $file;
	    }
	}
?>

<label for="watermark">
<input type="checkbox" name="watermark" id="watermark" onclick="javascript:ShowOrHide('make_watermark')"<?=((!extension_loaded('gd')) ? ' disabled' : ''); ?>><?=('Текст'); ?></label><br />
<span id="make_watermark" style="display: none;">
<table width="200" align="center">
<tr>
<td><?=('Текст'); ?></td>
<td><?=('Цвет'); ?></td>
<td><?=('Размер'); ?></td>
</tr>
<tr>
<td><input type="text" name="watermark_text" size="10" value="[date]"></td>
<td><input type="text" name="textcolor" maxlength="6" size="3" value="ffffff"></td>
<td><input type="text" name="textsize" maxlength="2" size="1" value="12"></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td><?=('Расположение'); ?></td>
<td><?=('Шрифт'); ?></td>
</tr>
<tr>
<td><input type="radio" name="hotspot1" value="1"> <input type="radio" name="hotspot1" value="2"> <input type="radio" name="hotspot1" value="3"><br />
<input type="radio" name="hotspot1" value="4"> <input type="radio" name="hotspot1" value="5" checked> <input type="radio" name="hotspot1" value="6"><br />
<input type="radio" name="hotspot1" value="7"> <input type="radio" name="hotspot1" value="8"> <input type="radio" name="hotspot1" value="9"></td>
<td valign="top">
<select name="watermark_font">
<option value="none">...</option>
<? foreach ($fonts as $font){ ?>
<option value="<?=$font; ?>"><?=$font; ?></option>
<? } ?>
</select>
</td>
</tr>
</table>
<br /></span>

<label for="merge"><input type="checkbox" name="merge" id="merge" onclick="javascript:ShowOrHide('make_merge')"<?=((!extension_loaded('gd')) ? ' disabled' : ''); ?>><?=('Картинка'); ?></label><br />
<span id="make_merge" style="display: none;">
<table width="200" align="center">
<tr>
<td><?=('Прозрачность'); ?></td>
</tr>
<tr>
<td><input type="text" name="merge_transition" maxlength="2" size="1" value="40"></td><td><?=('1 - прозрачная<br />99 - непрозрачная'); ?></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td><?=('Расположение'); ?></td>
<td><?=('Картинка'); ?></td>
</tr>
<tr>
<td width="50%"><input type="radio" name="hotspot2" value="1"> <input type="radio" name="hotspot2" value="5"> <input type="radio" name="hotspot2" value="2"><br />
<input type="radio" name="hotspot2" value="8"> <input type="radio" name="hotspot2" value="0" checked> <input type="radio" name="hotspot2" value="6"><br />
<input type="radio" name="hotspot2" value="4"> <input type="radio" name="hotspot2" value="7"> <input type="radio" name="hotspot2" value="3"></td>
<td width="50%" valign="top">
<select onchange="showpreview('data/watermark/'+this.options[this.selectedIndex].value, 'previewimage')" name="watermark_image">
<? foreach ($watermarks as $watermark_image){ ?>
<option value="<?=$watermark_image; ?>"><?=$watermark_image; ?></option>
<? } ?>
</select><br />
<img name="previewimage" width="100px" src="data/watermark/<?=$watermarks[0]; ?>" align="left" style="margin: 5px;">

</td>
</tr>
</table>
<br /></span>
</table>


<?php
	$handle = opendir($folder);
	while ($file = readdir($handle)){
		
		$ext = explode('.', $file);
		$ext = strtolower(end($ext));

	    if (in_array($ext, explode(', ', $config['type_images_upload']))){
	        $files[$file] = $file;
	    }
	}

	if (count($files)){
	    arsort($files);
	}
?>

<script>
function ckeck_uncheck_all_image(){

    frm = document.images;

    for (var i = 0; i < frm.elements.length; i++){
        var elmnt = frm.elements[i];

        if (elmnt.type == 'checkbox' && elmnt.id == 'checkbox'){
            if (frm.master_box.checked == true){
            	elmnt.checked = true;
            } else {
            	elmnt.checked = false;
            }
        }
    }

    if (frm.master_box.checked == true){
    	frm.master_box.checked = true;
    } else {
    	frm.master_box.checked = false;
    }
}
</script>

<? if ($files){ ?>
<br />
<table border="0" cellpading="0" cellspacing="0" width="100%">
<tr>
<td><input type="checkbox" name="master_box" title="<?=('Отметить все'); ?>" onclick="javascript:ckeck_uncheck_all_image()">
<? foreach ($files as $date => $image){ ?>
<tr>
<td><input name="images[<?=$image; ?>]" type="checkbox" value="on" id="checkbox">
<td>
<a href="<?=$config['path_image_upload'].'/'.$image; ?>"><?=$image; ?></a>
<? } ?>
</table>

<input type="submit" value="<?=t('Гашетку в пол'); ?>">
<? } ?>
</form>

<?
	echofooter();
}

#-------------------------------------------------------------------------------

/////////////////
//Function Watermark
//Code taken from http://edge.dev.box.sk/smsread.php?newsid=310
///////////////////
function add_watermark($thumb_in,$text="[date]",$hotspot=8,$rgbtext="FFFFFF",$font_size=12,$font="Arial.TTF",$datfmt="d-m-Y",$rgbtsdw="000000",$txp=15,$typ=5,$sxp=1,$syp=1) {

$suffx=substr($thumb_in,strlen($thumb_in)-4,4);
$suffx = strtolower($suffx);
if ($suffx==".jpg" || $suffx=="jpeg" || $suffx==".png" || $suffx==".gif") {
$text=str_replace("[date]",date($datfmt),$text);

if ($suffx==".jpg" || $suffx=="jpeg") {
$image=imagecreatefromjpeg($thumb_in);
}
if ($suffx==".png") {
$image=imagecreatefrompng($thumb_in);
}
if ($suffx == ".gif") {
$image=imagecreatefromgif($thumb_in);
}

$rgbtext=HexDec($rgbtext);
$txtr=floor($rgbtext/pow(256,2));
$txtg=floor(($rgbtext%pow(256,2))/pow(256,1));
$txtb=floor((($rgbtext%pow(256,2))%pow(256,1))/pow(256,0));

$rgbtsdw=HexDec($rgbtsdw);
$tsdr=floor($rgbtsdw/pow(256,2));
$tsdg=floor(($rgbtsdw%pow(256,2))/pow(256,1));
$tsdb=floor((($rgbtsdw%pow(256,2))%pow(256,1))/pow(256,0));

$coltext = imagecolorallocate($image,$txtr,$txtg,$txtb);
$coltsdw = imagecolorallocate($image,$tsdr,$tsdg,$tsdb);

if ($hotspot!=0) {
$ix=imagesx($image); $iy=imagesy($image); $tsw=strlen($text)*$font_size/imagefontwidth($font)*3; $tsh=$font_size/imagefontheight($font);
switch ($hotspot) {
case 1:
$txp=$txp; $typ=$tsh*$tsh+imagefontheight($font)*2+$typ;
break;
case 2:
$txp=floor(($ix-$tsw)/2); $typ=$tsh*$tsh+imagefontheight($font)*2+$typ;
break;
case 3:
$txp=$ix-$tsw-$txp; $typ=$tsh*$tsh+imagefontheight($font)*2+$typ;
break;
case 4:
$txp=$txp; $typ=floor(($iy-$tsh)/2);
break;
case 5:
$txp=floor(($ix-$tsw)/2); $typ=floor(($iy-$tsh)/2);
break;
case 6:
$txp=$ix-$tsw-$txp; $typ=floor(($iy-$tsh)/2);
break;
case 7:
$txp=$txp; $typ=$iy-$tsh-$typ;
break;
case 8:
$txp=floor(($ix-$tsw)/2); $typ=$iy-$tsh-$typ;
break;
case 9:
$txp=$ix-$tsw-$txp; $typ=$iy-$tsh-$typ;
break;
}
}

ImageTTFText($image,$font_size,0,$txp+$sxp,$typ+$syp,$coltsdw,$font,$text);
ImageTTFText($image,$font_size,0,$txp,$typ,$coltext,$font,$text);

if ($suffx==".jpg" || $suffx=="jpeg") {
imagejpeg($image, $thumb_in);
}
if ($suffx==".png") {
imagepng($image, $thumb_in);
}
if ($suffx == ".gif") {
imagegif($image, $thumb_in);
}
}
}
////////////////////
//Function mergePix
//Taken from http://de3.php.net/manual/de/function.imagecopymerge.php
///////////////////////

function mergePix($sourcefile,$insertfile, $targetfile, $pos=0,$transition=30) {
//Get the resource id?s of the pictures
switch (strtolower(end(explode('.', $sourcefile)))){
//switch (strtolower(end($sourcefile = explode('.', $sourcefile)))) {
		case 'gif':
			$sourcefile_id = imageCreateFromGIF($sourcefile);
			break;
		case 'jpg':
			$sourcefile_id = imageCreateFromJPEG($sourcefile);
			break;
		case 'png':
			$sourcefile_id = imageCreateFromPNG($sourcefile);
			break;
	}
switch (strtolower(end(explode('.', $insertfile)))) {
		case 'gif':
			$insertfile_id = imageCreateFromGIF($insertfile);
			break;
		case 'jpg':
			$insertfile_id = imageCreateFromJPEG($insertfile);
			break;
		case 'png':
			$insertfile_id = imageCreateFromPNG($insertfile);
			break;
	}

//Get the sizes of both pix
	$sourcefile_width=imageSX($sourcefile_id);
	$sourcefile_height=imageSY($sourcefile_id);
	$insertfile_width=imageSX($insertfile_id);
	$insertfile_height=imageSY($insertfile_id);

//middle
	if( $pos == 0 ) {
		$dest_x = ( $sourcefile_width / 2 ) - ( $insertfile_width / 2 );
		$dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
	}

//top left
		if( $pos == 1 ){
				$dest_x = 10;
				$dest_y = 10;
		}

//top right
		if( $pos == 2 ){
				$dest_x = $sourcefile_width - $insertfile_width - 10;
				$dest_y = 10;
		}

//bottom right
		if( $pos == 3 ){
				$dest_x = $sourcefile_width - $insertfile_width - 10;
				$dest_y = $sourcefile_height - $insertfile_height - 10;
		}

//bottom left
		if( $pos == 4 ){
				$dest_x = 10;
				$dest_y = $sourcefile_height - $insertfile_height - 10;
		}

//top middle
		if( $pos == 5 ){
				$dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 );
				$dest_y = 10;
		}

//middle right
		if( $pos == 6 ){
				$dest_x = $sourcefile_width - $insertfile_width - 10;
				$dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
		}

//bottom middle
		if( $pos == 7 ){
				$dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 );
				$dest_y = $sourcefile_height - $insertfile_height - 10;
		}

//middle left
		if( $pos == 8 ){
				$dest_x = 10;
				$dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
		}

//The main thing : merge the two pix
	imageCopyMerge($sourcefile_id, $insertfile_id,$dest_x,$dest_y,0,0,$insertfile_width,$insertfile_height,$transition);

//Create a jpeg/gif/png out of the modified picture
//switch (strtolower(end($sourcefile = explode('.', $sourcefile)))){
switch (strtolower(end(explode('.', $sourcefile)))){
		case 'gif':
			imagegif ($sourcefile_id,"$targetfile");
			break;
		case 'jpg':
			imagejpeg ($sourcefile_id,"$targetfile");
			break;
		case 'png':
			imagepng ($sourcefile_id,"$targetfile");
			break;
	}

}
?>