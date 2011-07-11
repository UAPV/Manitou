
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

    <fieldset class="sf_fieldset_none">
      <?php foreach (array ('profile_id','room_id','subnet_id','first_ip_address','pxe_file_id','count') as $name): ?>
        <div class="sf_admin_form_row <?php $form[$name]->hasError() and print ' errors' ?>">
          <?php echo $form[$name]->renderError() ?>
          <div>
            <?php echo $form[$name]->renderLabel() ?>

            <div class="content"><?php echo $form[$name]->render() ?></div>

            <?php if ($help = $form[$name]->renderHelp()): ?>
              <div class="help"><?php echo $help ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach ?>
    </fieldset>

    <ul class="sf_admin_actions">
      <li><input type="submit" value="Mettre à jour les infos" /></li>
    </ul>

    <h2>Adresses des machines</h2>

    <fieldset class="sf_fieldset_none">
      <?php for ($i=0; $i<$form['count']->getValue(); $i++): ?>
        <?php if ($form->offsetExists ('host_'.$i, $form)): ?>
          <?php echo $form['host_'.$i] ?>
        <?php endif ?>
      <?php endfor ?>
    </fieldset>

    <ul class="sf_admin_actions">
      <li><input type="submit" value="Enregistrer" /></li>
    </ul>

  </form>

</div>
</div>
</div>
