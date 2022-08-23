{if !$errorField|isset}{assign var=errorField value=''}{/if}

<section class="section">
	<h2 class="sectionTitle">{lang}wcf.acp.box.settings{/lang}</h2>

    <dl{if $errorField === 'serverID'} class="formError"{/if}>
        <dt><label for="serverID">{lang}rp.acp.box.settings.server{/lang}</label></dt>
        <dd>
            <select name="serverID" id="serverID">
                <option value=""{if !$serverID} selected{/if}>{lang}wcf.global.noSelection{/lang}</option>
                {foreach from=$validServers key=gameIdentifier item=serverGroups}
                    {foreach from=$serverGroups key=serverGroup item=servers}
                        <optgroup label="{lang}rp.game.{$gameIdentifier}{/lang} ({lang}rp.server.{$gameIdentifier}.group.{$serverGroup}{/lang})">
                            {foreach from=$servers item=server}
                                <option value="{@$server->serverID}"{if $serverID == $server->serverID} selected{/if}>{$server->getTitle()} ({$server->type})</option>
                            {/foreach}
                        </optgroup>
                    {/foreach}
                {/foreach}
            </select>
        </dd>
    </dl>
</section>