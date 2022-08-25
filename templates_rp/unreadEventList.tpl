{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='UnreadEventList' application='rp'}pageNo={@$pageNo+1}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='UnreadEventList' application='rp'}{if $pageNo > 2}pageNo={@$pageNo-1}{/if}{/link}">
	{/if}
{/capture}

{capture assign='contentTitleBadge'}<span class="badge">{#$items}</span>{/capture}

{capture assign='contentInteractionPagination'}
	{pages print=true assign=pagesLinks controller='UnreadEventList' application='rp' link="pageNo=%d"}
{/capture}

{capture assign='contentInteractionButtons'}
    <a href="#" class="markAllAsReadButton contentInteractionButton button small jsOnly"><span class="icon icon16 fa-check"></span> <span>{lang}wcf.global.button.markAllAsRead{/lang}</span></a>
{/capture}

{include file='header'}

{if $items}
	{include file='eventList' application='rp'}
{else}
	<p class="info" role="status">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}
					{event name='contentFooterNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

<script data-relocate="true">
    require(['Daries/RP/Ui/Event/MarkAllAsRead'], function(UiEventMarkAllAsRead) {
        UiEventMarkAllAsRead.init();
    });
</script>

{include file='footer'}
