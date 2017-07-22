<?php
//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com

include __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/mycomments/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/mycomments/class/commentrenderer.php';
require_once XOOPS_ROOT_PATH . '/modules/mycomments/include/comment_constants.php';

$uid = is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
if ($uid == 0) {
    redirect_header(XOOPS_URL, 2, _NOPERM);
}
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : $uid;

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$mid   = isset($_GET['mid']) ? (int)$_GET['mid'] : 0;

$d_view     = 0; //set 0 for default comments recieved or 1 for default comments sent
$view_array = array('0' => _MA_MYCOM_COM_RECIEVED, '1' => _MA_MYCOM_COM_SENT);
$view       = (isset($_GET['view'])
               && array_key_exists((int)$_GET['view'], $view_array)) ? (int)$_GET['view'] : $d_view;

$d_limit     = $xoopsModuleConfig['comnum'];
$limit_array = array('5' => 5, '10' => 10, '20' => 20, '50' => 50, '100' => 100);
$limit       = (isset($_GET['limit'])
                && array_key_exists((int)$_GET['limit'], $limit_array)) ? (int)$_GET['limit'] : $d_limit;

if ($uid == 0) {
    redirect_header(XOOPS_URL, 2, _NOPERM);
}

$myts                                    = MyTextSanitizer::getInstance();
$GLOBALS['xoopsOption']['template_main'] = 'mycomments_comments.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$thisuser = new XoopsUser($uid);

switch ($xoopsModuleConfig['displayname']) {
    case 1:        // Username
        $username = $thisuser->getVar('uname');
        break;

    case 2:        // Display full name (if it is not empty)
        if (xoops_trim($thisuser->getVar('name')) == '') {
            $username = $thisuser->getVar('uname');
        } else {
            $username = $thisuser->getVar('name');
        }
        break;
}

// admins can view all comments and IPs, others can only view approved(active) comments
if (is_object($xoopsUser) && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    $admin_view = true;
} else {
    $admin_view = false;
}

/** @var XoopsModuleHandler $moduleHandler */
$moduleHandler   = xoops_getHandler('module');
$commentsHandler = xoops_getModuleHandler('comment');

$criteria = new CriteriaCompo(new Criteria('hascomments', 1));
if ($mid > 0) {
    $criteria->add(new Criteria('mid', $mid), 'AND');
}
$modules = $moduleHandler->getObjects($criteria, true);
unset($criteria);

$criteria  = new CriteriaCompo();
$criteria2 = new CriteriaCompo();
foreach ($modules as $moduleid => $module) {
    //for comments recieved
    if ($view == 0) {
        $items = array();
        $items = mycomments_plugin_execute($module->getVar('dirname'), $uid, 'useritems');
        if (is_array($items) && count($items) > 0) {
            $items    = '(' . implode(',', $items) . ')';
            $dirname  = $module->getVar('dirname');
            $$dirname = new CriteriaCompo(new Criteria('com_modid', $moduleid));
            $$dirname->add(new Criteria('com_itemid', $items, 'IN'), 'AND');
            $criteria->add($$dirname, 'OR');
        } else {
            //ugly fix, sorry
            $criteria->add(new CriteriaCompo(new Criteria('1', 2)), 'OR');
        }
        unset($items);
        //for comments sent
    } else {
        $items = array();
        $items = mycomments_get_plugin_info($module->getVar('dirname'), 'useritems');
        if (is_array($items) && count($items) > 0) {
            $dirname  = $module->getVar('dirname');
            $$dirname = new Criteria('com_modid', $moduleid);
            $criteria2->add($$dirname, 'OR');
        } else {
            //ugly fix, sorry
            $criteria2->add(new CriteriaCompo(new Criteria('1', 2)), 'OR');
        }
        unset($items);
    }
}
if ($view == 1) {
    $criteria->add($criteria2);
    $criteria->add(new Criteria('com_uid', $uid));
}
$criteria->setSort('com_id');
$criteria->setOrder('DESC');
$criteria->setLimit($limit);
$criteria->setStart($start);

$comments  = $commentsHandler->getObjects($criteria);
$com_count = $commentsHandler->getCount($criteria);
$renderer  = MycommentsCommentRenderer::getInstance($xoopsTpl);
$renderer->setComments($comments);
$renderer->renderFlatView($admin_view);
unset($criteria);

$gpermHandler = xoops_getHandler('groupperm');
$groups       = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$xoopsTpl->assign('xoops_iscommentadmin', $gpermHandler->checkRight('system_admin', 14, $groups));

$count_string = ($com_count != 1) ? _MA_MYCOM_NCOMMENTS : _MA_MYCOM_NCOMMENT;
$xoopsTpl->assign('com_count', sprintf($count_string, $com_count));

$com_order = $com_mode = $link_extra = '';

// assign some lang variables
$xoopsTpl->assign(array(
                      'lang_from'    => _MA_MYCOM_FROM,
                      'lang_joined'  => _MA_MYCOM_JOINED,
                      'lang_posts'   => _MA_MYCOM_POSTS,
                      'lang_poster'  => _MA_MYCOM_POSTER,
                      'lang_thread'  => _MA_MYCOM_THREAD,
                      'lang_edit'    => _EDIT,
                      'lang_delete'  => _DELETE,
                      'lang_reply'   => _REPLY,
                      'lang_subject' => _MA_MYCOM_REPLIES,
                      'lang_posted'  => _MA_MYCOM_POSTED,
                      'lang_updated' => _MA_MYCOM_UPDATED,
                      'lang_notice'  => _MA_MYCOM_NOTICE
                  ));

//For the navbar, get all modules that have plugins

$mod_array = array();
//Lets save a query, if no $mid is set then we already have $modules correctly populated
if ($mid > 0) {
    $modules = $moduleHandler->getObjects(new Criteria('hascomments', 1), true);
}

foreach ($modules as $moduleid => $module) {
    $items = array();
    $items = mycomments_get_plugin_info($module->getVar('dirname'), 'useritems');
    if (is_array($items) && count($items) > 0) {
        $mod_array[$module->getVar('mid')] = $module->getVar('name');
    }
    unset($items);
}
//Now lets create the form fields
$sel = '';
if ('0' == $mid) {
    $sel = ' selected';
}
$mod_options = '<option value="0"' . $sel . '>' . _MA_MYCOM_ALL . '</option>';
foreach ($mod_array as $key => $value) {
    $sel = '';
    if ($key == $mid) {
        $sel = ' selected';
    }
    $mod_options .= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>';
}
$xoopsTpl->assign('mod_options', $mod_options);

$view_options = '';
foreach ($view_array as $key => $value) {
    $sel = '';
    if ($key == $view) {
        $sel = ' selected';
    }
    $view_options .= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>';
}
$xoopsTpl->assign('view_options', $view_options);

$limit_options = '';
foreach ($limit_array as $key => $value) {
    $sel = '';
    if ($key == $limit) {
        $sel = ' selected';
    }
    $limit_options .= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>';
}
$xoopsTpl->assign('limit_options', $limit_options);

if ($com_count > $limit) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $pagenav_args = 'mid=' . $mid;
    if ($d_view != $view) {
        $pagenav_args .= '&view=' . $view;
    }
    if ($d_limit != $limit) {
        $pagenav_args .= '&limit=' . $limit;
    }
    $pagenav = new XoopsPageNav($com_count, $limit, $start, 'start', $pagenav_args);
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());
} else {
    $xoopsTpl->assign('pagenav', '');
}
$xoopsTpl->assign('lang_go', _GO);

//navbar
$xoopsTpl->assign('module_name', $xoopsModule->getVar('name'));
$xoopsTpl->assign('module_dirname', $xoopsModule->getVar('dirname'));
$xoopsTpl->assign('user_name', '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $uid . '">' . $username . '</a>');
$xoopsTpl->assign('lang_home', _MA_MYCOM_HOME);

require_once XOOPS_ROOT_PATH . '/footer.php';
