jQuery(document).ready(function($){
    //Automação por CNPJ
    $('#vat').on('input', function(){
        var cnpj = $(this).val().replace(/[^0-9]/g, '');

        if(cnpj.length < 14){
            return false;
        }

        $.ajax({
            url: 'https://www.receitaws.com.br/v1/cnpj/'+cnpj,
            method: 'GET',
            dataType: 'jsonp',
            beforeSend: function(){
                $('#company').val('...');
                $('#address').val('...');
                $('#city').val('...');
                $('#state').val('...');
            },
            success: function(data){
                console.log(data);
                $('#company').val(data.nome);
                $('#address').val(data.logradouro);
                $('#city').val(data.municipio);
                $('#state').val(data.uf);
                $('#phonenumber').val(data.telefone);
                $('#zip').val(data.cep);
            }
        });
    });

    //Automação por CEP
    $('#zip').on('input', function(){
        var cep = $(this).val().replace(/[^0-9]/g, '');
        if(cep.length < 8){
            return false;
        }
        $.ajax({
            url: 'https://viacep.com.br/ws/'+cep+'/json/',
            method: 'GET',
            dataType: 'json',
            beforeSend: function(){
                $('#address').val('...');
                $('#city').val('...');
                $('#state').val('...');
            },
            success: function(data){
                $('#address').val(data.logradouro);
                $('#city').val(data.localidade);
                $('#state').val(data.uf);
            }
        });
    });
});