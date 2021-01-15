<!--h1 class="breadcrumbs" id="tabinfo">  
	<?php //if ( isset($tpl['user']['member']['id']) && $tpl['user']['id'] === $member['id'] ) { ?>
	<a class="icon icon-pencil" href="/profile"> &nbsp; Редактировать</a>
	<?php //} ?>
	Профиль пользователя <?=$tpl['user']['name']; ?>
</h1-->

<tr>
	<td valign="top" width="160">
		<div class="avatar">
			<img width="150" src="/uploads/thumb.php?src=<?=$tpl['user']['avatar'];?>&w=150&h=150&s=1" />
		</div>
	<td valign="top">
	<form id="editprofile">  
		
		<h4>Основное</h4>
		<dl>
			<dt> &nbsp; Логин:</dt> 
			<dd><b><?=$tpl['user']['username'];?></b></dd>
		</dl>
		<dl>
			<dt> &nbsp; Имя, Фамилия:</dt>     
			<dd><b><?=$tpl['user']['name']; ?></b></dd>
		</dl>
		<dl>
			<dt> &nbsp; Год рождения:</dt>     
			<dd><b><?=$tpl['user']['age']; ?></b></dd>
		</dl>
		<?php if ( !empty($tpl['user']['mail']) ) { ?>
		<dl>
			<dt> &nbsp; Почта:</dt>     
			<dd><b><a><?=$tpl['user']['mail']; ?></a></b></dd>
		</dl>
		<?php } ?>
		
		<dl>
			<dt> &nbsp; Блог:</dt> 	
			<dd><b><a href="<?=$tpl['user']['author'] ?>"><?=t('Всего публикаций')?> <?=$tpl['user']['publications']?></a></b></dd>
		</dl>
		
		<?php if ( !empty($tpl['user']['lj-username']) ) { ?>
		<dl>
			<dt> &nbsp; О пользователе:</dt>    
			<dd style="padding:8px 0;line-height:18px;">
				<?=$tpl['user']['about'] ?>
			</dd>
		</dl>
		<?php } ?>
		
		<h4>Контакты</h4>
		<dl>
			<dt> &nbsp; Откуда (город):</dt>   
			<dd><a class="icon icon-map-marker"></a> &nbsp; <b><?=$tpl['user']['location']; ?></b></dd>
		</dl>
		
		<?php if ( !empty($tpl['user']['phone']) ) { ?>
		<dl>
			<dt> &nbsp; Телефон (GSM):</dt>   
			<dd><a class="icon icon-phone"></a> &nbsp; <b><?=$tpl['user']['phone']; ?></b></dd>
		</dl>
		<?php } ?>
		<?php if ( !empty($tpl['user']['homepage']) ) { ?>
		<dl>
			<dt> &nbsp; Сайт:</dt> 	
			<dd><b><a><?=$tpl['user']['homepage']; ?></a></b></dd>
		</dl>
		<?php } if ( !empty($tpl['user']['lj-username']) ) { ?>
		<dl>
			<dt> &nbsp; Блог в LJ </dt>    
			<dd><b><?=$tpl['user']['lj-username']; ?></b></dd>
		</dl>
		<?php } ?>
		<!--dl>
			<dt> &nbsp; </dt>    
			<dd> &nbsp; 
			<div id="contentEditable" contenteditable="true" placeholder="Type something..."></div>
			</dd>
		</dl-->
	</form>