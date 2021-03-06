<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php use_stylesheet('/sfPropel15Plugin/css/default.css') ?>
    <?php use_stylesheet('/sfPropel15Plugin/css/global.css') ?>
    <?php use_stylesheet('admin.css') ?>
    <?php use_javascript('jquery-1.6.1.min.js') ?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <link  href="https://fonts.googleapis.com/css?family=Carter+One:regular" rel="stylesheet" type="text/css" >
  </head>
  <body>
    <div id="header">
      <h1>Manitou ! <span id="remi-joke">mais pas n'importe quoi !</span></h1>
      <ul>
        <li><?php echo link_to ('Logs', 'command_list') ?></li>
        <li><?php echo link_to ('Machines', 'host') ?></li>
        <?php if(sfContext::getInstance()->getUser()->hasCredential('superadmin')) { ?>
          <li><?php echo link_to ('Salles', 'room') ?></li>
          <li><?php echo link_to ('Profils', 'profile') ?></li>
        <?php } ?>

        <?php if(sfContext::getInstance()->getUser()->hasCredential('superadmin')) { ?>
          <li><?php echo link_to ('Subnets', 'subnet') ?></li>
        <?php } ?>

        <li><?php echo link_to ('Fichiers PXE', 'pxe_file') ?></li>
        <li><?php echo link_to ('Conf DHCP', 'dhcpd/index') ?></li>
        <li><?php echo link_to ('Erreurs DNS', 'dns/index') ?></li>
      </ul>
    </div>
    <div id="content">
      <?php echo $sf_content ?>
    </div>

  </body>
</html>
