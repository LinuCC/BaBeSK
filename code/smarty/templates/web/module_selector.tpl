
<div id='headmod_selection' class="clearfix">

	<p>Willkommen! Bitte w&auml;hle ein Modul:</p>

	{foreach $modules as $module}
		{if $module->isDisplayInMenuAllowed()}
			<a href="index.php?section={$module->getName()}">
				<div class='headmodule' id='headmod_{$counter}'>
					{$path = ModuleGenerator::modulePathGet($module, $moduleroot)}
					{_g("modulepath_$path")}
				</div>
			</a>
		{/if}
	{/foreach}
</div>
