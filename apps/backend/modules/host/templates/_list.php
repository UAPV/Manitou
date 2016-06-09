<script type="text/javascript">
  $(document).ready(function(){
    $('#submit').click(function (e) {
      // On empêche le navigateur de soumettre le formulaire
      e.preventDefault();

      var data = new FormData();
      jQuery.each($('#file')[0].files, function(i, file) {
        data.append('file', file);
      });

      $.loader({
        className:"blue-with-image",
        content:'<p style="margin-top: -20px;color: #4C4741;"><b>Traitement en cours ...</b></p>'
      });

      $.ajax({
        url: '<?php echo url_for('@csv_import'); ?>',
        type: "POST",             // Type of request to be send, called as method
        data: data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
        contentType: false,       // The content type used when sending data to the server.
        cache: false,             // To unable request pages to be cached
        processData:false,        // To send DOMDocument or non processed data file it is set to false
        success: function(data)   // A function to be called if request succeeds
        {
          $.loader('close');
          data = jQuery.parseJSON(data);
          alert(data.message)

          /*if(!data.erreur)
          {
            url = '<?php //echo url_for("dns/reload") ?>';
            $(location).attr('href', url);
          }*/
        }
      });
    });
  })
</script>

<div>
    Nombre maximum de machines :
    <?php foreach (sfConfig::get('app_const_max_per_page') as $value): ?>
    <span class="max_per_page_selector"><?php echo link_to($value, 'host/setMaxPerPage?max='.$value) ?></span>
    <?php endforeach; ?>
</div>

<div class="sf_admin_list">
  <?php if (!$pager->getNbResults()): ?>
    <p><?php echo __('No result', array(), 'sf_admin') ?></p>
  <?php else: ?>
    <table cellspacing="0">
      <thead>
        <tr>
          <th id="sf_admin_list_batch_actions"><input id="sf_admin_list_batch_checkbox" type="checkbox" onclick="checkAll();" /></th>
          <?php include_partial('host/list_th_tabular', array('sort' => $sort)) ?>
          <th id="sf_admin_list_th_actions"><?php echo __('Actions', array(), 'sf_admin') ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="7">
            <?php if ($pager->haveToPaginate()): ?>
              <?php include_partial('host/pagination', array('pager' => $pager)) ?>
            <?php endif; ?>

            <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults(), 'sf_admin') ?>
            <?php if ($pager->haveToPaginate()): ?>
              <?php echo __('(page %%page%%/%%nb_pages%%)', array('%%page%%' => $pager->getPage(), '%%nb_pages%%' => $pager->getLastPage()), 'sf_admin') ?>
            <?php endif; ?>
          </th>
        </tr>
      </tfoot>
      <tbody>
        <?php foreach ($pager->getResults() as $i => $Host): $odd = fmod(++$i, 2) ? 'odd' : 'even' ?>
          <tr class="sf_admin_row <?php echo $odd ?>">
            <?php include_partial('host/list_td_batch_actions', array('Host' => $Host, 'helper' => $helper)) ?>
            <?php include_partial('host/list_td_tabular', array('Host' => $Host)) ?>
            <?php include_partial('host/list_td_actions', array('Host' => $Host, 'helper' => $helper)) ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script type="text/javascript">
/* <![CDATA[ */
function checkAll()
{
  var boxes = document.getElementsByTagName('input'); for(var index = 0; index < boxes.length; index++) { box = boxes[index]; if (box.type == 'checkbox' && box.className == 'sf_admin_batch_checkbox') box.checked = document.getElementById('sf_admin_list_batch_checkbox').checked } return true;
}
/* ]]> */
</script>
