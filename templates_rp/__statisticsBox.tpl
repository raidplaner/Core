{if $rpStatistics|isset && $__wcf->session->getPermission('user.rp.canReadEvent')}
	<dt>{lang}rp.character.characters{/lang}</dt>
	<dd>{#$rpStatistics[characters]}</dd>
	<dt>{lang}rp.event.events{/lang}</dt>
	<dd>{#$rpStatistics[events]}</dd>
	<dt>{lang}rp.raid.raids{/lang}</dt>
	<dd>{#$rpStatistics[raids]}</dd>
{/if}