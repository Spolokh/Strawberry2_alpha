<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Дополнительные поля
Plugin URI:     http://www.drtwister.net
Description: 	Create custom input fields stored in MySQL and use them in your templates.
Version: 		2.1
Application: 	Strawberry
Author: 		Dr.Twister
Author URI:     mailto:drtwister@drtwister.net
*/

// Add a CuteFiels option in the Admin menu
add_filter('options', 'cutefields_AddToOptions');
add_action('plugins', 'cutefields_CheckAdminOptions');

// CuteFields Admin Option
function cutefields_AddToOptions($options)
{
	global $PHP_SELF;

	$options[] = [
		'name' => t('Дополнительные поля'), 'url' => 'plugin=cutefields', 'category' => 'templates'
	];  
	return $options;
}

function cutefields_CheckAdminOptions(){
	if (isset($_GET['plugin']) and $_GET['plugin'] == 'cutefields'){
		cutefields_AdminOptions();
	}
}

// Replace CuteFields data when template is called from show_news.php
//add_filter('news-entry', 'cutefields');
//add_filter('news-comment', 'cutefields');
//
// CuteFields replacement function 

function cutefields()
{
	global $output, $sql, $id, $allow_full_story, $allow_active_news;
	
	$row = $sql->select(['fields', 'where' => $id]);

	if (!reset($row)) {
		return;
	}
	
	$numberfields = $sql->tableNumFields('fields');
	
	for ($i=2; $i < $numberfields; $i++ ) {
	    
		$field = $sql->tableFieldDirect('fields', $i);
		$thisField = $row[$field];	
		
		
		if (empty($row[$field])) {
		
			//$output = preg_replace('{\[ifcf:'.$field.'\](.*?)\[\/ifcf:'.$field.'\]}i', '', $output);
			$output  = str_replace('[ifcf:'.$field.']', '', $output);
			$output  = str_replace('[/ifcf:'.$field.']', '', $output);
			$output  = str_replace('{cf:'.$field.'}', '', $output);
			//$output  = preg_replace('/\[ifcf:'.$field.'\](.*?)\[\/ifcf:'.$field.'\]/is', '', $output);
			
		} else {
			$output = str_replace('[ifcf:'.$field.']', '', $output);
			$output = str_replace('[/ifcf:'.$field.']', '', $output);
			$output = str_replace('{cf:'.$field.'}', $row[$field], $output);
			$output  = preg_replace('/\[ifcf:'.$field.'\](.*?)\[\/ifcf:'.$field.'\]/is', '', $output);
		}	
	}
	return $output;	
}

// Template locations to display CuteField data.
//add_filter('template-active', 'template_cutefields');
//add_filter('template-full', 'template_cutefields');
//add_filter('template-variables-comments', 'template_cutefields');

// Descriptions to display in Template options.

/*function template_cutefields($template){
	global $sql;
	
	$template['[ifcf:X] and [/ifcf:X]'] = 'Shows the CuteField with the name X only if it has a value.';
	
	foreach ($sql->select(array('table' => 'fieldlist')) as $cl_row) {

		$field = $cl_row['field_name'];
		$desc  = $cl_row['field_desc'];
   		$template['{cf:'.$field.'}'] = 'Add the field "'.$field.'": '.$desc.'';
    }	return $template;
}*/

// Add News and Edit News forms
add_action('new-advanced-options', 'cutefields_AddEdit', 50);
add_action('edit-advanced-options', 'cutefields_AddEdit', 50);

// Add CuteFields to Submit News and Edit News forms
function cutefields_AddEdit(){
	global $sql, $mod, $row, $id;

	$thisID = $id;
	$buffer = '<fieldset>';
		
	$field = $sql->tableFieldDirect('fields', 2);
		
	foreach ($sql->select(['fields', 'where' => $id]) as $cfrow);
		
		foreach ($sql->select(['fieldlist']) as $flrow) {
			
			$thisField    = $flrow['field_name'];
			$thisDesc     = $flrow['field_desc'];
			$thisType     = $flrow['field_type'];
			$thisSize     = $flrow['field_size'];
			$thisOptionID = $flrow['option_id'];
			$thisValue    = $cfrow[$thisField];
			$boxValue     = explode(', ', $thisValue);
			$boxCount     = count($boxValue);
			
		foreach ($sql->select(['fieldoptions', 'where' => ['option_id ='.$thisOptionID]]) as $optrow);	
			
			if ($mod == "addnews") {
			
				if ($thisType == "String") { // Add a text box
			
					$buffer .= '<legend>'.$thisDesc.'</legend><input type="text" size="40" name="'.$thisField.'" id="'.$thisField.'"/><br /><br />';
				}
				
				elseif($thisType == "Text"){ // Add a text area
				
					$buffer .= '<legend>'.$thisField.'</legend><textarea name="'.$thisField.'" size="40" rows="8"></textarea></label><br /><br />';
					
					$buffer .= '<input type="hidden" name="'.$thisField.'_text" value="yes">';
				}
				
				elseif($thisType == "Radio Buttons"){ // Add Radio Buttons
				
					$buffer .= '<legend>'.$thisField.'</legend>';
				
				
					for ( $i=1; $i<=$thisSize; $i++ ) {
						
						$thisOption = $optrow['field_option'.$i];
   						$buffer .= '<label><input name="'.$thisField.'" type="radio" value="'.$thisOption.'" />'.$thisOption.'</label><br />';
					}
					$buffer .= '<br />';
				
				}
				
				elseif($thisType == "Checkboxes"){ // Add checkboxes
				
					$buffer .= '<legend>'.$thisField.'</legend>';
				
					for ($i=1; $i<=$thisSize; $i++ ) {
						
						$thisOption = $optrow['field_option'.$i];
   						$buffer .= '<label><input name="'.$thisField.'_cb[]" type="checkbox" value="'.$thisOption.'" />'.$thisOption.'</label><br />';
					}
					
					$buffer .= '<input type="hidden" name="'.$thisField.'_box" value="yes">';
					$buffer .= '<br />';
				}
				
				elseif($thisType == "Drop Down") { // Add Drop Down Menu
				
				$buffer .= '<legend>'.$thisField.'</legend>';
				$buffer .= '<select name="'.$thisField.'">';
				$buffer .= '<option>--- Select ---</option>';
				
				for ($i=1; $i<=$thisSize; $i++ ) {						
						$thisOption = $optrow['field_option'.$i];
						$buffer .= '<option value="'.$thisOption.'">'.$thisOption.'</option>';
				}
						
				$buffer .= '</select><br />';
				
				}
			}
			
			elseif($mod == "editnews"){
		
				if($thisType == "String"){ // Edit text boxes
			
					$buffer .= '<legend>'.$thisDesc.'</legend><input type="text" size="40" name="'.$thisField.'" id="'.$thisField.'" value="'.$thisValue.'"/><br /><br />';
				}
				
				elseif($thisType == "Text"){ // Edit text areas
				
					$buffer .= '<legend>'.$thisDesc.'</legend><textarea name="'.$thisField.'" cols="40" rows="8">'.$thisValue.'</textarea></label><br /><br />';					
					//$buffer .= '<legend>'.$thisField.'</legend><textarea name="'.$thisField.'" cols="40" rows="8">'.htmlspecialchars(replace_news('admin', $thisValue)).'</textarea></label><br /><br />';								
					$buffer .= '<input type="hidden" name="'.$thisField.'_text" value="yes">';
				
				} elseif($thisType == "Radio Buttons"){ // Edit radio buttons
				
					$buffer .= '<legend>'.$thisDesc.'</legend>';				
				
					for ($i=1; $i<=$thisSize; $i++ ) {
												
						$thisOption = $optrow['field_option'.$i];
						
						if($thisOption == $thisValue){
							$checked = 'checked="yes"';
						}	
   						$buffer .= '<label><input name="'.$thisField.'" type="radio" value="'.$thisOption.'" '.$checked.' />'.$thisOption.'</label><br />';
						$checked = "";
					}
					$buffer .= '<br />';
				
				} elseif($thisType == "Checkboxes"){ // Edit checkboxes
				
					$buffer .= '<legend>'.$thisDesc.'</legend>';
				
					for ($i=1; $i<=$thisSize; $i++ ) {
						
						$thisOption = $optrow['field_option'.$i];
						
						for ($x=0; $x<=$boxCount; $x++) {
							
							$thisBox = $boxValue[$x]; 
							if($thisOption == $thisBox){
								$checked[$i] = 'checked="yes"';
							}
							
						}
   						
						$buffer .= '<label><input name="'.$thisField.'_cb[]" type="checkbox" value="'.$thisOption.'" '.$checked[$i].' />'.$thisOption.'</label><br />';				
					}
					$buffer .= '<input type="hidden" name="'.$thisField.'_box" value="yes">';
					$buffer .= '<br />';
				} elseif($thisType == "Drop Down"){ // Edit drop down menus
				
				$buffer.= '<legend>'.$thisField.'</legend>';
				$buffer.= '<select name="' .$thisField. '">';
				
					for ($i=1; $i <= $thisSize; $i++) {
						
						$thisOption = $optrow['field_option'.$i];
   						
						if( $thisOption == $thisValue) {
							$checked = 'selected="yes"';
						}	
					
						$buffer .= '<option value="'.$thisOption.'" '.$checked.'>'.$thisOption.'</option>';
						$checked = '';
					}					
				        $buffer .= '</select><br /><br />';				
				}
			}
		}
		$buffer .= '</fieldset>';
	
	return $buffer;
}

// Save data on Add News and Edit News forms
add_action('new-save-entry', 'cutefields_Save', 25); 
add_action('edit-save-entry', 'cutefields_Save', 25);

function cutefields_Save(){ // Save CuteFields Data when News is Added or Edited
		
		global $sql, $id, $mod, $row;

		$thisID = $id;
		
		if($mod == "editnews"){	 // Save edited news	
			
			$rownum = $sql->table_num_rows('fields', 'id', $id);		
				
			if ($rownum == 0) {	
				$sql->insert(['fields', 'values' => ['id' => $id]]);
			}
			
			foreach ($sql->select(['fields', 'where' => array("id = ".$id)]) as $cfrow);
			
			$thisCF = $cfrow['cf_id'];
			
			$sql->update(array('news', 'values' => array('cf_id' => $thisCF), 'where' => array('id = '.$id)));
					
			foreach ($sql->select(array('table' => 'fieldlist')) as $flrow){
				
				$thisField = $flrow['field_name'];
				//$boxValue  = implode(', ',$_POST[$thisField.'_cb']);
				if (is_array($_POST[$thisField.'_cb']))
				{ 
                   $boxValue = implode(', ', $_POST[$thisField.'_cb']); 
				}
				
				$boxes     = $_POST[$thisField.'_box'];
				$textarea  = $_POST[$thisField.'_text'];
				$thisValue = $_POST[$thisField];
				
				if($boxes == "yes"){	
					$thisValue = $boxValue;
				}else{
					$thisValue = $_POST[$thisField];
				}

				if ($textarea == 'yes'){
				
					$thisValue = htmlspecialchars(replace_news('admin', $thisValue));
					
					$sql->update(array('fields',
						'where'  => array("cf_id = ".$thisCF),
						'values' => array($thisField => $thisValue)
			    	));
						
				} else {
							
					$sql->update(array('fields',
						'where'  => array("cf_id = ".$thisCF),
						'values' => array($thisField => $thisValue)
			    	));
				}
			}
		}
		elseif ($mod == "addnews") { // Add news with CuteFields
		
				$rownum = $sql->table_num_rows('fields', 'id', $id);		
				
				if($rownum == 0){
			
					$sql->insert(array('fields','values' => array('id' => $thisID)));					
					
					$addrow = $sql->select(['fields', 'where' => $thisID]);
					$addrow = reset($addrow);
					$thisCF = $addrow['cf_id'];
				
					foreach ($sql->select(array('table' => 'fieldlist')) as $flrow){
			
						$thisField = $flrow['field_name'];
						//$boxValue = implode(', ',$_POST[$thisField.'_cb']);
                        if(is_array($_POST[$thisField.'_cb'])){ 
                            $boxValue = implode(", ",$_POST[$thisField.'_cb']); 
                        }
						$boxes = $_POST[$thisField.'_box'];
						$textarea = $_POST[$thisField.'_text'];
						$thisValue = $_POST[$thisField];
				
						if($boxes == "yes"){	
							$thisValue = $boxValue;
						}else{
							$thisValue = $_POST[$thisField];
						}

						if ($textarea == 'yes'){
				
							$thisValue = htmlspecialchars(replace_news('admin',$thisValue));
					
							$sql->update(array(
								'table'  => 'fields',
								'where'  => array("cf_id = ".$thisCF),
								'values' => array($thisField => $thisValue)
								));
						
						} else {
							
							$sql->update(array(
								'table'  => 'fields',
								'where'  => array("cf_id = ".$thisCF),
								'values' => array($thisField => $thisValue)
			    				));
						}
					}		
					
				} else {
    }
  }
}

add_action('new-save-entry', 'cutefields_UpdateNews', 30); // Update news table with CuteFields ID
function cutefields_UpdateNews(){                          // Update news table with CuteFields ID
	global $sql, $id, $mod, $row;

	
	$addrow = reset($sql->select(array('fields', 'where' => array("id = $id"))));		
	$thisCF = $addrow['cf_id'];			
	$sql->update(array('news', 'where' => $id, 'values' => array("cf_id" => $thisCF)));

}

function cutefields_AdminOptions() { // Admin Options
	global $sql, $PHP_SELF, $config;

	$subaction = $_GET['subaction'];

	if (!$subaction) {

		echoheader('options', 'Дополнительные поля', 'CuteFields');
		
		
		if (!$sql->tableExists('fields')){
?>    
			<table cellspacing="0" cellpadding="0">
			<tr>
			<td width=25 align=middle>
			<img border="0" align="ABSmiddle" src="skins/images/help_small.gif"/>
			<td>&nbsp;<a onClick="javascript:Help('cutefields')" href="#">Help</a></table><br /><br />
				
			<form method="POST" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=addtable">
			CuteFields is not ready to use yet. <br />
			Please setup the MySQL tables by clicking the button below:<br /><br />
			<input type="submit" value="Add Table">
			</form>
	<?php
		} else {
	?>
				<table border="0" width="100%">
				<tr>
				<td width="25" align="middle">
				<img border="0" align="ABSmiddle" src="skins/images/help_small.gif"></td>
				<td> &nbsp;
				<!--a onClick="javascript:Help('cutefields')" href="#">Help</a-->
				<a Title="Дополнительные поля" href="index.php?mod=help&amp;section=cutefields" role="button" data-target="myModal" data-toggle="modal">Help</a>
				</tr></table><br />

				<form method="POST" name="fieldoptions" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=fieldtask">

				<table border="0" class="panel">
				 <tr align="center" class="enabled">
				  <td width="24%"><div align="left"><b>Field Name</b></div>
				  <td width="34%"><div align="left"><b>Field Description</b></div>
				  <td width="15%"><div align="left"><b>Field Type</b></div>
				  <td width="14%"><div align="center"><b>Field Length</b></div>
				  <td width="13%"><div align="right"></div>

		<?
				  $news_per_page = 21;
				  $start_from = ($_GET['start_from'] ? $_GET['start_from'] : '');
				  $i = $start_from;
				  $j = 0;
				  
				  $total_news = $sql->tableCount('fieldlist');

				 foreach ($sql->select(['fieldlist']) as $row) {
						 
						 if ($j < $start_from){
								 $j++;
								 continue;
						 }
						 $i++;

				 $bg = cute_that();
				 ?>

				 <tr <?=$bg; ?>>
				  <td width="25%"><div align="left"><?=$row['field_name'];?></div></td>
				  <td width="40%"><div align="left"><?=$row['field_desc'];?></div></td>
				  <td width="16%"><div align="left">
				    <?=$row['field_type'];?>
				    </div></td>
				  <td width="14%"><div align="center"><?=$row['field_size'];?></div></td>
				  <td width="5%"><div align="center">
				  <input type="radio" name="fieldid" value="<?=$row['field_id'];?>">
				  </div></td>
				  </tr>

				 <?

				 if ($i >= $news_per_page + $start_from){
						 break;
				 }
				}

				if ($start_from > 0){
						$previous = $start_from - $news_per_page;
						$npp_nav .= '<a href="'.$PHP_SELF.'?plugin=cutefields&amp;start_from='.$previous.'">&lt;&lt;</a>';
				}

				if ($total_news > $news_per_page){
						$npp_nav .= ' [ ';
						$enpages_count = @ceil($total_news / $news_per_page);
						$enpages_start_from = 0;
						$enpages = '';

					for ($j = 1; $j <= $enpages_count; $j++){
						if ($enpages_start_from != $start_from){
										$enpages .= '<a href="'.$PHP_SELF.'?plugin=cutefields&amp;start_from='.$enpages_start_from.'">'.$j.'</a> ';
							 } else {
										$enpages .= ' <b> <u>'.$j.'</u> </b> ';
							}

							   $enpages_start_from += $news_per_page;
						   }

						 $npp_nav .= $enpages;
				   $npp_nav .= ' ] ';
				}

				if ($total_news > $i){
						$npp_nav .= '<a href="'.$PHP_SELF.'?plugin=cutefields&amp;start_from='.$i.'">&gt;&gt;</a>';
				}
				?>

				<tr>
				 <td><?=$npp_nav; ?></td>
				</tr>
			   </table>
				<p align="right">
				  <label>
				  <select name="fieldtask">
				    <option value="fieldadd" selected="selected">Add</option>
				    <option value="fieldedit">Edit</option>
				    <option value="fielddel">Delete</option>
			      </select>
				  </label>
				  <input type="submit" value=" Go ">
				</p>
			  </form>

			<?php
		    	}
		   }

			// Options/Field Tasks
			elseif ($subaction == "fieldtask") {
				$thisID = $_POST['fieldid'];
				$thisTask = $_POST['fieldtask'];
				header('Location: '.$PHP_SELF.'?plugin=cutefields&subaction='.$thisTask.'&id='.$thisID.'');
		
			}
		
			// Verify Delete Action
		    elseif ($subaction == "fielddel") {
				global $row, $sql, $PHP_SELF;
				
				echoheader('options', 'CuteFields', 'CuteFields');
				$delID = $_GET['id'];
					
				foreach ($sql->select(array('table' => 'fieldlist', 'where' => array("field_id = ".$delID))) as $delrow);
					
				$delField = $delrow['field_name'];
						
				?>
				  <form method="POST" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=dodelete">
				   <table border="0" cellpading="0" cellspacing="0" width="100%" height="100%">
					<tr>
					 <td>Are you sure you would like to delete the CuteField: <?php echo $delField; ?><br /><br />
					     Please be aware, all content and data in this field and the options related to this field<br />
						 will be lost. Consider making a backup of your data before you proceed.<br /><br />
					     Continue?<br /><br />
					   <input name="submit" type="submit" value="Yes" />
					   <input type="button" value="No" onclick="javascript:document.location='<?=$PHP_SELF; ?>?plugin=cutefields'">
					   <input type="hidden" name="id" value="<?php echo $delID;?>">
					 </td>
					 </tr>
					</table>
				</form>
		   <?
		   }

			// Delete Fields From CuteFields Table
			elseif ($subaction == "dodelete") {
				global $row, $sql, $PHP_SELF;
			
				echoheader('options', 'CuteFields', 'CuteFields');
			
				$delID = $_POST['id'];
			
				foreach ($sql->select(array('fieldlist', 'where' => array("field_id = ".$delID))) as $delrow);
			
				$delField = $delrow['field_name'];
				$delOptions = $delrow['option_id'];
			
				$nsql = $sql->dropColumn('fields',$delField);
				
				$sql->delete(array(
					'table'	  => 'fieldlist',
					'where'  => array("field_id = ".$delID)
				));
				
				$sql->delete(array(
					'table'	  => 'fieldoptions',
					'where'  => array("option_id = ".$delOptions)
				));
				
				$auto = rand();
		  		echo 'Your field has been deleted.<br><br>'; 
				echo '<a href="'.$PHP_SELF.'?plugin=cutefields&amp;auto='.$auto.'">Go Back</a>';
			}

			// Edit Fields 
			elseif ($subaction == "fieldedit") {
				global $sql, $PHP_SELF;
			
				echoheader('options', 'CuteFields', 'CuteFields');
				$editID = $_GET['id'];
				
				$editrow = @reset($sql->select(['fieldlist', 'where' => ["field_id = ".$editID]]));						
						
				$editField     = $editrow['field_name'];
				$editDesc      = $editrow['field_desc'];
				$editFieldSize = $editrow['field_size'];
				$editFieldType = $editrow['field_type'];
				$editOptionID  = $editrow['option_id'];
										
				$editoptionrow = @reset($sql->select(array('table' => 'fieldoptions', 'where' => array("option_id = ".$editOptionID))));	
							
			?>
		
				The field type can not be edited.  If you want to make a change to the field type, <br />
				remove this field and create a new field with the type of field you want.<br />
				<br />

				<form method="POST" name="fieldedit" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=doedit">
		  		<table width="350" border="0" cellspacing="2" cellpadding="2">
            	<tr>
              	<td width="29%"><div align="left">Field Name : </div></td>
              	<td width="71%"><div align="left">
                <input type="text" name="fieldname" value="<? echo $editField; ?>"/>
              	</div></td>
            	</tr>
            	<tr>
              	<td><div align="left">Description : </div></td>
              	<td><div align="left">
                <label>
                <input name="fielddesc" type="text" size="40" value="<? echo $editDesc; ?>" />
                </label>
              	</div></td>
            	</tr>
            	<tr>
            	  <td><div align="left">Field Type : </div></td>
            	  <td><div align="left"><? echo $editFieldType; ?></div></td>
          	  </tr>
            	<tr>
            	  <td><div align="left">Field Size : </div></td>
            	  <td><div align="left"><? echo $editFieldSize; ?><input type="hidden" name="thisfieldsize" value="<? echo $editFieldSize; ?>" /></div></td>
          	  </tr>
          		</table>
				<br />
				
				<?php 
				
					if ($editOptionID > 0){
					
				?>
				<table width="426" border="0" cellspacing="2" cellpadding="2">
                  <tr>
                    <td width="24%"><div align="left"><strong>Option</strong></div></td>
                    <td width="58%"><div align="left"><strong>
                    <label></label>Value                    </strong></div></td>
                    <td width="18%"><div align="left"><strong>Remove</strong></div></td>
                  </tr>
                </table>
				<?php
						
						for ($i=1; $i <= $editFieldSize; $i++){
							
							$editThisOption = $editoptionrow['field_option'.$i];
				?>
				
							<table width="426" border="0" cellspacing="2" cellpadding="2">  
                  			<tr>
                    		<td width="24%"><div align="left">Option #<? echo $i; ?> : </div></td>
                    		<td width="58%"><div align="left">
                        	<label></label>
                    		<input name="field_option<? echo $i; ?>" type="text" size="40" value="<? echo $editThisOption; ?>" />
                    		</div></td>
                  			<td width="18%"><div align="left">
                  			  <input name="button" type="button" onclick="javascript:document.location='<?=$PHP_SELF; ?>?plugin=cutefields&amp;subaction=deloption&amp;optid=<? echo $editOptionID;?>&amp;optnum=<? echo $i;?>&amp;fieldid=<? echo $editID;?>'" value="Remove"/>
                  			</div></td>
                  			</tr>
                			</table>
				
				            
	              	<?php // End Listing Options
						}
					?>
					
						<table width="426" border="0" cellspacing="2" cellpadding="2">  
                  			<tr>
                    		<td width="24%">
                    		  <div align="right"></div></td>
                    		<td width="58%"><div align="left">
                    		  <div align="left">                   		      
                    		    <input name="button" type="button" onclick="javascript:document.location='<?=$PHP_SELF; ?>?plugin=cutefields&amp;subaction=addextra&amp;optid=<? echo $editOptionID;?>&amp;fieldid=<? echo $editID;?>'" value=" Add "/>
                   		      Additional Options</div></td>
                  			<td width="18%"><div align="left"></div></td>
                  			</tr>
       			        </table>
					<?php // End Displaying of Options if Options are Available
					} ?>
				
				<br />
				<table width="350" border="0" cellspacing="2" cellpadding="2">
                <tr>
                <td width="29%"><div align="left"></div></td>
                <td width="71%"><div align="left">
                <input type="hidden" name="editID" value="<? echo $editID ?>" />
                <input name="submit" type="submit" value="  Done  " />
                </div></td>
                </tr>
                </table>
				</form>
			<?php
			// End Edit Confirmation	
			}
			
			// Add Extra Option to Edit Field 
			elseif ($subaction == "addextra") {
				global $sql, $PHP_SELF;
		
				
				$fieldID = $_GET['fieldid'];
				$optionID = $_GET['optid'];
				
				//Add 1 option 
				$addoption = reset($sql->select(array('table' => 'fieldlist', 'where' => array("field_id = ".$fieldID))));

				$newsize = $addoption['field_size'] + 1;
				
				$sql->update(array('table' => 'fieldlist','values' => array('field_size' => $newsize), 'where' => array("field_id = ".$fieldID)));
				
				//Return to editing field
				$auto = rand();
				header('Location: '.$PHP_SELF.'?plugin=cutefields&subaction=fieldedit&id='.$fieldID.'&auto='.$auto.'');
			}
			
			// Delete one of the options.
			elseif ($subaction == "deloption") {
				global $sql, $PHP_SELF;
				
				$optionID   = $_GET['optid'];
				$optionNum  = $_GET['optnum'];
				$fieldID    = $_GET['fieldid'];
				
				$deloption  = @reset($sql->select(array('table' => 'fieldoptions', 'where' => array("option_id = ".$optionID))));
				$changesize = @reset($sql->select(array('table' => 'fieldlist', 'where' => array("field_id = ".$fieldID))));
				
				$currentsize = $changesize['field_size'];
				
				for ($i=$optionNum; $i<=$currentsize; $i++){
				
					$x = $i+1;
					$nextoption = $deloption['field_option'.$x];
					
					$sql->update(array(
						'table'  => 'fieldoptions',
						'values' => array('field_option'.$i => $nextoption),
						'where'  => array("option_id = ".$optionID)
			    	));

				}
				
				// Update field size count
				$newsize = $currentsize - 1;
				
				$sql->update(array(
					'table'  => 'fieldlist',
					'values' => array('field_size' => $newsize),
					'where'  => array("field_id = ".$fieldID)
			    	));
					
				//Return to editing field
				$auto = rand();
				header('Location: '.$PHP_SELF.'?plugin=cutefields&subaction=fieldedit&id='.$fieldID.'&auto='.$auto.'');
			}
			
			// Do edit the field and confirm
			elseif ($subaction == "doedit") {
				global $sql, $PHP_SELF;
		
				echoheader('options', 'CuteFields', 'CuteFields');
			
				$editID = $_POST['editID'];
				$options = @reset($sql->select(array('fieldlist', 'where' => array("field_id = ".$editID))));
				$optionsID = $options['option_id'];
				
				$newfieldname = $_POST['fieldname'];
				$newfielddesc = $_POST['fielddesc'];
			
				$sql->rename_column('fields', $oldfieldname, $newfieldname);
				
				$sql->update(array('fieldlist',
					'values' => array(
						'field_name' => $newfieldname,
						'field_desc' => $newfielddesc),
					'where' => array("field_id = ".$editID)
			    	));
					
				$thisfieldsize = $_POST['thisfieldsize'];
					
				for ($i=1; $i<=$thisfieldsize; $i++){
						
						$thisOption = $_POST['field_option'.$i];
						
						$sql->update(array(
							'table'  => 'fieldoptions',
							'values' => array('field_option'.$i => $thisOption),
							'where'  => array('option_id = '.$optionsID)
			    			));
					}
				
		  		echo 'Your field has been edited.<br><br>'; 
				echo '<a href="'.$PHP_SELF.'?plugin=cutefields">Go Back</a>';
			}

			// Add Fields Form to CuteFields
			elseif ($subaction == "fieldadd") {
				global $sql, $PHP_SELF;
			
				echoheader('options', 'CuteFields', 'CuteFields');
		
			?>
		
			<form method="POST" name="fieldoptions" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=fieldadd2">
		  	<table width="450" border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td width="22%"><div align="left">Field Name :</div></td>
              <td width="78%"><div align="left">
                <input type="text" name="fieldname" />
              </div></td>
            </tr>
            <tr>
              <td><div align="left">Description :</div></td>
              <td><div align="left">
                <input name="fielddesc" type="text" size="40" />
              </div></td>
            </tr>
            <tr>
              <td><div align="left">Field Type :</div></td>
              <td><div align="left">
                <label>
                <select name="fieldtype">
                  <option value="textbox" selected="selected">Text Box</option>
                  <option value="checkbox">Check Box</option>
                  <option value="radio">Radio Button</option>
                  <option value="dropdown">Drop Down Select</option>
                  <option value="textarea">Text Area</option>
                </select>
                </label>
              </div></td>
            </tr>
            <tr>
              <td>Field Size : </td>
              <td><div align="left">
                <input name="fieldsize" type="text" size="4" />
              (Number of Options or Size of Text Area) </div></td>
            </tr>
            <tr>
              <td><div align="left"></div></td>
              <td><div align="left">
                <label></label>
              </div></td>
            </tr>
            
            <tr>
              <td><div align="left"></div></td>
              <td>
                <div align="left">
                  <input name="submit" type="submit" value=" Next " />
                </div></td>
            </tr>
          </table>
		</form>
		<?php	
		}
			elseif ($subaction == "fieldadd2") { // Add Fields Form to CuteFields
				global $sql, $PHP_SELF;
			
				echoheader('options', 'CuteFields', 'CuteFields');
				$cur_fieldname = $_POST['fieldname'];
				$cur_fielddesc = $_POST['fielddesc'];
				$cur_fieldtype = $_POST['fieldtype'];
				$cur_fieldsize = $_POST['fieldsize'];
		
			?>
		
			<form method="POST" name="fieldoptions" action="<?=$PHP_SELF;?>?plugin=cutefields&amp;subaction=doadd">
			
			Please confirm the information below. If you are creating a list of options<br />
			enter your options now. If you would like to change your field type,<br />
			hit your browsers back button.<br /><br />

		  	<table width="350" border="0" cellspacing="2" cellpadding="2">
            <tr>
              <td width="28%"><div align="left">Field Name :</div></td>
              <td width="72%"><div align="left">
                <input type="text" name="fieldname" value="<? echo $cur_fieldname; ?>" />
              </div></td>
              </tr>
            
            <tr>
              <td><div align="left">Description :</div></td>
              <td><div align="left">
                <label>
                <input name="fielddesc" type="text" size="40" value="<? echo $cur_fielddesc; ?>" />
                </label>
              </div></td>
              </tr>
          </table>
		    <?php 	
			if ($cur_fieldtype == 'textbox'){	
			?>
				<table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left">Field Type :</div></td>
                <td width="72%"><div align="left">
                    <input type="text" name="fieldtypedis" value="String" readonly />
					<input type="hidden" name="fieldtype" value="string" readonly />
                </div></td>
              </tr>
              <tr>
                <td><div align="left">Field Size  :</div></td>
                <td><div align="left">
                    <label>
                    <input name="fieldsize" type="text" size="4" value="255" readonly />
                    </label>
                (255 Characters by Default) </div></td>
              </tr>
            </table>
				
				
				
			<?php } elseif ($cur_fieldtype == 'checkbox'){ ?>
			<table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left">Field Type :</div></td>
                <td width="72%"><div align="left">
                    <input type="text" name="fieldtypedis" value="Checkboxes" readonly />
					<input type="hidden" name="fieldtype" value="checkbox" readonly />
                </div></td>
              </tr>
              <tr>
                <td><div align="left">Field Size :</div></td>
                <td><div align="left">
                    <label>
                    <input name="fieldsize" type="text" size="4" value="<? echo $cur_fieldsize; ?>" readonly />
                    </label>
                </div></td>
              </tr>
            </table>
		    <br />
			
				<?php 	
					for ($i=1; $i<=$cur_fieldsize; $i++){
				 ?>
		    		<table width="350" border="0" cellspacing="2" cellpadding="2">
              		<tr>
                	<td width="28%">Option #<? echo $i;?> : </td>
                	<td width="72%"><div align="left">
                  	<input type="text" name="field_option<? echo $i;?>" />
                	</div></td>
             		</tr>
       		  </table>
				<?php
				 	}				 

			} elseif ($cur_fieldtype == 'radio'){
						?>
			<table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left">Field Type :</div></td>
                <td width="72%"><div align="left">
                    <input type="text" name="fieldtypedis" value="Radio Buttons" readonly />
					<input type="hidden" name="fieldtype" value="radio" readonly />
                </div></td>
              </tr>
              <tr>
                <td><div align="left">Field Size  :</div></td>
                <td><div align="left">
                    <label>
                    <input name="fieldsize" type="text" size="4" value="<? echo $cur_fieldsize; ?>" readonly />
                    </label>
                </div></td>
              </tr>
            </table>
		    <br />
			
				<?php 	
					for ($i=1; $i<=$cur_fieldsize; $i++){
				 ?>
		    		<table width="350" border="0" cellspacing="2" cellpadding="2">
              		<tr>
                	<td width="28%">Option #<? echo $i;?> : </td>
                	<td width="72%"><div align="left">
                  	<input type="text" name="field_option<? echo $i;?>" />
                	</div></td>
              		</tr>
       		  </table>
				<?php
				 	}				 
			
			}elseif ($cur_fieldtype == 'dropdown'){
				?>
			<table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left">Field Type :</div></td>
                <td width="72%"><div align="left">
                    <input type="text" name="fieldtypedis" value="Drop Down" readonly />
					<input type="hidden" name="fieldtype" value="dropdown" readonly />
                </div></td>
              </tr>
              <tr>
                <td><div align="left">Field Size  :</div></td>
                <td><div align="left">
                    <label>
                    <input name="fieldsize" type="text" size="4" value="<? echo $cur_fieldsize; ?>" readonly />
                    </label>
                </div></td>
              </tr>
            </table>
		    <br />
			
				<?php 	
					for ($i=1; $i<=$cur_fieldsize; $i++){
				 ?>
		    		<table width="350" border="0" cellspacing="2" cellpadding="2">
              		<tr>
                	<td width="28%">Option #<? echo $i;?> : </td>
                	<td width="72%"><div align="left">
                  	<input type="text" name="field_option<? echo $i;?>" />
                	</div></td>
              		</tr>
       		  </table>
				<?php
				 	}
					
			}elseif ($cur_fieldtype == 'textarea'){
			?>
				<table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left">Field Type :</div></td>
                <td width="72%"><div align="left">
                    <input type="text" name="fieldtypedis" value="Text" readonly />
					<input type="hidden" name="fieldtype" value="text" readonly />
                </div></td>
              </tr>
              <tr>
                <td><div align="left">Field Size  :</div></td>
                <td><div align="left">
                    <label>
                    <input name="fieldsize" type="text" size="4" value="<? echo $cur_fieldsize; ?>" readonly />
                    </label>
                (Size of Text Area) </div></td>
              </tr>
            </table>
			<? } ?>
		    <br />
		    <table width="350" border="0" cellspacing="2" cellpadding="2">
              <tr>
                <td width="28%"><div align="left"></div></td>
                <td width="72%">
                    <div align="left">
                      <input name="submit" type="submit" value=" Add " />
                    </div>
            </table>
			</form>
		    <p>
         <?
		}
		// Do add the new field and confirm
			elseif ($subaction == "doadd") {
				global $sql, $PHP_SELF;
		
				echoheader('options', 'CuteFields', 'CuteFields');
			
				$fieldname = $_POST['fieldname'];
				$fielddesc = $_POST['fielddesc'];
				$fieldtype = $_POST['fieldtype'];
				$fieldsize = $_POST['fieldsize'];
				$fieldtypedis = $_POST['fieldtypedis'];
				$fieldname = str_replace(' ', '', $fieldname);
			
				if($fieldtype == "string"){
				
					// Add to database standard string field
					$sql->altertable(array(
						'table'	  => 'fields',
						'name'	  => $fieldname,
						'action'  => 'insert',
						'values'  =>  array('type' => 'string')
			 			));
			
					$sql->insert(array(
						'table'  => 'fieldlist',
						'values' => array(
							'field_name' => $fieldname,
							'field_desc' => $fielddesc,
							'field_type' => 'String',
							'field_size' => '255'
							)
			    		));
				
				}
				elseif($fieldtype == "text") {
				
					$sql->altertable(array( // Add to database text area field
						'table'	  => 'fields',
						'name'	  => $fieldname,
						'action'  => 'insert',
						'values'  =>  array('type' => 'text')
			 			));
			
					$sql->insert(array(
						'table'  => 'fieldlist',
						'values' => array(
							'field_name' => $fieldname,
							'field_desc' => $fielddesc,
							'field_type' => 'Text',
							'field_size' => $fieldsize
							)
			    		));
				}
				
				else{
				
					$sql->altertable(array( // Add to Database multiple options
						'table'	  => 'fields',
						'name'	  => $fieldname,
						'action'  => 'insert',
						'values'  =>  array('type' => 'string')
			 			));
			
					$optionID = $sql->last_insert_id('fieldoptions', '', 'option_id') + 1;
					
					$sql->insert(array(
						'table'  => 'fieldoptions',
						'values' => array('option_id' => $optionID)
			    		));
					
					for ($i=1; $i<=$fieldsize; $i++){
					
						$sql->altertable(array(
							'table'	  => 'fieldoptions',
							'name'	  => 'field_option'.$i,
							'action'  => 'insert',
							'values'  =>  array('type' => 'string')
			 				));
						
						$thisOption = $_POST['field_option'.$i];
						
						$sql->update(array(
							'table'  => 'fieldoptions',
							'values' => array('field_option'.$i => $thisOption),
							'where'  => array('option_id = '.$optionID)
			    			));
					}
					
					$sql->insert(array(
						'table'  => 'fieldlist',
						'values' => array(
							'field_name' => $fieldname,
							'field_desc' => $fielddesc,
							'field_type' => $fieldtypedis,
							'field_size' => $fieldsize,
							'option_id'  => $optionID
							)
			    		));				
					
				}
				
				$auto = rand();
		  		echo 'Your field has been added.<br><br>'; 
				echo '<a href="'.$PHP_SELF.'?plugin=cutefields&amp;auto='.$auto.'">Go Back</a>';
                
			} elseif ($subaction == "addtable") { // Add Cute Fields Tables
				global $sql, $PHP_SELF;
				
	   		 $sql->createTable(array(
				'table'	  => 'fields',
				'columns' => array(
					'cf_id' => array('type'	=> 'int','auto_increment' => 1,'primary' => 1),
					'id'	=> array('type' => 'int')
					)));
					
			 $sql->createTable(array(
				'table'	  		=> 'fieldlist',
				'columns' 		=> array(
					'field_id' 		=> array('type' => 'int','auto_increment' => 1,'primary' => 1), 
					'field_name'	=> array('type' => 'string'),
					'field_desc'	=> array('type' => 'string'),
					'field_type'	=> array('type' => 'string'),
				 	'field_size'	=> array('type' => 'int'),
					'option_id'		=> array('type'	=> 'int') 
					)));
					
			$sql->createTable(array(
				'table'	  		=> 'fieldoptions',
				'columns' 		=> array(
					'option_id' 	=> array('type' => 'int','auto_increment' => 1,'primary' => 1),
					'field_option1'	=> array('type' => 'string'),
					'field_option2'	=> array('type' => 'string'),
					'field_option3'	=> array('type' => 'string'),
				 	'field_option4'	=> array('type' => 'string'),
					'field_option5'	=> array('type'	=> 'string')
					)));

		 	 $sql->alterTable(array(
				'table'	  => 'news',
				'name'	  => 'cf_id',
				'action'  => 'insert',
				'values'  =>  array('type' => 'int','default' => '0')
			 	));


			echoheader('options', 'CuteFields', 'CuteFields');
			echo 'The CuteFields Table has been created.<br><br>';
			echo '<a href="'.$PHP_SELF.'?plugin=cutefields">Go Back</a>';
	  		}

   echofooter();
}

add_action('mass-deleted', 'cutefieldsDelete');

function cutefieldsDelete()
{
	global $row, $id, $sql;

    $delID = $row['id'];

	$sql->delete(['fields', 'where' => ["post_id = ".$delID]]);

}

add_filter('help-sections', 'cutefieldsHelp');

function cutefieldsHelp($help_sections){
$help_sections['cutefields'] = <<<HTML
 
<p><b>CuteFields</b><br /><br />
CuteFields allows you to create custom input fields for your news posts and then be able to call
the custom fields through embed variables inside of your CuteNews templates.  This plugin ONLY works with the MySQL version of CuteNews.ru. <br />
<br />
CuteFields was written by <a href='mailto:drtwister@drtwister.net'>Dr.Twister</a>.
</p>
<p>
  <b>Installing CuteFields</b>
  <ol>
    <li>Copy cutenews.php to the CuteNews/Plugins directory.</li>
    <li>Copy mysql.class.php to the CuteNews/inc/db directory.</li>
    <li>Activate the plugin from the CuteNews admin menu using the Manage Plugins link.</li>
    <li>Select to Activate CuteFields.</li>
    <li>Select the CuteFields options menu.</li>
    <li>Create the CuteFields Tables using the button that appears in the top of the options menu.</li>
    <li>Begin adding custom fields.</li>
  </ol>
</p>
<p>
  <b>Creating CuteFields</b>
  <ol>
    <li>Within the CuteFields options menu, select from the drop down menu ADD and click the GO button.</li>
    <li>Enter the name of the new field. This is the name that will appear in your embed variable {cf:name}</li>
    <li>Enter a description of the field for your reference.</li>
	<li>Select the type of field you would like. Choose between Text Box, Drop Down, Checkboxes, Radio Buttons and Text Area.</li>
	<li>If your field type requires options, type your options in</li>
	<li>Click the Add Button</li>
  </ol>
</p>
<p>
  <b>Field Name</b>: The name of your field. This will be used in your templates as {cf:name}. Field names will automatically remove spaces and is case sensitive; {cf:color} is different from {cf:Color}.
</p>
<p>
  <b>Field Description</b>: A brief description of your field. This is simple for your personal reference.
</p>
<p>
  <b>Field Type</b>: There are five choices of fields you can create. Text Box, Text Area, Drop Down, Radio Buttons, Checkboxes.  The Drop Down, Radio Buttons and Checkboxes allow for as many options as you would like. Enter the number of options you would like to have in the Field Size box and enter your options on the following page.
</p>
<p>
  <b>Field Size</b>: By default, certain types have set field sizes. For example, the text is automatically identified as a string capable of holding 255 characters. If you want more space, use a text area field.  The field size also determines how many options are available in a drop down, radio button or checkbox type of field.  For example if you select Radio Button and a field size of 3, there will be 3 radio button options.
</p>
<p>
  <b>Field Options</b>: Enter whatever value you would like to have appear in your field. For example you could have a Radio Button with 2 options, Male and Female. When you enter your news, the radio options will appear for easy input.
</p>
<p>
  <b>Using CuteFields</b><br /><br />
Inside your templates you should now see options for your new custom fields. They will appear
in the format of {cf:fieldname}.  So for example if your custom field name is color the template
variable will be {cf:color}. In your template it might look like this:<br />
<br />
td bgcolor="{cf:color}" I like the color {cf:color}<br />
<br />
CuteFields also has a conditional setting. In order to use conditional settings, use the format [ifcf:name]...[/ifcf:name].  Your template might look like this:<br />
<br />
[ifcf:color]I love {cf:color} balloons![/ifcf:color]
</p>
<p>
  <b>Deleting CuteFields</b>
  <ol>
	<li>Within the CuteFields options menu, select from the drop down menu DELETE and click the GO button.</li>
	<li>A confirmation message will appear to make sure you want to delete your field.</li>
	<li>Click Yes and the field is deleted.</li>
</ol>
Please note: Deleting a CuteField will also erase ALL data associated with the field. USE WITH CAUTION!
Back up your database before you delete fields just to be safe.
</p>
HTML;
return $help_sections;
}
?>