<?php 
if(IN_MANAGER_MODE!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODx Content Manager instead of accessing this file directly.");
// count messages
$sql="SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$modx->getLoginUserID()." and messageread=0;";
$rs = mysql_query($sql); 
$row = mysql_fetch_assoc($rs);
$_SESSION['nrnewmessages'] = $row['count(*)'];
$sql="SELECT count(*) FROM $dbase.".$table_prefix."user_messages where recipient=".$modx->getLoginUserID()."";
$rs = mysql_query($sql);
$row = mysql_fetch_assoc($rs);
$_SESSION['nrtotalmessages'] = $row['count(*)'];
$messagesallowed = $modx->hasPermission('messages');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>MODx</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $etomite_charset; ?>" />
	<link rel="stylesheet" type="text/css" href="media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>style.css<?php echo "?$theme_refresher";?>" />
	<script type="text/javascript">var MODX_MEDIA_PATH = "<?php echo IN_MANAGER_MODE ? "media":"manager/media"; ?>";</script>
	<script type="text/javascript" language="JavaScript" src="media/script/modx.js"></script>
	<script type="text/javascript" language="JavaScript">
		document.setIncludePath("media/script/bin/");
		document.addEventListener("oninit",function() { 
			document.include("dynelement");
		})
	</script>	
	<script type="text/javascript" language="JavaScript" src="media/script/cb2.js"></script>
	<script language="JavaScript" type="text/javascript">

		var MODX_MEDIA_PATH = "media"; // set media path

		function document_onload() {
 			stopWorker();
 			msgCount(); 
 			hideLoader();
 			<?php echo isset($_REQUEST['r']) ? " doRefresh(".$_REQUEST['r'].");" : "" ;?>;  
		};
		
		var dontShowWorker = false;
		function document_onunload() {
 			if(!dontShowWorker) {
 				top.scripter.work();
 			}
 		};
		
		// set tree to default action.		
		parent.menu.ca = "open";

		function msgCount() {
			try {
				top.scripter.startmsgcount(<?php echo $_SESSION['nrnewmessages'] ; ?>,<?php echo $_SESSION['nrtotalmessages'] ; ?>,<?php echo $messagesallowed ? 1:0 ; ?>);
			} catch(oException) {
				ww = window.setTimeout('msgCount()',1000);
			}
		}
		
		function stopWorker() {
			try {
				parent.scripter.stopWork();
			} catch(oException) {
				ww = window.setTimeout('stopWorker()',500);
			}
		}
		
		function doRefresh(r) {
			try {
				rr = r;
				top.scripter.startrefresh(rr);
			} catch(oException) {
				vv = window.setTimeout('doRefresh()',1000);
			}
		}
		var documentDirty=false;

		function checkDirt() {
			if(documentDirty==true) {
				event.returnValue = "<?php echo $_lang['warning_not_saved']; ?>";
			}
		}
		
		function saveWait(fName) {
			document.getElementById("savingMessage").innerHTML = "<?php echo $_lang['saving']; ?>";
			for(i = 0; i < document.forms[fName].elements.length; i++) {
				document.forms[fName].elements[i].disabled='disabled';
			}
		}

		var managerPath = "";

		function hideLoader() {
			document.getElementById('preLoader').style.display = "none";
		}
		
		retry=0;
		function loadagain(id) {
			try {
				top.menu.Sync(<?php echo $syncid; ?>);
			} catch(oException) {
				retry=retry + 1;
				if(retry<4) {
					xyy=window.setTimeout("loadagain(<?php echo $syncid; ?>)", 2000);
				} else {
					//alert("Failed to sync to tree!");
				}
			}
		}

		hideL = window.setTimeout("hideLoader()", 5000);
		
	</script>
<?php
if($_SESSION['browser']=='ie') {
?>   
	<style>
	/* stupid box model hack for equally stupid MSIE */
	.sectionHeader, .sectionBody {
		width:100%;
	}
	</style>
<?php
}
?>
	<style>
	#preLoader {
		position: 						absolute;
		z-index:						50000;
		width:							100%;
		height:							100%;
		text-align:						center;
		vertical-align:					middle;
	}
	.preLoaderText {
		background-color:				#ffffff;
		width:							300px;
		height:							150px;
		padding:						50px;
		border:							1px solid #003399;
	}
	</style>
</head>
<body ondragstart="return false" onbeforeunload="checkDirt();">

<div id="preLoader"><table width="100%" height="50%" border="0" cellpadding="0"><tr><td align="center"><div class="preLoaderText"><?php echo $_lang['loading_page']; ?></div></td></tr></table></div>
