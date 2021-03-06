   <!-- BEGIN HEADER -->
    <div class="header navbar navbar-inverse">
            
        <div class="navbar-inner">
            <div class="container-fluid">
               <a href="?"><font style="color: rgb(255, 255, 255); font: 18px/36px 'Microsoft YaHei',arial;"><font style="color:#f54"></font><?=$sysName?></font></a>
               <ul class="nav pull-right" style="color:#ffffff;padding:10px 20px 0 0;">
                   <li><i class="halflings-icon white user"></i> 你好,<?=$admin?> &nbsp;&nbsp;<i class="halflings-icon white off"></i><a href="?c=Login&a=Logout" style="display:inline;color:#ffffff;padding-left:5px;">退出登录</a></li>
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
                <!-- <li><div class="sidebar-toggler hidden-phone"></div></li> -->
                <?php
                $menuArr = ZOL_Config::get("Admin_Menu");
                
                if($menuArr){
                    foreach($menuArr as $name => $menu){
                        //权限判定
                        if(isset($menu["permission"])){
                            if(!in_array($adminType, $menu["permission"])){
                                continue;
                            } 
                        }
                        $cls = isset($menu['class']) ? $menu['class'] : '';
                        echo '<li class="sel '.$cls.'">
                                <a href="javascript:;">
                                <img src="icons/'.$menu["icon"].'" align="absmiddle" width="20" height="20" style="width:20px;vertical-align:middle;"/> <span class="title">'.$name.'</span><span class="arrow "></span></a>
                                <ul class="sub-menu">
                            ';
                        
                        foreach($menu['items'] as $k => $v){
                            //权限判定
                            if(isset($v["permission"])){
                                if(!in_array($adminType, $v["permission"])){
                                    continue;
                                } 
                            }
                        
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