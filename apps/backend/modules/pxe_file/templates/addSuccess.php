<?php use_helper('I18N') ?>
<?php include_partial('pxe_file/assets') ?>

<div id="sf_admin_container">
  <h1>Changer les fichiers PXE</h1>

  <?php include_partial('pxe_file/flashes') ?>

  <div id="sf_admin_content">
    <?php use_stylesheets_for_form($form) ?>
    <?php use_javascripts_for_form($form) ?>

    <div class="sf_admin_form">
      <?php echo form_tag ('@add_pxe', array ('method' => 'post')) ?>
        <?php echo $form->renderHiddenFields(false) ?>

        <?php if ($form->hasGlobalErrors()): ?>
          <?php echo $form->renderGlobalErrors() ?>
        <?php endif; ?>

        <?php foreach ($configuration->getFormFields($form, 'new') as $fieldset => $fields): ?>
          <?php include_partial('pxe_file/form_fieldset', array('form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
        <?php endforeach; ?>


      <ul class="sf_admin_actions">
        <li class="sf_admin_action_save">
          <input type="submit" value="Sauvegarder" />
        </li>
      </ul>
    </form>
  </div>

  <script type="text/javascript">

    $(document).ready (function () {
      // On supprime les hôtes décochés
      $('.sf_admin_form_field_hosts input:not(:checked)').closest('li').remove();

      // Pour les autre on vérifie s'ils sont démarrés
      $('.sf_admin_form_field_hosts input').each (function (i, input) {
        $.get ('<?php echo url_for('@host') ?>/'+input.value+'/status', function (data) {
          $(input).closest ('li').append (data.status == 1 ? 'UP !' : 'DOWN');
          if (data.status == 1)
            $(input).removeAttr ('checked')
        });
      });
    });

  </script>
