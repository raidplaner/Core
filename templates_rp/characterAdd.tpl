{capture assign='contentTitle'}{lang}rp.character.{$action}{/lang}{/capture}
{if $action == 'edit'}
    {capture assign='contentDescription'}{$formObject->getTitle()}{/capture}
{/if}

{capture assign='contentHeaderNavigation'}
    {if $action == 'edit' && !$formObject->isPrimary}
        <li>
            <a href="{link controller='CharacterSetAsMain' application='rp' id=$formObject->characterID}{/link}" class="button">
                <span class="icon icon16 fa-refresh"></span> 
                <span>{lang}rp.character.setAsMain{/lang}</span>
            </a>
        </li>
    {/if}

    <li>
        <a href="{link controller='CharactersList' application='rp'}{/link}" class="button">
            <span class="icon icon16 fa-list"></span> 
            <span>{lang}rp.character.list{/lang}</span>
        </a>
    </li>
{/capture}

{include file='header'}

{@$form->getHtml()}

{include file='footer'}