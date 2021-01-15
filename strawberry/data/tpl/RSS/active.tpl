<item>
<title><?=htmlspecialchars($tpl['post']['title']); ?></title>
<description><?=htmlspecialchars($tpl['post']['short-story']); ?></description>
<pubDate><?=date('r', $tpl['post']['_']['date']); ?></pubDate>
<dc:creator><?=htmlspecialchars($users[$row['author']]['name']); ?></dc:creator>
<guid isPermaLink="false"><?=htmlspecialchars($tpl['post']['link']['post']); ?></guid>
<link><?=htmlspecialchars($tpl['post']['link']['post']); ?></link>
<comments><?=htmlspecialchars($tpl['post']['link']['post']); ?></comments>
<wfw:commentRSS><?=htmlentities($tpl['post']['link']['rss.php/post']); ?></wfw:commentRSS>

<? if ($tpl['post']['category']['name']){ ?>
<? foreach (explode(',', $tpl['post']['_']['category']) as $cat){ ?>
<category><?=htmlspecialchars($categories[$cat]['name']); ?></category>
<? } ?>
<? } ?>
</item>