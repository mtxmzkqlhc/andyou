   <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse navbar-fixed-top">
            
        <div class="navbar-inner">
            <div class="container-fluid">
               <a href="?"><font style="color: rgb(255, 255, 255); font: 20px/40px 'Microsoft YaHei',arial;"><font style="color:#f54"></font><?=$sysName?></font></a>
               <ul class="nav pull-right" style="color:#ffffff;padding:10px 20px 0 0;">
                   <li><i class="halflings-icon white user"></i> ÄãºÃ,<?=$admin?> &nbsp;&nbsp;<i class="halflings-icon white off"></i><a href="?c=Login&a=Logout" style="display:inline;color:#ffffff;padding-left:5px;">ÍË³öµÇÂ¼</a></li>
               </ul>
            </div>
        </div>
        
    </div>
    <!-- END HEADER -->
    <!-- BEGIN CONTAINER -->   
    <div class="page-container row-fluid" id="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar nav-collapse"  id="page-sidebar">
            <!-- BEGIN SIDEBAR MENU -->        
            <ul class="page-sidebar-menu">
                <li><div class="sidebar-toggler hidden-phone"></div></li> 
                <?php
                $menuArr = ZOL_Config::get("Admin_Menu");
                
                if($menuArr){
                    foreach($menuArr as $name => $menu){
                        $cls = isset($menu['class']) ? $menu['class'] : '';
                        echo '<li class="sel '.$cls.'">
                                <a href="javascript:;"><i class="icon-'.$menu["icon"].'"></i><span class="title">'.$name.'</span><span class="arrow "></span></a>
                                <ul class="sub-menu">
                            ';
                        
                        foreach($menu['items'] as $k => $v){
                            $targetStr = isset($v['target']) ? " target='{$v['target']}' " : "";
                            echo '<li><a href="'.$v['url'].'" '.$targetStr.'>'.$k.'</a></li>';
                        }
                        
                        echo '</ul></li>';
                    }
                }
                ?>
            </ul>
            <!-- END SIDEBAR MENU -->
        </div>




        <!-- END SIDEBAR -->
        <!-- BEGIN PAGE -->
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div id="portlet-config" class="modal hide">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button"></button>
                    <h3>portlet Settings</h3>
                </div>
                <div class="modal-body">
                    <p>Here will be a configuration form</p>
                </div>
            </div>
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE CONTAINER-->
            <div class="container-fluid">