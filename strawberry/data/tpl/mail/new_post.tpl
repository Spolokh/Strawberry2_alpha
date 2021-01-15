<?
// inc/mod/addnews.mdu
?>

Subject: Новая публикация на сайте <?=$config['home_title']; ?>

<?=langdate($config['timestamp_comment'], $added_time); ?> добавлена новая новость от пользователя <?=$member['username']; ?>.

<?=$title; ?>
<?=replace_news('admin', $short_story); ?>

--
<?=$config['http_home_url']; ?>?id=<?=$id; ?>