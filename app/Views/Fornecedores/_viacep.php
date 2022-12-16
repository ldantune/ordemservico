$('[name=cep]').on('keyup', function() {
      var cep = $(this).val();

      if (cep.length === 9) {
        $.ajax({
          type: 'GET',
          url: '<?php echo site_url('fornecedores/consultacep'); ?>',
          data: {
            cep: cep
          },
          dataType: 'json',
          beforeSend: function() {

            $("#form").LoadingOverlay("show");
            $("#cep").html('');

          },
          success: function(response) {
            $("#form").LoadingOverlay("hide", true);
            
            if (!response.erro) {
              if(!response.endereco){
                $('[name=endereco]').prop('readonly', false);
                $('[name=endereco]').focus();
              }

              if(!response.bairro){
                $('[name=bairro]').prop('readonly', false);
              }

              $('[name=endereco]').val(response.endereco);
              $('[name=bairro]').val(response.bairro);
              $('[name=estado]').val(response.estado);
              $('[name=cidade]').val(response.cidade);

            }

            if (response.erro) {
              $("#cep").html(response.erro);
              
            }
          },
          error: function() {
            alert('Não foi possível processar a solicitação. Por favor entre em contato com suporte técnico.');
            $("#form").LoadingOverlay("hide", true);
          }
        });
      }
    });