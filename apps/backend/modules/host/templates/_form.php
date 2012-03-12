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

        // Quand le focus est enlevé de l'input adresse ip, on vérifie qu'elle corresponde avec la syntaxe d'une adresse ip avant d'envoyer la requete ajax
        $('#host_ip_address').focusout(function(){
            var ip = $("#host_ip_address").val();
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
        })

    //on regarde quand il fait save si l'hote existe deja et on lui demande validation apres un confirm
     $('#testDns').click(function(){
        var profile = $("#host_profile_id").val();
        var room = $("#host_room_id").val();
        var suffixe = $("#host_number").val();
        var url = '<?php echo url_for("@inDnsHostname") ?>';
        $.ajax({
            url:    url,
            data:    { profile: profile, room: room, suffixe: suffixe },
            success: function(data){
                if(data.have)
                {
                    alert("Attention, cet hostname est deja présent dans le DNS et va etre effacé si vous sauvegardez !");
                }
            }
        })
         return false;
    })
  });
</script>