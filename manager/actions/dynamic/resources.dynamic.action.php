<?php
if(IN_MANAGER_MODE!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODx Content Manager instead of accessing this file directly.");
?>

<div class="subTitle">
	<span class="right"><img src="media/images/_tx_.gif" width="1" height="5"><br /><?php echo $_lang['resource_management']; ?></span>
</div>

<link type="text/css" rel="stylesheet" href="media/style/tabs.css" /> 
<script type="text/javascript" src="media/script/tabpane.js"></script> 
<div class="sectionHeader"><img src='media/images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['resource_management']; ?></div><div class="sectionBody">
<div class="tab-pane" id="resourcesPane"> 
	<script type="text/javascript">
		tpResources = new WebFXTabPane( document.getElementById( "resourcesPane" ) );
	</script> 

	<!-- Templates -->
    <div class="tab-page" id="tabTemplates"> 
    	<h2 class="tab"><?php echo $_lang["manage_templates"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabTemplates" ) );</script> 
		<p><?php echo $_lang['template_management_msg']; ?></p>

		<ul>
			<li><a href="index.php?a=19"><?php echo $_lang['new_template']; ?></a></li>
		</ul>
		<br />
		<ul>
		<?php

		$sql = "select templatename, id, description, locked from $dbase.".$table_prefix."site_templates order by templatename"; 
		$rs = mysql_query($sql); 
		$limit = mysql_num_rows($rs);
		if($limit<1){
			echo $_lang['no_results'];				
		}
		for($i=0; $i<$limit; $i++) {
			$row = mysql_fetch_assoc($rs);
		?>
			<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=16"><?php echo $row['templatename']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['template_locked_message'].')</small></i>' : "" ; ?></li>
		<?php
		}

		?>
		</ul>
	</div>
	
	<!-- template variables -->
    <div class="tab-page" id="tabVariables"> 
    	<h2 class="tab"><?php echo $_lang["tmplvars"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabVariables" ) );</script> 
		<!--//
			Modified By Raymond for Template Variables
			Added by Apodigm 09-06-2004- DocVars - web@apodigm.com
		-->
		<p><?php echo $_lang['tmplvars_management_msg']; ?></p>
			<ul>
				<li><a href="index.php?a=300"><?php echo $_lang['new_tmplvars']; ?></a></li>
			</ul>
		<?php

			$sql = "SELECT DISTINCT tv.* ";
			$sql.= "FROM $dbase.".$table_prefix."site_tmplvars tv ";
			$sql.= "ORDER BY tv.rank;";

			$rs = mysql_query($sql); 

			include_once "controls/datasetpager.class.php";	
			$dp = new DataSetPager('',$rs,20);
			$dp->setRenderRowFnc("RenderRow");
			$dp->render();
			$pager	= $dp->getRenderedPager();
			$rows 	= $dp->getRenderedRows();
			if($pager) $pager = "Page $pager";
			else $pager = "<br />";

			echo "<table border='0'>";
			echo "<tr><td align='right'>$pager</td></tr>";
			echo "<tr><td><ul>$rows</ul></td></tr>";
			echo "</table>";

			function RenderRow($i,$row){
				global $_lang;

				if($i<1){
					return "<li>".$_lang['no_results']."</li>";
				}
				else {
					return "<li><span style='width: 200px'><a href='index.php?id=".$row['id']."&a=301'>".$row['caption']."</a></span>".($row['description']!='' ? ' - '.$row['description'] : "" ).($row['locked']==1 ? " <i><small>(".$_lang['tmplvars_locked_message'].")</small></i>" : "")."</li>";
				}
			}	
		?>
		<!--//End modification-->
	</div>

	<!-- chunks -->
    <div class="tab-page" id="tabChunks"> 
    	<h2 class="tab"><?php echo $_lang["manage_htmlsnippets"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabChunks" ) );</script> 
		<p><?php echo $_lang['htmlsnippet_management_msg']; ?></p>

		<ul>
			<li><a href="index.php?a=77"><?php echo $_lang['new_htmlsnippet']; ?></a></li>
		</ul>
		<br />
		<ul>
		<?php

		$sql = "select name, id, description, locked from $dbase.".$table_prefix."site_htmlsnippets order by name"; 
		$rs = mysql_query($sql); 
		$limit = mysql_num_rows($rs);
		if($limit<1){
			echo $_lang['no_results'];			
		}
		for($i=0; $i<$limit; $i++) {
			$row = mysql_fetch_assoc($rs);
		?>
			<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=78"><?php echo $row['name']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['snippet_locked_message'].')</small></i>' : "" ; ?></li>
		<?php
		}

		?>
		</ul>
	</div>
	
	<!-- snippets -->
    <div class="tab-page" id="tabSnippets"> 
    	<h2 class="tab"><?php echo $_lang["manage_snippets"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabSnippets" ) );</script> 
		<p><?php echo $_lang['snippet_management_msg']; ?></p>

		<ul>
			<li><a href="index.php?a=23"><?php echo $_lang['new_snippet']; ?></a></li>
		</ul>
		<br />
		<ul>
		<?php

		$sql = "select name, id, description, locked from $dbase.".$table_prefix."site_snippets order by name"; 
		$rs = mysql_query($sql); 
		$limit = mysql_num_rows($rs);
		if($limit<1){
			echo $_lang['no_results'];
		}
		for($i=0; $i<$limit; $i++) {
			$row = mysql_fetch_assoc($rs);
		?>
			<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=22"><?php echo $row['name']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['snippet_locked_message'].')</small></i>' : "" ; ?></li>
		<?php
		}

		?>
		</ul>
	</div>

	<!-- plugins -->
    <div class="tab-page" id="tabPlugins"> 
    	<h2 class="tab"><?php echo $_lang["manage_plugins"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabPlugins" ) );</script> 
		<p><?php echo $_lang['plugin_management_msg']; ?></p>

		<ul>
			<li><a href="index.php?a=101"><?php echo $_lang['new_plugin']; ?></a></li>
		</ul>
		<br />
		<ul>
		<?php

		$sql = "SELECT name, id, description, locked FROM $dbase.".$table_prefix."site_plugins ORDER BY name"; 
		$rs = mysql_query($sql); 
		$limit = mysql_num_rows($rs);
		if($limit<1){
			echo $_lang['no_results'];
		}
		for($i=0; $i<$limit; $i++) {
			$row = mysql_fetch_assoc($rs);
		?>
			<li><span style="width: 200px"><a href="index.php?id=<?php echo $row['id']; ?>&a=102"><?php echo $row['name']; ?></a></span><?php echo $row['description']!='' ? ' - '.$row['description'] : '' ; ?><?php echo $row['locked']==1 ? ' <i><small>('.$_lang['plugin_locked_message'].')</small></i>' : "" ; ?></li>
		<?php
		}

		?>
		</ul>
	</div>

	<!-- keywords -->
    <div class="tab-page" id="tabKeywords"> 
    	<h2 class="tab"><?php echo $_lang["keywords"] ?></h2> 
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabKeywords" ) );</script> 
		<ul>
		<li><span style="width: 200px"><a href="index.php?a=81"><?php echo $_lang['manage_keywords']; ?></a></span> - <?php echo $_lang['keywords_message']; ?></li>
		</ul>
	</div>
	
</div>
</div>
