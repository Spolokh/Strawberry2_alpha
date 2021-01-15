<?php
function langdate($langdateformat, $langdatetimestamp = ''){
global $config;

    $langdatetimestamp = ($langdatetimestamp ? $langdatetimestamp : time);

    $result = date($langdateformat, $langdatetimestamp);
    $result = run_filters('langdate', $result);

return $result;
}
?>