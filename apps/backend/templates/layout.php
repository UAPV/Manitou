<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php use_stylesheet('admin.css') ?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <div id="header">
      <h1>DRBL Admin</h1>
      <ul>
        <li><?php echo link_to ('Machines', 'host') ?></li>
        <li><?php echo link_to ('Salles', 'room') ?></li>
        <li><?php echo link_to ('Profils', 'profile') ?></li>
        <li><?php echo link_to ('Hôtes DRBL', 'drbl_server') ?></li>
        <li><?php echo link_to ('Subnets', 'subnet') ?></li>
        <li><?php echo link_to ('Fichiers PXE', 'pxe_file') ?></li>
      </ul>
    </div>
    <div id="content">
      <?php echo $sf_content ?>
    </div>
  </body>
</html>
