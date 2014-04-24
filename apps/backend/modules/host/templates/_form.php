<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<div class="sf_admin_form">
  <?php echo form_tag_for($form, '@host') ?>
    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
      <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <?php foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit') as $fieldset => $fields): ?>
      <?php include_partial('host/form_fieldset', array('Host' => $Host, 'form' => $form, 'fields' => $fields, 'fieldset' => $fieldset)) ?>
    <?php endforeach; ?>

    <?php include_partial('host/form_actions', array('Host' => $Host, 'form' => $form, 'configuration' => $configuration, 'helper' => $helper)) ?>
    </form>
</div>

<script type="text/javascript">
    $(document).ready (function () {

        $("#host_ip_address").css("border","1 px solid #ccc");
        $("#host_number").css("border","1 px solid #ccc");
        $("#alert").remove();

        // Quand le focus est enlevé de l'input adresse ip, on vérifie qu'elle corresponde avec la syntaxe d'une adresse ip avant d'envoyer la requete ajax
        $('#host_ip_address').focusout(function(){
            $("#alert").remove();
            var ip = $("#host_ip_address").val();
            var profile = $("#host_profile_id").val();
            var room = $("#host_room_id").val();
            var suffixe = $("#host_number").val();
            $("#alert").html('');
            $("#host_ip_address").css("border","1 px solid #ccc");
            $.ajax({
                url:     '<?php echo url_for("@inDnsCheck") ?>',
                data:    { ip: ip },
                success: function(data){
                   if(data.have)
                   {
                     $(".sf_admin_form_field_ip_address .help").prepend('<div id="alert">Attention, cette adresse ip appartient déjà au DNS pour l\'hote '+data.host+' , elle sera donc écrasée par votre nouvel enregistrement !</div>')
                     $("#host_ip_address").css("border","1px solid red");
                   }
                }
            })

            if(suffixe == "")
            {
              tableau = ip.split('.');
              suffixe = tableau[3];
              $("#host_number").val(suffixe);
            }
            checkDns(profile, room, suffixe, false);
        })

    //on regarde quand il fait save si l'hote existe deja et on lui demande validation apres un confirm
     $('#testDns').change(function(){
        var profile = $("#host_profile_id").val();
        var room = $("#host_room_id").val();
        var suffixe = $("#host_number").val();
        checkDns(profile, room, suffixe, true);
         return false;
    })

    //la case "pas de profil" à été cochée
    $('#noProfile').click(function(){
        if( $(this).is(':checked') )
        {
           // $('#host_profile_id').append('<option value="no" selected="selected"></option>');
            return false;
        }
    })
  });

  function checkDns(profile, room, suffixe, test)
  {
      var url = '<?php echo url_for("@inDnsHostname") ?>';
      $.ajax({
          url:    url,
          data:    { profile: profile, room: room, suffixe: suffixe },
          success: function(data){
              if(data.have)
              {
                  if(test)
                    alert("Attention, cet hostname est deja présent dans le DNS et va etre effacé si vous sauvegardez !");
                  else
                  {
                    $(".sf_admin_form_field_number").append('<div id="alert">Attention, cet hostname est deja présent dans le DNS et va etre effacé si vous sauvegardez</div>')
                    $("#host_number").css("border","1px solid red");
                  }
              }
              else
              {
                if(test)
                  alert("Le nom est disponible");
              }
          }
      })
      return false;
  }

</script>