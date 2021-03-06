<?php
//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

/**
 * @param $options
 * @return array
 */
function b_mycomments_show($options)
{
    $block = [];
    require_once XOOPS_ROOT_PATH . '/modules/mycomments/include/comment_constants.php';
    $commentHandler = xoops_getModuleHandler('comment', 'mycomments');
    $criteria       = new CriteriaCompo(new Criteria('com_status', MYCOM_ACTIVE));
    $criteria->setLimit((int)$options[0]);
    $criteria->setSort('com_created');
    $criteria->setOrder('DESC');

    // Check modules permissions
    global $xoopsUser;
    $modulepermHandler = xoops_getHandler('groupperm');
    $gperm_groupid     = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];
    $criteria1         = new CriteriaCompo(new Criteria('gperm_name', 'module_read', '='));
    $criteria1->add(new Criteria('gperm_groupid', '(' . implode(',', $gperm_groupid) . ')', 'IN'));
    $perms  = $modulepermHandler->getObjects($criteria1, true);
    $modIds = [];
    foreach ($perms as $item) {
        $modIds[] = $item->getVar('gperm_itemid');
    }
    if (count($modIds) > 0) {
        $modIds = array_unique($modIds);
        $criteria->add(new Criteria('com_modid', '(' . implode(',', $modIds) . ')', 'IN'));
    }
    // Check modules permissions

    $comments      = $commentHandler->getObjects($criteria, true);
    $memberHandler = xoops_getHandler('member');
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler  = xoops_getHandler('module');
    $modules        = $moduleHandler->getObjects(new Criteria('hascomments', 1), true);
    $comment_config = [];
    foreach (array_keys($comments) as $i) {
        $mid           = $comments[$i]->getVar('com_modid');
        $com['module'] = '<a href="' . XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname') . '/">' . $modules[$mid]->getVar('name') . '</a>';
        if (!isset($comment_config[$mid])) {
            $comment_config[$mid] = $modules[$mid]->getInfo('comments');
        }
        $com['id']    = $i;
        $com['title'] = '<a href="'
                        . XOOPS_URL
                        . '/modules/'
                        . $modules[$mid]->getVar('dirname')
                        . '/'
                        . $comment_config[$mid]['pageName']
                        . '?'
                        . $comment_config[$mid]['itemName']
                        . '='
                        . $comments[$i]->getVar('com_itemid')
                        . '&amp;com_id='
                        . $i
                        . '&amp;com_rootid='
                        . $comments[$i]->getVar('com_rootid')
                        . '&amp;'
                        . htmlspecialchars($comments[$i]->getVar('com_exparams'))
                        . '#comment'
                        . $i
                        . '">'
                        . $comments[$i]->getVar('com_title')
                        . '</a>';
        $com['icon']  = htmlspecialchars($comments[$i]->getVar('com_icon'), ENT_QUOTES);
        $com['icon']  = ('' != $com['icon']) ? $com['icon'] : 'icon1.gif';
        $com['time']  = formatTimestamp($comments[$i]->getVar('com_created'), 'm');
        if ($comments[$i]->getVar('com_uid') > 0) {
            $poster = $memberHandler->getUser($comments[$i]->getVar('com_uid'));
            if (is_object($poster)) {
                $com['poster'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $comments[$i]->getVar('com_uid') . '">' . $poster->getVar('uname') . '</a>';
            } else {
                $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
            }
        } else {
            $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
        }
        $block['comments'][] =& $com;
        unset($com);
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_mycomments_edit($options)
{
    $inputtag = "<input type='text' name='options[]' value='" . (int)$options[0] . "'>";
    $form     = sprintf(_MB_MYCOM_DISPLAYC, $inputtag);

    return $form;
}

/**
 * @param $options
 * @return array
 */
function b_mycomments2_show($options)
{
    global $xoopsUser;
    require_once XOOPS_ROOT_PATH . '/modules/mycomments/include/comment_constants.php';
    $limit = 10; // If you  are not getting suficient results, please increase a little more this number
    $block = $comment_config = $trackedItems = [];

    $commentHandler    = xoops_getModuleHandler('comment', 'mycomments');
    $modulepermHandler = xoops_getHandler('groupperm');
    $memberHandler     = xoops_getHandler('member');
    /** @var XoopsModuleHandler $moduleHandler */
    $moduleHandler = xoops_getHandler('module');

    $criteria = new CriteriaCompo(new Criteria('com_status', MYCOM_ACTIVE));
    $criteria->setLimit((int)($options[0] * $limit));
    $criteria->setSort('com_created');
    $criteria->setOrder('DESC');

    $comments = $commentHandler->getObjects($criteria, true);
    $modules  = $moduleHandler->getObjects(new Criteria('hascomments', 1), true);

    $count = 0;
    foreach (array_keys($comments) as $i) {
        if ($count == $options[0]) {
            continue;
        }
        $mid = $comments[$i]->getVar('com_modid');

        if ($xoopsUser) {
            if (!$modulepermHandler->checkRight('module_read', $mid, $xoopsUser->getGroups())) {
                continue;
            }
        } else {
            if (!$modulepermHandler->checkRight('module_read', $mid, XOOPS_GROUP_ANONYMOUS)) {
                continue;
            }
        }

        $com['module'] = '<a href="' . XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname') . '/">' . $modules[$mid]->getVar('name') . '</a>';
        if (!isset($comment_config[$mid])) {
            $comment_config[$mid] = $modules[$mid]->getInfo('comments');
        }
        $com['id']    = $i;
        $com['title'] = '<a href="'
                        . XOOPS_URL
                        . '/modules/'
                        . $modules[$mid]->getVar('dirname')
                        . '/'
                        . $comment_config[$mid]['pageName']
                        . '?'
                        . $comment_config[$mid]['itemName']
                        . '='
                        . $comments[$i]->getVar('com_itemid')
                        . '&com_id='
                        . $i
                        . '&com_rootid='
                        . $comments[$i]->getVar('com_rootid')
                        . '&'
                        . htmlspecialchars($comments[$i]->getVar('com_exparams'))
                        . '#comment'
                        . $i
                        . '">'
                        . $comments[$i]->getVar('com_title')
                        . '</a>';
        $com['icon']  = htmlspecialchars($comments[$i]->getVar('com_icon'), ENT_QUOTES);
        $com['icon']  = ('' != $com['icon']) ? $com['icon'] : 'icon1.gif';
        $com['time']  = formatTimestamp($comments[$i]->getVar('com_created'), 'm');
        if ($comments[$i]->getVar('com_uid') > 0) {
            $poster = $memberHandler->getUser($comments[$i]->getVar('com_uid'));
            if (is_object($poster)) {
                $com['poster'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $comments[$i]->getVar('com_uid') . '">' . $poster->getVar('uname') . '</a>';
            } else {
                $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
            }
        } else {
            $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
        }
        if (count($trackedItems) > 0) {
            $itemMatch = false;
            foreach (array_keys($trackedItems) as $j) {
                if ($comments[$i]->getVar('com_modid') == $trackedItems[$j]['modid']
                    && $comments[$i]->getVar('com_itemid') == $trackedItems[$j]['itemid']) {
                    $itemMatch = true;
                }
            }
            if (!$itemMatch) {
                $block['comments'][] =& $com;
                $trackedItems[]      = [
                    'modid'  => $comments[$i]->getVar('com_modid'),
                    'itemid' => $comments[$i]->getVar('com_itemid')
                ];
                ++$count;
            }
        } else {
            $block['comments'][] =& $com;
            $trackedItems[]      = [
                'modid'  => $comments[$i]->getVar('com_modid'),
                'itemid' => $comments[$i]->getVar('com_itemid')
            ];
            ++$count;
        }
        unset($com);
    }

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_mycomments2_edit($options)
{
    $inputtag = "<input type='text' name='options[]' value='" . (int)$options[0] . "'>";
    $form     = sprintf(_MB_MYCOM_DISPLAYC, $inputtag);

    return $form;
}
