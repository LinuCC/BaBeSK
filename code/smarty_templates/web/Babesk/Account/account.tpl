{extends file=$inh_path}{block name=content}

<h3>Konto sperren</h3> 
<form action="index.php?section=Babesk|Account" method="post">
    <fieldset>
      <select name="kontoSperren">
      <option value="lockAccount">Konto sperren</option>
      <option value="dontLockAccount" selected>Konto NICHT sperren</option>
      <input type="submit" value="Best&auml;tigen" />
      </select>
    </fieldset>
</form>                                                                          
{/block}