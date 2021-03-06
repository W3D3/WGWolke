<?php
require_once("../dao/DaoFactory.php");
require_once("SessionHelper.php");

class PrintHelper {

    public static function includeCSS() { ?>
        <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
        <link href="../vendor/sb-admin/css/sb-admin-2.css" rel="stylesheet">
        <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="../css/wg_wolke.css" rel="stylesheet" type="text/css">
    <?php }

    public static function includeDataTablesCSS() { ?>
        <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
        <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">    
    <?php }

    public static function includeJS() { ?>
        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="../vendor/metisMenu/metisMenu.min.js"></script>
        <script src="../vendor/sb-admin/js/sb-admin-2.js"></script>
        <script src="../js/wg_wolke.js"></script>
    <?php }

    public static function includeDataTablesJS() { ?>
        <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
        <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>    
    <?php }

// TODO move to Module.php
    public static function printModule($module) {
        $title = $module->getName();

        // calculate value
        $value = "Value";
        $subtext = "";
        $icon = "";
        $module_color = "";
        $url = "#";

        switch($module->getType()) {
            case 1:
                $icon = "icon-member";
                $module_color = "primary";
                $value = self::getMemberCount();
                $url = "Member.php";
                break;
            case 2:
                $icon = "icon-finances";
                $module_color = "green";
                $value = "€ 42,02";
                break;
            case 3:
                $icon = "icon-menuplan";
                $module_color = "red";
                $value = self::getTodayDish();
                $url = "Menuplan.php";
                break;
            case 4:
                $icon = "icon-todo";
                $module_color = "yellow";
                $url = "ToDoList.php";
                $value = self::getTodoListCount();
                break;
        }

        self::printTile($icon, $module_color, $title, $value, Resources::$text_view_details, $url);
        ?>
    <?php }

    public static function printTile($icon, $color, $title, $value, $subtext, $url) { ?>
        <div class="col-md-6">
            <div class="panel panel-<?php echo $color; ?>">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <div class="<?php echo $icon; ?> scaling-normal">
                            </div>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"> <?php echo $value; ?> </div>
                            <div> <?php echo $title; ?> </div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo $url; ?>">
                    <div class="panel-footer">
                        <span class="pull-left"><?php echo $subtext; ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    <?php }

    public static function printNavBar($title, $user, $community) {        
        $newsFeeds = DaoFactory::createNewsFeedDao()->getByCommunity($community->getObjectId());
        ?>

        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a href="Dashboard.php"><img class="navbar-brand" src="../images/wg_wolke.png" ></a>
                <a class="navbar-brand" href="Dashboard.php"> <?php echo $title; ?>
                    <?php echo " - " . htmlspecialchars($community->getName()); ?>
                </a>
            </div>

            <ul class="nav navbar-top-links navbar-right">
                <li class="">
                    <span class="text-muted ">
                        <?php
                            echo htmlspecialchars($user->getFirstName()) .
                                " " .
                                htmlspecialchars($user->getLastName());
                        ?>
                    </span>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li>
                            <a href="#"><i class="fa fa-gear fa-fw"></i> Settings </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="index.php?logout=1"><i class="fa fa-sign-out fa-fw"></i> Logout </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav in padding-small" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input id="input-google-it" class="form-control" placeholder="Google it!" type="text">
                                <span class="input-group-btn">
                                    <button id="button-google-it" class="btn btn-default" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </li>
                        <li class="li-padding">
                            <h3  style="text-align: center; margin: 0 !important;"> Black Board 
                                <button type="button" data-toggle="modal" data-target="#blackboard-dialog" id="button-blackboard" class="btn btn-info btn-circle btn-offset">
                                    <i class="fa fa-plus"></i>
                                </button> 
                            </h3>
                        </li>
                        <?php
                            foreach($newsFeeds as $newsFeed) {
                                $newsFeed->createView();
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>    
    <?php }

    public static function createBlackBoardDialog() { ?>
        <div class="modal fade" id="blackboard-dialog" tabindex="-1" role="dialog" aria-labelledby="blackboardTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="blackboardTitle"><?php echo Resources::$title_new_entry; ?></h4>
                </div>
                <div class="modal-body">
                    <?php
                        FormGenerator::createTextField("bb-title", "Title", true);
                        FormGenerator::createTextArea("bb-message", 3, "Message", false, 200);
                    ?>
                    <div class="alert alert-danger" id="alert-incorrect-data">
                        <?php echo Resources::$invalid_entries; ?>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo Resources::$button_cancel; ?></button>
                    <button type="button" class="btn btn-primary" id="button-create"><?php echo Resources::$button_create; ?></button>
                </div>
            </div>
        </div>
    </div>
    
    
    <?php }

    public static function printTag($content, $type) {
        echo "<span class=\"wg-tag wg-tag-" . $type . "\">" . htmlspecialchars($content) . "</span>";
    }


    // helper functions
    
    private static function getMemberCount() {
        $count = 1;
        $user_oid = SessionHelper::getCurrentUserOid();
        if ($user_oid !== null) {
            $user = DaoFactory::createUserDao()->getById($user_oid);
            if ($user !== null) {
                $count = DaoFactory::createCommunityDao()->getUserCount($user->getCommunityOid());
            }
        }
        return $count;
    }

    private static function getTodoListCount() {
        $count = 0;
        $user_oid = SessionHelper::getCurrentUserOid();
        if ($user_oid !== null) {
            $count = count(DaoFactory::createToDoListDao()->getByCreatorOid($user_oid));
            $count += count(DaoFactory::createToDoListDao()->getByMemberOid($user_oid));            
        }
        return $count;
    }

    private static function getTodayDish(){
        $food = "Kein Essen verfügbar";
        $user_oid = SessionHelper::getCurrentUserOid();
        $community_oid = DaoFactory::createUserDao()->getById($user_oid)->getCommunityOid();
        if($community_oid !==null){
            $dish = DaoFactory::createDishItemEntryDao()->getTodayDish($community_oid);
            $food = DaoFactory::createDishItemDao()->getById($dish->getDishItemOid()); 
        }
        return $food->getName();
    }
}