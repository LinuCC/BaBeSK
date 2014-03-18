{extends file=$inh_path}{block name=content}

<div class="">
	<div class="panel panel-primary bg-fit">
		<div class="panel-heading">
			<div class="panel-title">
				Kursdetails des Kurses {$class.label}
				<div class="pull-right label label-primary">
					{if $class.status} {$class.status}
					{else}<b>Fehler - ein falscher Statuseintrag! Wenden sie sich an den Administrator!</b>
					{/if}
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="panel-body">
			<h4>
				<span class="label label-default label-lg">
					{$class.classteacherName}
				</span>
			</h4>
			<div class="quotebox quoted">
				{$class.description}
			</div>
		</div>
	</div>
	{if $class.registrationEnabled}
	<a class="btn btn-default" href="index.php?module=web|Kuwasys">
		{t}back{/t}
	</a>
	<form class="pull-right" action="index.php?section=Kuwasys|ClassDetails&action=deRegisterClassConfirmation&classId={$class.ID}" method="post">
		<input class="btn btn-danger" type="submit" value="Von dem Kurs abmelden">
	</form>
	{else}
	Abmelden von dem Kurs ist nicht mehr möglich.
	{/if}
</div>



{/block}