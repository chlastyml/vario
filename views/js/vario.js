$(document).ready(function(){
    console.log("#get_params start");
    $.ajax({
        url: '../modules/vario/ajax/get_params.php',
        data: {
            ajax        : true,
            action      : 'get_params',
            token       : new Date().getTime()
        },
        method:'GET',
        success:function(data){
            if(data == null || data == '')
                return;

            var json = JSON.parse(data);
            document.getElementById('vario_wsdl').value = json.wsdl_url;
            resetElements('#get_params');
            console.log("#get_params complete");
        }
    });

    $('#update_vario_wsdl').click(function(){
        console.log('start: #update_vario_wsdl');
        $.ajax({
            url:"../modules/vario/ajax/set_params.php",
            data:{
                ajax        : true,
                action      : 'set_params',
                token       : new Date().getTime(),
                wsdl_url    : $('#vario_wsdl').val()
            },
            method:'POST',
            success:function(data){
                document.getElementById('tag-id').innerHTML = data;
                resetElements('#update_vario_wsdl');
                console.log("#update_vario_wsdl success");
            }
        });
    });

    $('#test_vario').click(function(){
        console.log('start: #test_vario');
        $.ajax({
            url:"../modules/vario/ajax/test_vario.php",
            data:{
                ajax        : true,
                action      : 'test_vario',
                token       : new Date().getTime(),
                wsdl_url    : $('#vario_wsdl').val()
            },
            method:'POST',
            success:function(data){
                // Uspech
                document.getElementById('test_vario_icon').className = 'icon-usd';
                console.log("#test_vario success");
                resetElements('#test_vario');
                console.log("#test_vario complete");
            },
            error:function (data) {
                document.getElementById('test_vario_icon').className = 'icon-gear';
                document.getElementById('tag-id').innerHTML = data;

                resetElements('#test_vario');

                console.log("#update_vario_wsdl error " + data );
            }
        });
    });

    $('#import_vario').click(function(){
        console.log('start: #import_vario');
        $.ajax({
            url:"../modules/vario/ajax/import_product.php",
            data:{
                ajax        : true,
                action      : 'import_vario',
                token       : new Date().getTime(),
                wsdl_url    : $('#vario_wsdl').val()
            },
            method:'POST',
            success:function(data){
                document.getElementById('tag-id').innerHTML = data;
                console.log("#import_vario success. " + data);

                resetElements('#import_vario');
            }
        });
    });

    $('#export_order').click(function(){
        console.log('start: #export_order');
        $.ajax({
            url:"../modules/vario/ajax/export_order.php",
            data:{
                ajax        : true,
                action      : 'export_order',
                token       : new Date().getTime()
            },
            method:'POST',
            success:function(data){
                document.getElementById('tag-id').innerHTML = data;
                console.log("#export_order success. " + data);

                resetElements('#export_order');
            }
        });
    });

    var default_icon = document.getElementById('test_vario_icon').className;
    function resetElements(functionName) {
        setTimeout(function(){
            document.getElementById('test_vario_icon').className = default_icon;
            document.getElementById('tag-id').innerHTML = '';
            console.log(functionName + " take back");
        }, 3000);
    }
});