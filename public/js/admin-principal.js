toastr.options.timeOut = 0;
toastr.options.extendedTimeOut = 0;
toastr.options.positionClass = "toast-bottom-right";

$(function() {
    pedidos()
    setInterval(() => {
        toastr.remove()
        pedidos()
    }, 60000);
});

function pedidos(){
    $.ajax({
        url: "/admin/comandas/novo-pedido",
        method: 'post',
        success: function(response){
            if(response > 0){
                toastr.warning("Tem "+response+" novo(s) pedido(s)", "Novo Pedido")
            }
        }
    });
}

$('.alert').addClass("show");
$('.alert').removeClass("hide");
$('.alert').addClass("showAlert");
setTimeout(function(){
    $('.alert').removeClass("show");
    $('.alert').addClass("hide");
},3500)