jQuery(document).ready(function($) {
    console.log(SolicitudesAjax);

    $("#btnuevo").click(function(){ // Cuando haga click me muestra el modal

        $("#modalnuevo").modal("show");
        console.log("Estoy aqui");
    });
    

    var i = 1;

    $("#add").click(function(){ // Cuando hago click en el boton de agregar añado un nuevo campo
        console.log("le he dado");
        i++;
        $("#camposdinamicos").append('<tr id="row'+i+'"><td><label for="txtnombre" class="col-form-label" style="margin-right:5px">Pregunta '+i+'</label></td><td><input type="text" name="name[]" id="name" class="form-control name_list"></td><td><select name="type[]" id="type" class="form-control type_list" style="margin-left:5px"><option value="1" selected>SI - NO</option><option value="2">Rango 0 - 5</option></select></td><td><button name="remove" id="'+i+'" class="btn btn-danger btn_remove" style="margin-left:5px">Borrar</button></td></tr>')
        return false;
    });
    
    $(document).on('click','.btn_remove',function(){ //Accion para eliminar los campos que quiero 
        var button_id = $(this).attr('id');
        $('#row'+ button_id +"").remove();

    });

    $(document).on('click',"a[data-id]",function (){
        var id = this.dataset.id;
        console.log(id);
        var url = SolicitudesAjax.url;

        $.ajax({
            type: "POST",
            url: url,
            data:{
                action : "peticionEliminar",
                nonce : SolicitudesAjax.seguridad,
                id : id,

            },
            success:function(){
                $('body').prepend('<h1 style="color:red">Se ha borrado la encuesta</h1>');
                location.reload();
            }

        });
    });

    // $(document).on('click',"a[data-ver]",function(){
    //     $("#modalestadisticas").modal("show");
    // })
    $("#btestadisticas").click(function(){ // Cuando haga click me muestra el modal

        $("#modalestadisticas").modal("show");
        console.log("Estoy aqui");
    });

});