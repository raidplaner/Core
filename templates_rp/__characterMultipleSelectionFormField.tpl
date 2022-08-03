<script data-relocate="true">
    require(['Language', 'WoltLabSuite/Core/Ui/ItemList/Filter', 'Daries/RP/Form/Builder/Field/Character/MultipleSelection'], (Language, UiItemListFilter, CharacterMultipleSelection) => {
        Language.addObject({
            'wcf.global.filter.button.visibility': '{jslang}wcf.global.filter.button.visibility{/jslang}',
            'wcf.global.filter.button.clear': '{jslang}wcf.global.filter.button.clear{/jslang}',
            'wcf.global.filter.error.noMatches': '{jslang}wcf.global.filter.error.noMatches{/jslang}',
            'wcf.global.filter.placeholder': '{jslang}wcf.global.filter.placeholder{/jslang}',
            'wcf.global.filter.visibility.activeOnly': '{jslang}wcf.global.filter.visibility.activeOnly{/jslang}',
            'wcf.global.filter.visibility.highlightActive': '{jslang}wcf.global.filter.visibility.highlightActive{/jslang}',
            'wcf.global.filter.visibility.showAll': '{jslang}wcf.global.filter.visibility.showAll{/jslang}'
        });

        new UiItemListFilter('{@$field->getPrefixedId()}_list');
        new CharacterMultipleSelection('{@$field->getPrefixedId()}_list')
    });
</script>

<ul class="scrollableCheckboxList" id="{@$field->getPrefixedId()}_list">
	{foreach from=$field->getNestedOptions() item=__fieldNestedOption}
		<li{if $__fieldNestedOption[depth] > 0} style="padding-left: {$__fieldNestedOption[depth]*20}px"{/if}>
			<label>
				<input {*
					*}type="checkbox" {*
					*}name="{@$field->getPrefixedId()}[]" {*
					*}value="{$__fieldNestedOption[value]}"{*
					*}{if !$field->getFieldClasses()|empty} class="{implode from=$field->getFieldClasses() item='class' glue=' '}{$class}{/implode}"{/if}{*
					*}{if $field->getValue() !== null && $__fieldNestedOption[value]|in_array:$field->getValue() && $__fieldNestedOption[isSelectable]} checked{/if}{*
					*}{if $field->isImmutable() || !$__fieldNestedOption[isSelectable]} disabled{/if}{*
					*}{foreach from=$field->getFieldAttributes() key='attributeName' item='attributeValue'} {$attributeName}="{$attributeValue}"{/foreach}{*
					*}{if $__fieldNestedOption[userID]} data-user-id="{@$__fieldNestedOption[userID]}"{/if}{*
				*}>
				{@$__fieldNestedOption[label]}
			</label>
		</li>
	{/foreach}
</ul>
