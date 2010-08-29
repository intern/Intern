<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?= $TITLE; ?> | Inter Administrator</title>
<meta content="text/html; charset=<?=$CHARSET; ?>"
    http-equiv="Content-Type">
<meta name="Copyright" content="Inter Inc.">
<?= $HEAD; ?>
</head>
<body scroll="no" style="margin: 0px;">
<table cellpadding="0" cellspacing="0" width="100%" height="100%">
    <tbody>
        <tr>
            <td colspan="2" height="90">
                <div class="mainhd">
                    <div id="logo">logo</div>
                    <div class="navbg"></div>
                    <div class="main_nav">
                        <?= $ADMIN_MAIN_MENU ?>
                        <div class="navbd"></div>
                        <div class="breadcrumb"></div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td valign="top" width="160" class="menutd">
                <div id="sub_nav" class="left_menu"><?= $ADMIN_SUB_MENU ?></div>
            </td>
            <td valign="top" class="content">
                <div class="content_top"><h3 class="fl"><?= $TITLE; ?></h3><?= $ADMIN_TAB_MENU ?></div>
                <div class="description"><?= $PAGE_DESC; ?></div>
                <div><?= $PAGE_CONTENT;?></div>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
