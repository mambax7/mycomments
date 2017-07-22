<{include file="db:mycomments_navigation.tpl"}>

<table class="outer" cellpadding="5" cellspacing="1">
    <tr>
        <td colspan="2"><{$pagenav}>
        <td>
    </tr>
    <{foreach item=comment from=$comments}>
        <!-- start comment post -->
        <tr>
            <td class="itemHead" colspan="2"><span class="itemTitle"><a
                            href="<{$comment.module_link}>"><{$comment.module_name}></a>&nbsp;-&nbsp;<a
                            href="<{$comment.item_link}>"><{$comment.item_title}></a></span></td>
        </tr>
        <tr>
            <td class="head" width="20%"><a id="comment<{$comment.id}>"></a> <{$comment.poster.uname}></td>
            <td class="head">
                <div class="comDate"><span class="comDateCaption"><{$lang_posted}>:</span> <{$comment.date_posted}>
                    &nbsp;&nbsp;<span class="comDateCaption"><{$lang_updated}>
                        :</span> <{$comment.date_modified}></div>
            </td>
        </tr>
        <tr>

            <{if $comment.poster.id != 0}>
                <td class="odd">
                    <div class="comUserRank">
                        <div class="comUserRankText"><{$comment.poster.rank_title}></div>
                        <img class="comUserRankImg" src="<{$xoops_upload_url}>/<{$comment.poster.rank_image}>" alt="">
                    </div>
                    <img class="comUserImg" src="<{$xoops_upload_url}>/<{$comment.poster.avatar}>" alt="">
                    <div class="comUserStat"><span class="comUserStatCaption"><{$lang_joined}>
                            :</span> <{$comment.poster.regdate}></div>
                    <div class="comUserStat"><span class="comUserStatCaption"><{$lang_from}>
                            :</span> <{$comment.poster.from}></div>
                    <div class="comUserStat"><span class="comUserStatCaption"><{$lang_posts}>
                            :</span> <{$comment.poster.postnum}></div>
                    <div class="comUserStatus"><{$comment.poster.status}></div>
                </td>
            <{else}>
                <td class="odd"></td>
            <{/if}>

            <td class="odd">
                <div class="comTitle"><{$comment.image}><{$comment.title}></div>
                <div class="comText"><{$comment.text}></div>
            </td>
        </tr>
        <tr>
            <td class="even"></td>

            <{if $xoops_iscommentadmin == true}>
                <td class="even" align="right">
                    <a href="<{$comment.editcomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/edit.gif" alt="<{$lang_edit}>"></a><a
                            href="<{$comment.deletecomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/delete.gif" alt="<{$lang_delete}>"></a><a
                            href="<{$comment.replycomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/reply.gif" alt="<{$lang_reply}>"></a>
                </td>
            <{elseif $xoops_isuser == true && $xoops_userid == $comment.poster.id}>
                <td class="even" align="right">
                    <a href="<{$comment.editcomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/edit.gif" alt="<{$lang_edit}>"></a><a
                            href="<{$comment.replycomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/reply.gif" alt="<{$lang_reply}>"></a>
                </td>
            <{elseif $xoops_isuser == true || $anon_canpost == true}>
                <td class="even" align="right">
                    <a href="<{$comment.replycomment_link}>&amp;com_id=<{$comment.id}>"><img
                                src="<{$xoops_url}>/images/icons/reply.gif" alt="<{$lang_reply}>"></a>
                </td>
            <{else}>
                <td class="even"></td>
            <{/if}>

        </tr>
        <!-- end comment post -->
    <{/foreach}>
    <tr>
        <td colspan="2"><{$pagenav}>
        <td>
    </tr>
</table>