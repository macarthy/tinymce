<?php
if(IN_MANAGER_MODE!="true") die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODx Content Manager instead of accessing this file directly.");

function RenderRow($i,$row){
	global $_lang;

	if($i<1){
		return "<li>".$_lang['no_results']."</li>";
	}
	else {
		return "<li><span style='width: 200px'><a href='index.php?id=".$row['id']."&a=301'>".$row['caption']."</a></span>".($row['description']!='' ? ' - '.$row['description'] : "" ).($row['locked']==1 ? " <i><small>(".$_lang['tmplvars_locked_message'].")</small></i>" : "")."</li>";
	}
}
$theme = $manager_theme ? "$manager_theme/":"";

?>

<div class="subTitle">
	<span class="right"><img src="media/style/.$theme./images/_tx_.gif" width="1" height="5"><br /><?php echo $_lang['resource_management']; ?></span>
</div>

<link rel="stylesheet" type="text/css" href="media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>tabs.css<?php echo "?$theme_refresher";?>" />
<script type="text/javascript" src="media/script/tabpane.js"></script>
<div class="sectionHeader"><img src='media/style/<?php echo $manager_theme ? "$manager_theme/":""; ?>images/misc/dot.gif' alt="." />&nbsp;<?php echo $_lang['resource_management']; ?></div><div class="sectionBody">
<div class="tab-pane" id="resourcesPane">
	<script type="text/javascript">
		tpResources = new WebFXTabPane( document.getElementById( "resourcesPane" ) );
	</script>

<!-- Templates -->
<?php 	if($modx->hasPermission('new_template') || $modx->hasPermission('edit_template')) { ?>
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
<?php } ?>

<!-- Template variables -->
<?php 	if($modx->hasPermission('new_template') || $modx->hasPermission('edit_template')) { ?>
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

			include_once $base_path."manager/includes/controls/datasetpager.class.php";
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

		?>
		<!--//End modification-->
	</div>
<?php } ?>

<!-- chunks -->
<?php 	if($modx->hasPermission('new_snippet') || $modx->hasPermission('edit_snippet')) { ?>
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
<?php } ?>

<!-- snippets -->
<?php 	if($modx->hasPermission('new_snippet') || $modx->hasPermission('edit_snippet')) { ?>
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
<?php } ?>

<!-- plugins -->
<?php 	if($modx->hasPermission('new_plugin') || $modx->hasPermission('edit_plugin')) { ?>
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
<?php } ?>

<!-- Meta tags -->
<?php 	if($modx->hasPermission('manage_metatags')) { ?>
    <div class="tab-page" id="tabKeywords">
    	<h2 class="tab"><?php echo $_lang["meta_keywords"] ?></h2>
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabKeywords" ) );</script>
		<ul>
		<li><span style="width: 250px"><a href="index.php?a=81"><?php echo $_lang['manage_metatags']; ?></a></span> - <?php echo $_lang['metatag_message']; ?></li>
		</ul>
	</div>
<?php } ?>

<!-- category view -->
    <div class="tab-page" id="tabCategory">
    	<h2 class="tab"><?php echo $_lang["resource_categories"] ?></h2>
    	<script type="text/javascript">tpResources.addTabPage( document.getElementById( "tabCategory" ) );</script>
		<p><?php echo $_lang['category_msg']; ?></p>
		<br />
		<ul>
		<?php
		$tablePre = $dbase.'.'.$table_prefix;
		
		$displayInfo = array();
		if($modx->hasPermission('edit_plugin')) {
            $displayInfo['plugin'] = array('table'=>'site_plugins','action'=>102,'name'=>$_lang['manage_plugins']);
        }
        if($modx->hasPermission('edit_snippet')) {
            $displayInfo['snippet'] = array('table'=>'site_snippets','action'=>22,'name'=>$_lang['manage_snippets']);
            $displayInfo['htmlsnippet'] = array('table'=>'site_htmlsnippets','action'=>78,'name'=>$_lang['manage_htmlsnippets']);
        }
        if($modx->hasPermission('edit_template')) {
            $displayInfo['templates'] = array('table'=>'site_templates','action'=>16,'name'=>$_lang['manage_templates']);
            $displayInfo['tmplvars'] = array('table'=>'site_tmplvars','action'=>301,'name'=>$_lang['tmplvars']);
        }
        if($modx->hasPermission('edit_module')) {
            $displayInfo['modules'] = array('table'=>'site_modules','action'=>108,'name'=>$_lang['modules']);
        }

        $finalInfo = array();

        foreach ($displayInfo as $n => $v) {
            $nameField = ($v['table'] == 'site_templates')? 'templatename': 'name';
            $sql = 'SELECT '.$nameField.' as name, '.$tablePre.$v['table'].'.id, description, locked, '.$tablePre.'categories.category FROM '.$tablePre.$v['table'].' left join '.$tablePre.'categories on '.$tablePre.$v['table'].'.category = '.$tablePre.'categories.id ORDER BY 5,1';
            $rs = mysql_query($sql);
    		$limit = mysql_num_rows($rs);
    		if($limit>0){
    			for($i=0; $i<$limit; $i++) {
                    $row = mysql_fetch_assoc($rs);
                    $row['type'] = $v['name'];
                    $row['action'] = $v['action'];
                    if (empty($row['category'])) {$row['category'] = $_lang['no_category'];}
                    $finalInfo[] = $row;
                }
    		}
        }
        
        foreach($finalInfo as $n => $v) {
            $category[$n] = $v['category'];
            $name[$n] = $v['name'];
        }
        
        array_multisort($category, SORT_ASC, $name, SORT_ASC, $finalInfo);
        
		$preCat = '';
		foreach($finalInfo as $n => $v) {
			if ($preCat !== $v['category']) {
                echo $insideUl? '</ul>': '';
                echo '<li><strong>'.$v['category'].'</strong><ul>';
                $insideUl = 1;
            }
		?>
			<li><span style="width: 200px"><a href="index.php?id=<?php echo $v['id']. '&a='.$v['action'];?>"><?php echo $v['name']; ?></a></span><?php echo ' (' . $v['type'] . ')'; echo $v['description']!='' ? ' - '.$v['description'] : '' ; ?><?php echo $v['locked']==1 ? ' <i><small>('.$_lang['plugin_locked_message'].')</small></i>' : "" ; ?></li>
		<?php
		$preCat = $v['category'];
        }
        echo $insideUl? '</ul>': '';
		?>
		</ul>
	</div>

</div>
</div>
