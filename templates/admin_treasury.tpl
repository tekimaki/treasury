{* $Header: /cvsroot/bitweaver/_bit_treasury/templates/admin_treasury.tpl,v 1.2 2006/08/30 16:23:21 squareing Exp $ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />

	{legend legend="Treasury Settings"}
		{foreach from=$treasurySettings key=feature item=output}
			<div class="row">
				{formlabel label=`$output.label` for=$feature}
				{forminput}
					{if $output.type == 'checkbox'}
						{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
					{else}
						<input type='text' name="{$feature}" id="{$feature}" size="{if $output.type == 'text'}40{else}5{/if}" value="{$gBitSystem->getConfig($feature)|escape}" /> {$output.unit}
					{/if}
					{formhelp note=`$output.note` page=`$output.page`}
				{/forminput}
			</div>
		{/foreach}
	{/legend}

	{legend legend="Gallery Listing"}
		{foreach from=$galleryListing key=feature item=output}
			<div class="row">
				{formlabel label=`$output.label` for=$feature}
				{forminput}
					{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
					{formhelp note=`$output.note` page=`$output.page`}
				{/forminput}
			</div>
		{/foreach}
	{/legend}

	{legend legend="Item Listing"}
		<div class="row">
			{formlabel label="Item List Thumbnail Size"}
			{forminput}
				{html_options values=$imageSizes options=$imageSizes name="treasury_item_list_thumb" selected=$gBitSystem->getConfig('treasury_item_list_thumb')}
			{/forminput}
		</div>

		{foreach from=$itemListing key=feature item=output}
			<div class="row">
				{formlabel label=`$output.label` for=$feature}
				{forminput}
					{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
					{formhelp note=`$output.note` page=`$output.page`}
				{/forminput}
			</div>
		{/foreach}
	{/legend}

	{legend legend="Item Viewing"}
		<div class="row">
			{formlabel label="Item List Thumbnail Size"}
			{forminput}
				{html_options values=$imageSizes options=$imageSizes name="treasury_item_view_thumb" selected=$gBitSystem->getConfig('treasury_item_view_thumb')}
			{/forminput}
		</div>

		{foreach from=$itemViewing key=feature item=output}
			<div class="row">
				{formlabel label=`$output.label` for=$feature}
				{forminput}
					{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
					{formhelp note=`$output.note` page=`$output.page`}
				{/forminput}
			</div>
		{/foreach}
	{/legend}

	<div class="row submit">
		<input type="submit" name="treasury_settings" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
