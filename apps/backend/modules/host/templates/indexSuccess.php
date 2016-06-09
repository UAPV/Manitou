<?php use_helper('I18N', 'Date') ?>
<?php include_partial('host/assets') ?>

<div id="sf_admin_container">

  <h1><?php echo __('Gestion des machines', array(), 'messages') ?></h1>

  <?php include_partial('host/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('host/list_header', array('pager' => $pager)) ?>
  </div>

  <div id="sf_admin_bar">
    <?php include_partial('host/filters', array('form' => $filters, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <form action="<?php echo url_for('host_collection', array('action' => 'batch')) ?>" method="post">
    <?php include_partial('host/list', array('pager' => $pager, 'sort' => $sort, 'helper' => $helper)) ?>
    <ul class="sf_admin_actions">
      <?php include_partial('host/list_batch_actions', array('helper' => $helper)) ?>
      <?php include_partial('host/list_actions', array('helper' => $helper)) ?>
    </ul>
    </form>
  </div>

  <?php if(sfContext::getInstance()->getUser()->hasCredential('superadmin')): ?>
    <div style="color: #555;font-weight: bold; border: 1px solid #ddd; background-color: white;padding: 5px; width: 40%; height: 30px;">
      <span style="rgb(0,​ 0,​ 0);float: left;">Importer des machines en masse</span>
      <div style="float: right;">
        <form id="importMasse" method="post">
          <input  type="file" value="Import en masse" id="file"/>
          <input type="button" value="Envoyer" id="submit" />
        </form>
      </div>
      <div style="clear: both;"></div>
    </div>
  <?php endif; ?>

  <div id="sf_admin_footer">
    <?php include_partial('host/list_footer', array('pager' => $pager)) ?>
  </div>
</div>
