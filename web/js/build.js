$(document).ready(function(){function a(a){return!isNaN(parseFloat(a))&&isFinite(a)}$(document).ready(function(){var a=new $.slidebars;$("#mobile-icon").on("click",function(){a.slidebars.open("left")}),$("#share-mobile-button").on("click",function(){a.slidebars.open("right")})}),$(document).on("show.bs.modal",".modal",function(){var a=1040+10*$(".modal:visible").length;$(this).css("z-index",a),setTimeout(function(){$(".modal-backdrop").not(".modal-stack").css("z-index",a-1).addClass("modal-stack")},0)}),$("#owl-demo, #owl-demo1").owlCarousel({navigation:!0,slideSpeed:300,paginationSpeed:400,singleItem:!0,navigationText:["",""],rewindNav:!0,scrollPerPage:!1}),$(".selectpicker").selectpicker({style:"btn-info",size:4}),$(".selectpicker").selectpicker("val","Mustard"),$(".selectpickerspc").selectpicker({style:"btn-info kaup-show",size:4}),$(".link-selected").on("click",function(a){var b=$(this).attr("href");window.location.href=b}),$("#googleMap").css("min-height",$("#map-sibling p").innerHeight()),$(".panel-heading").click(function(){$(this).find("span.toggle-icon").toggleClass("glyphicon glyphicon-triangle-top glyphicon glyphicon-triangle-bottom"),$(this).toggleClass("accordion-yellow")}),$("#panel-heading").on("click",function(){$(".collapse2").collapse("toggle")}),$("#panel-heading1").on("click",function(){$(".collapse1").collapse("toggle")}),$("#panel-heading3").on("click",function(){$(".collapse3").collapse("toggle")}),$("#panel-heading4").on("click",function(){$(".collapse4").collapse("toggle")}),$("#panel-heading5").on("click",function(){$(".collapse5").collapse("toggle")}),$(".spc").on("click",function(){$(this).hasClass("open")?$("#kaup-hidden").hide():$("#kaup-hidden").show()}),$("#kategorie-select").on("click",function(){$(this).hasClass("open")?$("#kaup-hidden").hide():$("#kaup-hidden").show(),$(this).toggleClass("open")}),$("#close-haup").on("click",function(){$("#kaup-hidden").hide()}),$("input,textarea").focus(function(){$(this).data("placeholder",$(this).attr("placeholder")).attr("placeholder","")}).blur(function(){$(this).attr("placeholder",$(this).data("placeholder"))}),$('[data-toggle="tooltip"]').tooltip(),$(document).mouseup(function(a){var b=$("#kaup-hidden");b.is(a.target)||0!==b.has(a.target).length||b.hide(),$(".dropdown-menu-span").is(a.target)||0!==$(".dropdown-menu-span").has(a.target).length||($(".dropdown-menu-span").removeClass("active"),$("#open-menu").removeClass("active"),$(".dropdown-menu-span").parent().find("p").removeClass("active"))});var b=$("#footer-toggle"),c=$("#full-footer");b.on("click",function(a){$(this).html("+"),$("#full-footer").is(":hidden")?($("#full-footer").fadeIn(10),$(this).html("-")):$("#full-footer").fadeOut(10),a.stopPropagation()}),$(document).find("body").children().not("#footer").on("click",function(){b.html("+"),$(c).is(":visible")&&$(c).fadeOut(10)}),$("#preis_pro_tag").on("change keydown keypress keyup mousedown click mouseup",function(){var b=$("#preis_pro_tag").val();if(a(b)){var c=parseInt($("#range_30").val()),d=c*b;$(".price").html(d+" €")}}),$("#open-menu").click(function(a){a.preventDefault(),$(".dropdown-menu-span").toggleClass("active"),$("#open-menu").toggleClass("active"),$(".dropdown-menu-span").parent().find("p").toggleClass("active")}),$("body").on("change keydown keypress keyup mousedown click mouseup","#range_30",function(){var b=$("#preis_pro_tag").val();if(a(b)){var c=parseInt($("#range_30").val()),d=c*b;$(".price").html(d+" €")}})}),$(window).on("scroll touchmove",function(){$("#footer").toggleClass("footer-on",$(document).scrollTop()>0)});