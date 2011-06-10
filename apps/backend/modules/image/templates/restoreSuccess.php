<?php include_partial('image/assets') ?>

<div id="sf_admin_container">
  <h1>Restauration</h1>

  <?php include_partial('image/flashes') ?>

  <div id="sf_admin_content">
    <?php use_stylesheets_for_form($form) ?>
    <?php use_javascripts_for_form($form) ?>

    <div class="sf_admin_form">
      <?php echo form_tag ('@image_restore', array ('method' => 'post')) ?>
        <?php echo $form->renderHiddenFields(false) ?>

        <?php if ($form->hasGlobalErrors()): ?>
          <?php echo $form->renderGlobalErrors() ?>
        <?php endif; ?>

        <?php foreach ($configuration->getFormFields($form, 'new') as $fieldset => $fields): ?>
          <?php include_partial('image/form_fieldset', array('form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
        <?php endforeach; ?>


      <ul class="sf_admin_actions">
        <li class="sf_admin_action_save">
          <input type="submit" value="Démarrer la restauration" />
        </li>
      </ul>
    </form>
  </div>
