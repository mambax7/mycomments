<table width="100%" cellspacing="1" class="outer">
    <{foreach item=comment from=$block.comments}>
        <tr class="<{cycle values="even,odd"}>">
            <td align="center"><img src="<{$xoops_url}>/images/subject/<{$comment.icon}>" alt=""></td>
            <td><{$comment.title}></td>
            <td align="center"><{$comment.module}></td>
            <td align="center"><{$comment.poster}></td>
            <td align="right"><{$comment.time}></td>
        </tr>
    <{/foreach}>
</table>
