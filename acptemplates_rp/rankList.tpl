{include file='header' pageTitle='rp.acp.rank.list'}

{if $objects|count}
    <script data-relocate="true">
        require(['Ajax', 'WoltLabSuite/Core/Ui/Sortable/List'], function (Ajax, UiSortableList) {
            new UiSortableList({
                containerId: 'rankList',
                className: 'rp\\data\\rank\\RankAction',
                offset: {@$startIndex},
                additionalParameters: {
                    gameID: {@RP_DEFAULT_GAME_ID}
                }
            });
        });
    </script>
{/if}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}rp.acp.rank.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link controller='RankAdd' application='rp'}{/link}" class="button">
                    {icon name='plus'} 
                    <span>{lang}rp.acp.rank.add{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller="RankList" application='rp' link="pageNo=%d"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div id="rankList" class="sortableListContainer section">
        <ol class="sortableList jsReloadPageWhenEmpty jsObjectActionContainer" data-object-action-class-name="rp\data\rank\RankAction" data-object-id="0" start="{@($pageNo - 1) * $itemsPerPage + 1}">
            {foreach from=$objects item=rank}
                <li class="sortableNode sortableNoNesting rankRow jsObjectActionObject" data-object-id="{@$rank->rankID}">
                    <span class="sortableNodeLabel">
                        <a href="{link controller='RankEdit' application='rp' id=$rank->rankID}{/link}">{$rank->getTitle()}</a> {if $rank->isDefault} ({lang}wcf.global.defaultValue{/lang}){/if}

                        <span class="statusDisplay sortableButtonContainer">
                            <span class="sortableNodeHandle">
                                {icon name='arrows'}
                            </span>
                            {if !$rank->isDefault}
                                <span 
                                    class="jsObjectAction pointer" 
                                    data-object-action="setAsDefault" 
                                    data-object-action-success="reload">
                                    {icon name='square'}
                                </span>
                            {else}
                                <span class="disabled">
                                    {icon name='check-square'}
                                </span>
                            {/if}
                            <a href="{link controller='RankEdit' application='rp' id=$rank->rankID}{/link}">
                                <span title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
                                    {icon name='pencil'}
                                </span>
                            </a>
                            {if !$rank->isDefault}
                                {objectAction action="delete" objectTitle=$rank->getTitle()}
                            {else}
                                <span class="disabled" title="{lang}wcf.global.button.delete{/lang}">
                                    {icon name='times'}
                                </span>
                            {/if}

                            {event name='itemButtons'}
                        </span>
                    </span>
                    <ol class="sortableList" data-object-id="{@$rank->rankID}"></ol>
                </li>
            {/foreach}
        </ol>
    </div>

    <div class="formSubmit">
        <button class="button buttonPrimary" data-type="submit">{lang}wcf.global.button.saveSorting{/lang}</button>
    </div>

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
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}