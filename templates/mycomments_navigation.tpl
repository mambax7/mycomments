<div style="text-align: center;">
    <form name="form1" action="<{$xoops_url}>/modules/mycomments/index.php" method="get">
        <select name="mid"><{$mod_options}></select>
        <select name="view"><{$view_options}></select>
        <select name="limit"><{$limit_options}></select>
        <input type="hidden" name="start" value="0">
        <input type="submit" value="<{$lang_go}>" class="formButton">
    </form>
    <hr>
</div>
<div id="breadcrumbs">
    <a href="http://www.xuups.com" title="Xuups Users">
        <img src="<{$xoops_url}>/modules/<{$module_dirname}>/assets/images/xuupslogo.png">
    </a>
    <a href="<{$xoops_url}>" title="<{$xoops_sitename}> - <{$xoops_slogan}>">
        <{$lang_home}>
    </a>
    >
    <a href="<{$xoops_url}>/modules/<{$module_dirname}>/index.php" title="<{$module_name}>">
        <{$module_name}>
    </a>
    >
    <{$user_name}>
    >
    <{$com_count}>
</div>
<div>&nbsp;</div>
