
<h1>Création de machines en masse</h1>

<div id="sf_admin_container">
<div id="sf_admin_content">
<div class="sf_admin_form">

  <form method="post">
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <h2>Attributs communs</h2>

    <table>
      <?php echo $form->renderUsing('Table')?>
    </table>

    <ul class="sf_admin_actions">
      <li><input type="submit" value="Enregistrer" /></li>
    </ul>

  </form>

</div>
</div>
</div>
