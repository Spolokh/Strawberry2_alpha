<td colspan="2">
<p>����� �� ������� <?=langdate('d M Y - H:i'); ?><br>
<b>��������� ��� ������������ <?=$user_id; ?></b></p>.
<tr>
<td width="120">
<div>
<? if ($row['avatar']){ 
echo '<img src="'.$config['path_userpic_upload'].'/'.$user_id.'.'.$users[$user_id]['avatar'].'">';
} else {
  echo '<img src="'.$config['path_userpic_upload'].'/noavatar.png" width="120">';
}
?>
</div>
<td>
<div>
<form method="post">
 <input type="text" name="title"><br/>
 <textarea name="text"></textarea><br/>
    <input type="submit" value="   ���������   ">
 </form>
</div>
<? close_table(); ?>