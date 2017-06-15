function setHeiHeight() {
    $('#bgimage').css({
        height: $(window).height() - 165 + 'px'
    });
}
setHeiHeight(); // устанавливаем высоту окна при первой загрузке страницы
$(window).resize( setHeiHeight ); // обновляем при изменении размеров окна

$(document).ready(function () {
    var hash = window.location.hash;
    hash && $('ul.nav-tabs a[href="' + hash + '"]').tab('show');

    $('.modal-auto-popup').modal('show');
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover({ 
        placement : 'bottom',
    });
    
    $(".dynamicform_wrapper").on("beforeInsert", function(e, item) {
    console.log("beforeInsert");
    });

    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        console.log("afterInsert");
    });

    $(".dynamicform_wrapper").on("beforeDelete", function(e, item) {
        if (! confirm("Are you sure you want to delete this item?")) {
            return false;
        }
        return true;
    });

    $(".dynamicform_wrapper").on("afterDelete", function(e) {
        console.log("Deleted item!");
    });

    $(".dynamicform_wrapper").on("limitReached", function(e, item) {
        alert("Limit reached");
    });

    $(".show-refuse-reason").click(function(){
        $(this).hide();
        $(".container-refuse-reason").show();
        $(".container-refuse-reason textarea").focus();
    });

    $(".show-search-form").click(function(){
        $(".search-form").slideToggle();
    });
});

function selectGroup(obj) {
    value = $(obj).find("option:selected").attr("data-nominal");
    $("#nominalField").val(value);
}


function selectType(value) {
        if (value == 2) { 
            $("#proxy").hide(); 
            $("#svidet").hide(); 
        }
        if (value == 1) { 
            $("#proxy").show(); 
            $("#svidet").hide();
        }
        if (value == 3) { 
            $("#proxy").hide();
            $("#svidet").show();
        }
}

function selectTypes(value) {
        if (value == 4) { 
            $("#svid").show();
            $("#proxy").hide();
            
        }
        if (value == 3) { 
            $("#svid").show();
            $("#proxy").show();
        }
        if (value == 1 || value == 2) { 
            $("#svid").hide();
            $("#proxy").show();
            
        }
}

function selectOvz(value) {
        if (value == 1) { $("#zab").hide(); }
        if (value == 2) { $("#zab").show(); }
}