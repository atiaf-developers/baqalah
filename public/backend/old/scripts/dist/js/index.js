$(document).ready(function(){
	/*start multiple tab client request*/
    $("#edit-bttn").click(function(){
        $("#textarea-section").hide();
    });
$(".tab").click(function()
                    {
                    var X=$(this).attr('id');
                    if(X=='info')
                    {
                        $("#info").addClass('select');
                        $("#branch").removeClass('select');
                        $("#photo").removeClass('select');
						$("#info-box").show();
						$("#branch-box").hide();
                        $("#photo-box").hide();
					}
                else if(X=='branch')
                {
                    	$("#info").removeClass('select');
                        $("#branch").addClass('select');
                        $("#photo").removeClass('select');
						$("#info-box").hide();
						$("#branch-box").show();
                        $("#photo-box").hide();
                }
                else
                {
                    	$("#info").removeClass('select');
                        $("#branch").removeClass('select');
                        $("#photo").addClass('select');
						$("#info-box").hide();
						$("#branch-box").hide();
                        $("#photo-box").show();
                }
            });
		/*end multiple tab client request*/
		$(".client-request-tab #client-request-tab1 a").click(function(){
		$(".client-request-tab #client-request-tab1 a").css({"background-color":"#c1c1c1", "color":"#000"});
		$(this).css({"background-color":"#666","color":"#fff"});
	});
	$("aside .btn-group-vertical button").click(function(){
		$("asside button li").show();
	});
	/*start categry search tab color*/
	$(".category-search-tab #category-search-tab1 a").click(function(){
		$(".category-search-tab #category-search-tab1 a").css({"background-color":"#c1c1c1","color":"#000"});
		$(this).css({"background-color":"#666","color":"#fff"});
	});
	/*end categry search tab color*/
	/*start edit tab page color*/
	$(".edit-tab #edit-tab1 a").click(function(){
		$(".edit-tab #edit-tab1 a").css({"background-color":"#c1c1c1","color":"#000"});
		$(this).css({"background-color":"#666","color":"#fff"});
	});
	/*end edit tab page color*/
	/*start edit tab page*/
	$(".tab").click(function()
                    {
                    var X=$(this).attr('id');
                    if(X=='main')
                    {
                        $("#main").addClass('select');
                        $("#branch").removeClass('select');
                        $("#categ").removeClass('select');
						$("#tags").removeClass('select');
						$("#main-box").show();
						$("#branch-box").hide();
						$("#categ-box").hide();
                        $("#tags-box").hide();
					}
                	else if(X=='branch')
                	{
                    	$("#main").removeClass('select');
                        $("#branch").addClass('select');
                        $("#categ").removeClass('select');
						$("#tags").removeClass('select');
						$("#main-box").hide();
						$("#branch-box").show();
						$("#categ-box").hide();
                        $("#tags-box").hide();
                	}
		 			else if(X=='categ')
                	{
                    	$("#main").removeClass('select');
                        $("#branch").removeClass('select');
                        $("#categ").addClass('select');
						$("#tags").removeClass('select');
						$("#main-box").hide();
						$("#branch-box").hide();
						$("#categ-box").show();
                        $("#tags-box").hide();
                	}
                	else
                	{
                    	$("#main").removeClass('select');
                        $("#branch").removeClass('select');
                        $("#categ").removeClass('select');
						$("#tags").addClass('select');
						$("#main-box").hide();
						$("#branch-box").hide();
						$("#categ-box").hide();
                        $("#tags-box").show();
					}
            });
	/*end edit tab page*/
    /*start side bar*/
        $(".main-sidebar .sidebar .sidebar-menu .treeview").click(function(){
            $(this).children(".main-sidebar .sidebar .sidebar-menu .treeview .treeview-menu").toggle();
        });
        $(".main-sidebar .sidebar .sidebar-menu .treeview a").click(function(){
            $(".main-sidebar .sidebar .sidebar-menu .treeview a").css("background-color","#a0a5ab");
            $(this).css("background-color","#666");
        });
        $(".main-sidebar .sidebar .sidebar-menu .treeview a").click(function(){
            $(".main-sidebar .sidebar .sidebar-menu .treeview a").css("background-color","#a0a5ab");
            $(this).css("background-color","#666");
        });
        $(".main-sidebar .sidebar .sidebar-menu .treeview .treeview-menu li a").hover(function(){
            $(".main-sidebar .sidebar .sidebar-menu .treeview .treeview-menu li a").css("background-color","#a0a5ab");
            $(this).css("background-color","#878b90");
        });
    /*end side bar*/
	/*start sidebar visible*/
		$(".navbar-header button").click(function(){
		  $(".main-sidebar").toggle();
        });
	/*end sidebar visible*/
	/*start resize sidebar*/
        var f = 50;
    	$(".main-sidebar").height($(window).height() - f);
    	   $(window).on("resize",function(){
           $(".main-sidebar").height($(window).height() - f);
        });
});
	
