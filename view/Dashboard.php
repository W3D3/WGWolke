<?php
require_once("../code/PrintHelper.php");
require_once("../code/SessionHelper.php");
require_once("../code/Util.php");
require_once("../dao/DaoFactory.php");

SessionHelper::doActivity();
$user_oid = SessionHelper::getCurrentUserOid();
if ($user_oid === null) {
    Util::redirect("index.php");
}

$user = DaoFactory::createUserDao()->getById($user_oid);
if ($user === null) {
    Util::redirect("index.php?error=1");
}

$community = DaoFactory::createCommunityDao()->getByUserOid($user_oid);
if ($community === null) {
    Util::redirect("index.php?error=2");
}

$modules = DaoFactory::createCommunityDao()->getModules($community->getObjectId());
$newsFeeds = DaoFactory::createNewsFeedDao()->getByCommunity($community->getObjectId());

?>

<!DOCTYPE html>
<html>
<head>
    <?php PrintHelper::includeCSS(); ?>
    <title> Dashboard </title>
</head>

<body>
    
    <div class="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <img class="navbar-brand" src="../images/wg_wolke.png" >
                <a class="navbar-brand" href="Dashboard.php">Dashboard
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
                    <ul class="nav in" id="side-menu">
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
                        <li>
                                                   
                                       
                        </li>

                        <?php

                            foreach($newsFeeds as $newsFeed) {
                                PrintHelper::printNewsFeedItem($newsFeed);
                            }

                        ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-wrapper">
            <br />
            <div class="row">

                <?php
                    foreach($modules as $module) {
                        if ($module->getType() === 1 && !$user->isOwner()) continue;
                        PrintHelper::printModule($module);
                    }
                ?>                
            </div>

        </div>

    </div>
    
    <div class="modal fade" id="blackboard-dialog" tabindex="-1" role="dialog" aria-labelledby="blackboardTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="blackboardTitle">New Entry</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="blackboard-title-form-group">
                        <input class="form-control" placeholder="Title" id="blackboard-title" name="blackboard-title" type="text" autofocus maxlength="50">
                    </div>
                    <div class="form-group" id="blackboard-message-form-group">
                        <textarea id="blackboard-message" class="form-control" rows="3" placeholder="Message" maxlength="200"></textarea>
                    </div>
                    <div class="alert alert-danger" id="alert-incorrect-data">
                        We are sorry, some fields contain incorrect information.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="button-create">Create</button>
                </div>
            </div>
        </div>
    </div>

    <?php PrintHelper::includeJS(); ?>

    <script type="text/javascript">

        $(document).ready(function() {
            $("#alert-incorrect-data").hide();
            $("#blackboard-title").popover({content: "Title must not be empty", trigger: "focus", placement:"bottom"});
            $("#blackboard-title").focusout(validateTitle);
            $("#button-create").click(validateBlackboardForm);
            $("#button-google-it").click(searchGoogle);

            $("#input-google-it").keyup(function(e) {
                if (e.which === 13) {
                    searchGoogle();
                }
            });
        });

        function validateBlackboardForm() {
            var title = $("#blackboard-title");
            var message = $("#blackboard-message");

            validateTitle();
            var isValid = validateTitle();

            if (isValid) {
                $("#alert-incorrect-data").hide();
                $.ajax({
                    url: "../code/CreateNewsFeedItem.php",
                    type: "POST",
                    data: {
                        title: title.val(),
                        message: message.val(),
                        community_oid: "<?php echo $community->getObjectId(); ?>",                        
                        user_oid: "<?php echo $user_oid ?>"
                    },
                    dataType: "json"
                }).done(function() {
                    // Util::redirect("Dashboard.php");
                    window.location.replace("Dashboard.php");
                }).fail(function(jqXhr, status, error) {
                    console.log(jqXhr, status, error);
                });
            } else {
                $("#alert-incorrect-data").show();
            }
        }

        function validateTitle() {
            var title = $("#blackboard-title");
            if (title.val().length === 0) {
                $("#blackboard-title-form-group").addClass("has-error");
            } else {
                $("#blackboard-title-form-group").removeClass("has-error");
            }
            return title.val().length !== 0;
        }

        function searchGoogle() {
                var input = $("#input-google-it").val();
                window.open("https://www.google.com/search?q=" + input, "_blank");
                $("#input-google-it").val("");
        }

    </script>
</body>
</html>