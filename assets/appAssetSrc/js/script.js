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

    $(".toggle-search-settings").click(function(){
        $(".search-settings").slideToggle();
    });
    $(".show-additional-params").click(function(){
        $(".additional-params").slideToggle();
    });

    $(".toggle-password").click(function () {
        $obj = $("#loginform-password");
        if ($obj.attr('type') === 'password') {
            $obj.attr('type', 'text');
            $(this).find('span').removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
        } else {
            $obj.attr('type', 'password');
            $(this).find('span').removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
        }
    });

    var titles = $(".input-title").find("input");
    var titleBody = $(".input-title-body");

    titles.focusin(function () {
        titleBody.html($(this).parent(".input-title").data('input-title'));
        titleBody.css('display', 'block');
        titleBody.css('width', $(this).width());
        titleBody.css('top', $(this).offset().top + $(this).height() + 20);
        titleBody.css('left', $(this).offset().left);
    });

    titles.focusout(function () {
        titleBody.css('display', 'none');
    });
});

function selectGroup(obj) {
    nominal = $("#possible-cert-group").find("option:selected").attr("data-nominal");
    if (!nominal || nominal < 1) {
        krajeeDialog.alert("Пожалуйста, выберите сначала группу сертификата.")
        return false;
    }

    if ($(obj).find(":checked").attr("data-force-nominal") > 0) {
        value = nominal;
    } else {
        value = 0;
    }
    $("#nominalField").val(value);

    return true;
}

function showNextContainer(obj) {
    if ($(obj).prop('checked') == true) {
        var nextShow = true;

        // отобразить все отмеченные разделы до первого не отмеченного
        $(obj).parents(".checkbox-container").nextAll().each(function () {
            if (nextShow) {
                $(this).show();
            }
            if (0 in $(this).find('input[type="checkbox"]') && !$(this).find('input[type="checkbox"]')[0].checked) {
                nextShow = false;
            }
        });
    } else {
        $(obj).parents(".checkbox-container").nextAll().hide();
        $(obj).parents(".checkbox-container").nextAll().find('input').attr('checked', false);
    }
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
