var flatpickr_option={enableTime:!0,minDate:"today",locale:{firstDayOfWeek:1},time_24hr:!0};$(function(){flatpickr("input[id^=dtp]",flatpickr_option)}),$(function(){$(".users-panel-info").on("click",function(){var e=$(this).parent();$(".users-panel-body",e).slideToggle(300)})}),$(function(){$(".send_group").on("click",function(){var e=$(this).parent(),t=e.children('input[type="hidden"]').val(),n=e.attr("id"),a=n.substr(4),i=e.children("input:checked").val(),o="id="+escape(a)+"&role="+escape(i)+"&old="+escape(t);$.ajaxSetup({cache:!1}),$.ajax({url:"/ajax/adminpanel/setban",type:"POST",data:o,success:function(){e.children('input[type="hidden"]').val(i),"ROLE_BANNED_0"==i?($("#panel"+a).removeClass("background-warning"),$("#panel"+a).removeClass("background-danger")):"ROLE_BANNED_1"==i?($("#panel"+a).removeClass("background-danger"),$("#panel"+a).addClass("background-warning")):($("#panel"+a).removeClass("background-warning"),$("#panel"+a).addClass("background-danger"))}})})}),$(document).ready(function(){var e=$("input:checked").val();1==e?($(".schema1").css("display","block"),$(".schema2").css("display","block"),$(".schema3").css("display","none")):2==e?($(".schema1").css("display","block"),$(".schema2").css("display","none"),$(".schema3").css("display","none")):($(".schema1").css("display","block"),$(".schema2").css("display","block"),$(".schema3").css("display","block"))}),$(function(){$(document).on("focus",".radios",function(){var e=$(this).val();console.log(e),1==e?($(".schema1").show(500),$(".schema2").show(500),$(".schema3").hide(500)):2==e?($(".schema1").show(500),$(".schema2").hide(500),$(".schema3").hide(500)):($(".schema1").show(500),$(".schema2").show(500),$(".schema3").show(500))})}),$(document).ready(function(){var e=$(".tournament-user div").length;$(".allow").html(e)}),$(function(){$(document).on("click",".preteam",function(){var e=$(this),t=e.attr("id"),n=t.substr(1);e.removeClass("preteam"),e.addClass("team");var a=$("#schema").html(),i=$(".tournament-user div").length;if(1==a){var o=$(".tournament-user");o.append(e)}else if(2==a){var o=$(".tournament-user");o.append(e),i%2==0?o.append(" - "):o.append("<br>")}else if(3==a){var r=$("#groups").html(),o=$(".tournament-user");if(i%r==0){var s;0==i&&(s="A"),4==i&&(s="B"),8==i&&(s="C"),12==i&&(s="D"),16==i&&(s="E"),20==i&&(s="F"),24==i&&(s="G"),26==i&&(s="H"),o.append("<br>ГРУППА "+s+"<br>")}o.append(e)}$(".form-hidden").append('<input type="hidden" id="form_user_'+n+'" name="form[user]['+n+']" value="'+n+'" class="form-control" />');var l=$(".tournament-user div").length;$(".allow").html(l);var c=$(".all").html(),u=$(".but");0==$("#groups").html()?c==l?(console.log("all "+c+" allow "+l),u.show()):u.hide():2==l||4==l||8==l||16==l||32==l||64==l||128==l||256==l?u.show():u.hide()})}),$(function(){$("#add_score").on("click",function(e){e.stopPropagation();var t=$(".scoregroup"),n=$(".score").last(),a=$(".scoregroup > .score").length,i=n.clone();$("input",i).prop("id","dtpbefore"+a),i.appendTo(t),i.find("input[id^=dtp]").flatpickr(flatpickr_option)})}),$(function(){$("#remove_score").on("click",function(){$(".scoregroup > .score").length>1?$(".score:last").remove():alert("Должен быть минимум один матч")})}),$(function(){$(".headsmile").on("click",function(){$(".smilepanel").slideToggle(200)})}),$(function(){$(".bbimg").on("click",function(){var e=$(this).attr("id"),t=$("textarea");if("post"==e)var n="post:",a="";else var n="["+e+"]",a="[/"+e+"]";var i=t[0].selectionStart,o=t[0].selectionEnd,r=t.val(),s=t.val().substr(i,o-i),l=n+s+a,c=t.val().length,u=r.substr(0,i),d=r.substr(o,c);t.val(u+l+d);t.val().length;t.focus(),0==s.length?t[0].setSelectionRange(i+n.length,i+n.length):t[0].setSelectionRange(i,o+n.length+a.length)})}),$(function(){$(".smiles").on("click",function(){var e=$(this).attr("id"),t=$("textarea"),n=e,a="";console.log(n);var i=t[0].selectionStart,o=t[0].selectionEnd,r=t.val(),s=t.val().substr(i,o-i),l=n+s+a,c=t.val().length,u=r.substr(0,i),d=r.substr(o,c);t.val(u+l+d);t.val().length;t.focus(),0==s.length?t[0].setSelectionRange(i+n.length,i+n.length):t[0].setSelectionRange(i,o+n.length+a.length)})}),$(function(){$(".spoiler-name").on("click",function(){var e=$(this).parent();$(".spoiler-body:first",e).slideToggle(200),"+"==$(".sign",e).html()?$(".sign:first",e).html("−"):$(".sign:first",e).html("+")})}),$(function(){$(".tumbler").on("click",function(){var e=$(this).attr("id"),t=e.substr(0,1),n=e.substr(1);$("#u"+n).html(""),$("#d"+n).html("");var a="id="+escape(n)+"&sign="+escape(t);$.ajaxSetup({cache:!1}),$.ajax({url:"/guestbook/ranking",data:a,type:"POST",dataType:"json",success:function(e){0==e.sum?$("#r"+n).html("<span class='gray'>"+e.sum+"</span>"):e.sum>0?$("#r"+n).html("<span class='green'>+"+e.sum+"</span>"):e.sum<0&&$("#r"+n).html("<span class='red'>"+e.sum+"</span>"),0==e.author_sum?$(".us"+e.author_id).html("<span class='gray'>"+e.author_sum+"</span>"):e.author_sum>0?$(".us"+e.author_id).html("<span class='green'>+"+e.author_sum+"</span>"):e.author_sum<0&&$(".us"+e.author_id).html("<span class='red'>"+e.author_sum+"</span>"),$("#ru"+n).html(e.plus),$("#rd"+n).html(e.minus)}})})}),$(function(){$(".post-ranking").on("mouseenter mouseleave",function(){var e=$(this).parent();$(".rank-users",e).stop(!0,!1).slideToggle(400)})}),$(function(){$('div[id^="nach"]').on("mouseenter",function(e){e.preventDefault(),$(".toolkit",this).stop(!0,!1).slideDown(200)})}),$(function(){$('div[id^="nach"]').on("mouseleave",function(e){e.preventDefault(),$(".toolkit",this).stop(!0,!1).slideUp(200)})}),$(function(){$('div[id^="topnach"]').on("mouseenter",function(e){e.preventDefault(),$(".toptoolkit",this).stop(!0,!1).fadeIn(200)})}),$(function(){$('div[id^="topnach"]').on("mouseleave",function(e){e.preventDefault(),$(".toptoolkit",this).stop(!0,!1).fadeOut(200)})}),$(function(){$(".edit-post").on("click",function(){var e=$(this).attr("id"),t=e.substr(4),n=$("#message"+t),a=n.html().trim(),i=$("#hm"+t).text().trim();$("#hm"+t).html(a);var o=n.outerHeight(!0)+5;o<150&&(o=150),n.html("<textarea style='height: "+o+"px'>"+i+"</textarea><button id='update"+t+"' class='update button button-main'>Обновить</button><button id='cancel"+t+"' class='cancel button button-main'>Отмена</button>")})}),$(function(){$(document).on("click",".cancel",function(){var e=$(this).attr("id"),t=e.substr(6),n=$("#hm"+t).html().trim(),a=$("#message"+t+" > textarea").html().trim();$("#hm"+t).html(a),$("#message"+t).html(n)})}),$(function(){$(document).on("click",".update",function(){var e=$(this).attr("id"),t=e.substr(6),n=($("#post"+t),$("#message"+t+" > textarea").val()),a="id="+escape(t)+"&message="+encodeURIComponent(n);$.ajaxSetup({cache:!1}),$.ajax({url:"/guestbook/edit",data:a,type:"POST",dataType:"json",success:function(e){1==e.error?alert(e.text):($("#message"+t).html(e.text),$("#hm"+t).html(e.hide),0!=e.edit&&$("#post-edit"+t).html("...<i class='fa fa-edit'></i>"+e.edit))}})})}),$(function(){$(".quote").on("click",function(){var e=$(this).parent().parent().parent().parent().attr("id"),t=e.substr(4),n=$(this).parent().parent().prev().children().next().children().children().html().trim(),a=$("#hd"+t).text().trim(),i=$("#hm"+t).text().trim(),o="[quote author="+n+" date="+a+" post="+t+"]\n"+i+"\n[/quote]\n\n",r=$("textarea"),s=r[0].selectionStart,l=r[0].selectionEnd,c=r.val(),u=c.substr(0,s),d=r.val().length,p=c.substr(l,d);return r.val(u+o+p),r.focus(),r[0].setSelectionRange(s+o.length,s+o.length),$("html, body").animate({scrollTop:400},500),!1})}),$(function(){$('div[id^="nach"]').on("mouseenter",function(e){e.preventDefault(),$(".toolkit",this).stop(!0,!1).slideDown(200)})}),$(function(){$('div[id^="nach"]').on("mouseleave",function(e){e.preventDefault(),$(".toolkit",this).stop(!0,!1).slideUp(200)})}),$(function(){$('div[id^="topnach"]').on("mouseenter",function(e){e.preventDefault(),$(".toptoolkit",this).stop(!0,!1).fadeIn(200)})}),$(function(){$("#navigation li").on("mouseenter",function(){$("ul",this).stop(!0,!1).slideDown(300)})}),$(function(){$("#navigation li").on("mouseleave",function(){$("ul",this).stop(!0,!1).slideUp(300)})}),$(function(){$('input[type="file"]',this).on("change",function(){var e=$(this);$(".in-file-text").html(e.val())})}),$(function(){$(".notification").on("click",function(){var e=$(this).attr("id"),t=$(this).prop("checked"),n="notification="+escape(e)+"&status="+escape(t);$.ajaxSetup({cache:!1}),$.ajax({url:"/notification/set",data:n,type:"POST",dataType:"json",success:function(e){}})})}),$(function(){$(".tournament-access").on("click",function(){var e=$(this).attr("id"),t=e.substr(4),n=$(this).prop("checked"),a=$("#trid").html();console.log(n);var i="user="+escape(t)+"&status="+escape(n)+"&tr="+escape(a);$.ajaxSetup({cache:!1}),$.ajax({url:"/access/set",data:i,type:"POST",dataType:"json",success:function(e){}})})}),$(function(){$(".player-scored").on("click",function(){var e=$("#howscored"),t=e.text();scored=["/public/images/scored/empty.png","/public/images/scored/goal.png","/public/images/scored/assist.png","/public/images/scored/yellow.png","/public/images/scored/red.png"];var n=$("img",this).attr("src");if(active=["empty","goal","assist","yellow","red"],t<=7)if(7==t&&"/public/images/scored/empty.png"==n)alert("Можно выбрать только 7 действий!");else{for(i=0;i<scored.length;i++)n==scored[i]&&(y=i);4==y?x=0:x=y+1,$("img",this).attr("src",scored[x]),$("input",this).attr("value",active[x]);var a=$('.player-scored > input[value!="empty"]').length;e.text(a)}})}),$(function(){$(".player-keeper").on("click",function(){scored=["/public/images/scored/emptykeeper.png","/public/images/scored/null.png","/public/images/scored/one.png","/public/images/scored/two.png","/public/images/scored/three.png"];var e=$("img",this).attr("src");for(active=["emptykeeper","null","one","two","three"],i=0;i<scored.length;i++)e==scored[i]&&(y=i);4==y?x=0:x=y+1,$("img",this).attr("src",scored[x]),$("input",this).attr("value",active[x])})}),$(function(){$("#add_option").on("click",function(){var e=$(".optionsgroup > input").length;$(".optionsgroup").append('<input type="text" name="option['+e+']">')})}),$(function(){$("#remove_option").on("click",function(){$(".optionsgroup > input").length>2?$(".optionsgroup input:last").remove():alert("Должно быть минимум два варианта ответа")})});