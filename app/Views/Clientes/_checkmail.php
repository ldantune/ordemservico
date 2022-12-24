$('[name=email]').on('change', function() {
      var email = $(this).val();

      if (email != '') {
        $.ajax({
          type: 'GET',
          url: '<?php echo site_url('clientes/consultaemail'); ?>',
          data: {
            email: email
          },
          dataType: 'json',
          beforeSend: function() {

            $("#form").LoadingOverlay("show");
            $("#email").html('');

          },
          success: function(response) {
            $("#form").LoadingOverlay("hide", true);
            
            if (!response.erro) {
              $("#email").html('');
            }

            if (response.erro) {
              $("#email").html(response.erro);
              
            }
          },
          error: function() {
            alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
            $("#form").LoadingOverlay("hide", true);
          }
        });
      }
    });