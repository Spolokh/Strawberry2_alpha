<item>
<title><?=htmlspecialchars($tpl['comment']['author']); ?></title>
<dc:creator><?=htmlspecialchars($tpl['comment']['author']); ?></dc:creator>
<link><?=htmlspecialchars($tpl['post']['link']['post']); ?>#comment<?=$tpl['comment']['number']; ?></link>
<guid><?=htmlspecialchars($tpl['post']['link']['post']); ?>#comment<?=$tpl['comment']['number']; ?></guid>
<description><?=htmlspecialchars($tpl['comment']['story']); ?></description>
<pubDate><?=date('r', $tpl['comment']['_']['date']); ?></pubDate>
</item>