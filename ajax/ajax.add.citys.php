<?php

include_once substr(dirname(__FILE__), 0, -5).'/head.php';

$autocomlete = array();

/*
$pets = array(
'�����������',
'���������',
'��������',
'�������',
'��������',
'���������',
'������������',
'�������',
'������',
'�����������',
'��������',
'���������',
'�������',
'�������',
'�������',
'������������',
'�������',
'������',
'�������',
'������-���',
'������',
'�����������',
'������',
'�������',
'��������',
'�����',
'�����������-��-�����',
'��������',
'���������',
'����������',
'������',
'�����',
'������',
'�������',
'������������',
'���������',
'���������',
'������',
'��������',
'���������� �����',
'�������',
'������ ��������',
'������ �����',
'��������',
'�����������',
'�����������',
'����',
'����',
'��������',
'�����',
'�����',
'������������',
'�������������-����������',
'�����',
'���������',
'������-��-����',
'�������',
'������',
'������',
'�����-���������',
'�������',
'�������',
'��������',
'����',
'����������',
'������ �����',
'������',
'���������',
'��������',
'������',
'��������',
'�����',
'����',
'������',
'���������',
'���',
'���������',
'���������',
'���������',
'����',
'�������',
'������',
'���������'
);
*/

if (isset($_POST['receiver'])) :

	$request     = $_POST['receiver'];
	$autocomlete = [];
	foreach ( $sql->select(['users', 'where' => ['deleted = 0'], 'select' => ['username','mail','name','id']]) as $row ) { //��������� $pets � ������������� ������ $names
		$autocomlete[] = ($row['name'] ? $row['name'] : $row['username']). '<'.$row['mail'].'>';
	}  
	
	$query = $autocomlete;

endif;

//$pets = $sql->select(array('table' => 'categories', 'select' => array('name')));
//$names = array();
//foreach ($pets as $row) {$names[] = $row['name'];}  //��������� $pets � ������������� ������ $names
//$pets = $names; //�������� "������" ������ $pets ��� ����������� ������
//include_once languages_directory.'/'.$config['lang'].'/country.inc.php';
//$pets   = $_country_lang;
//$_POST['register']['location'] = iconv('utf-8', 'cp1251', $_POST['register']['location']);

$return  = array();
$request = iconv('utf-8', 'cp1251', $request);
$request = strtolower($request);

function str_srch($item, $key) { 
	global $return, $request; 
	if(strtolower(substr($item, 0, strlen($request))) == $request) 
	$return[] = $item;
}  	array_walk($query,'str_srch');


if(count($return) > 0) echo '<ul><li>'.implode('</li><li>',$return).'</li></ul>';
else echo '<span></span>';
?>