<?php
//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

/**
 * @param        $uid
 * @param  int   $limit
 * @param  int   $offset
 * @return array
 */
function news_useritems($uid, $limit = 0, $offset = 0)
{
    global $xoopsDB;
    $ret = [];

    $sql    = 'SELECT storyid FROM ' . $xoopsDB->prefix('news_stories') . ' WHERE published>0 AND published<=' . time() . ' AND uid=' . $uid;
    $result = $xoopsDB->query($sql, $limit, $offset);
    if ($result) {
        while ($row = $xoopsDB->fetchArray($result)) {
            $ret[] = $row['storyid'];
        }
    }

    return $ret;
}

/**
 * @param        $items
 * @param  int   $limit
 * @param  int   $offset
 * @return array
 */
function news_iteminfo($items, $limit = 0, $offset = 0)
{
    global $xoopsDB;
    $ret     = [];
    $URL_MOD = XOOPS_URL . '/modules/news';

    $sql    = 'SELECT s.storyid, s.title, s.published, s.hometext, s.nohtml, s.nosmiley, s.created, s.uid, s.counter, s.comments, t.topic_id, t.topic_title
    FROM ' . $xoopsDB->prefix('news_stories') . ' s, ' . $xoopsDB->prefix('news_topics') . ' t
    WHERE s.topicid=t.topic_id
    AND s.storyid IN (' . implode(',', $items) . ')
    AND s.published>0
    AND s.published<=' . time() . '
    ORDER BY s.published DESC';
    $result = $xoopsDB->query($sql, $limit, $offset);

    $i = 0;
    while ($row = $xoopsDB->fetchArray($result)) {
        $storyid             = $row['storyid'];
        $ret[$i]['link']     = $URL_MOD . '/article.php?storyid=' . $storyid;
        $ret[$i]['pda']      = $URL_MOD . '/print.php?storyid=' . $storyid;
        $ret[$i]['cat_link'] = $URL_MOD . '/index.php?storytopic=' . $row['topic_id'];
        $ret[$i]['title']    = $row['title'];
        $ret[$i]['time']     = $row['published'];
        // uid
        $ret[$i]['uid'] = $row['uid'];
        // category
        $ret[$i]['cat_name'] = $row['topic_title'];
        // counter
        $ret[$i]['hits']    = $row['counter'];
        $ret[$i]['replies'] = $row['comments'];
        // description
        $myts   = MyTextSanitizer::getInstance();
        $html   = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($row['nohtml']) {
            $html = 0;
        }
        if ($row['nosmiley']) {
            $smiley = 0;
        }
        $ret[$i]['description'] = $myts->displayTarea($row['hometext'], $html, $smiley, $xcodes);
        ++$i;
    }

    return $ret;
}
