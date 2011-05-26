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
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <link  href="http://fonts.googleapis.com/css?family=Carter+One:regular" rel="stylesheet" type="text/css" >
  </head>
  <body>
    <div id="header">
      <h1>Manitou ! <span id="remi-joke">mais pas n'importe quoi !</span></h1>
      <ul>
        <li><?php echo link_to ('Machines', 'host') ?></li>
        <li><?php echo link_to ('Salles', 'room') ?></li>
        <li><?php echo link_to ('Profils', 'profile') ?></li>
        <li><?php echo link_to ('Serveurs d\'image', 'image_server') ?></li>
        <li><?php echo link_to ('Subnets', 'subnet') ?></li>
        <li><?php echo link_to ('Fichiers PXE', 'pxe_file') ?></li>
        <li><?php echo link_to ('Conf Dhcp', 'dhcpd/index') ?></li>
      </ul>
    </div>
    <div id="content">
      <?php echo $sf_content ?>
    </div>
  </body>
</html>
