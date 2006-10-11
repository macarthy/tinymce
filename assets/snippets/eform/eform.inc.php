<?php
# eForm 1.4 BETA 1 - Electronic Form Snippet (Extended)
# Original created by: Raymond Irving 15-Dec-2004.
# Extended by: Jelle Jager (TobyL) September 2006
# -----------------------------------------------------
#
# Captcha image support - thanks to Djamoer
# Multi checkbox, radio, select support - thanks to Djamoer
# Form Parser and extended validation - by Jelle Jager
#
#
# see docs/eform.htm for installation and usage information
# (* = tested in 0.9.5, ? = not tested)
# * WHATS NEW for	version 1.4
# * Fixed: &debug parameter wasn't doing anything
# * Fixed: Returning 'foreach' error when chunk or document not found for form template
# * Fixed: Erroneous 'tampering' error when using multiple forms on a page ($formats persisted between eForm calls)
# * Fixed/Updated: Events now work using the &eFormOnBeforeMailSent and &eFormOnMailSent paramaters.
#   The previous event structure did not appear to work without some tricks (variables were out of scope)
# * Updated:  [+subject+] can now be used in report and thank you templates
# * Updated: When using a document for the templates the document no longer needs to have published set
# * New: Show error if &tpl is set to the same document (id) containing the eForm snippet call
# * New: extended debug messages for validated fields
# ? New: added some protection against mail injection
# ? TODO: send 'abuse alert' mail to [(mailsender)] (controlled by configuration parameter &reportAbuse)?
# * New: added example on how to use eform events
# * New: You can now use <form id="formName"...> to match your &formid=`formName`
#   instead of <input type="hidden" name="formid" value="formName" />
# * New: You can now set the description using the label tag instead of in the eform attribute
#   eg. <label for="email">Your Email</label><input type="text" name="email" eform=":email:1::/>
#		instead of <label>Your Email</label><input type="text" name="email" eform="Your Email:email:1::/>
#   If both are used the eform value takes precedence.
# * New: Added &disclaimer parameter for adding a disclaimer to the email body. Can be chunk name or document id
#   Need to manually add [+disclaimer+] placeholder to report template
# *  Optimized: moved form parser code so it is only executed when form is posted
#	* Updated: You can again use placeholders in &from and &fromname (as you can with &subject and &keywords)
# * Updated: It is now possible to use placeholders in #SELECT validation rule i.e.
#   #SELECT jobnum FROM translation_jobs WHERE client_email={email}
# * New: you can now use the #LIST validation rule for file type checking with file uploads
#   by supplying a list of file extensions eg: #LIST jpg,jpeg,png,gif
# TODO: replace #EVAL with #FUNCTION? Call an external function and not use eval(). More secure...
# TODO: implement storage of data in document or external database. Perhaps this can be done using an
#   external script/snippet and the eformOnBeforeMailSent event? Means less bloated code for simple forms and it is
#   now very simple to add the events.

#Testing Remarks
# Unrelated to eForm -  in 0.9.5 vericode fails more then 50% of the time! tested this with eFrom snippet, directly in browser and via a little script requesting 25 - 50 images.
# parameter &keywords - can't find anywhere in the code where it is used!


$GLOBALS['optionsName'] = "eform"; //name of pseudo attribute used for format settings
function eForm($modx,$params) {
global $_lang;
global $debugText;
global $formats,$fields;

	extract($params,EXTR_SKIP); // extract params into variables

	#include language files
	include_once($snipPath."eform/lang/english.inc.php");
	$form_language = isset($language)?$language:$modx->config['manager_language'];
	if($form_language!="english" && $form_language!='') {
		include_once $snipPath ."eform/lang/".$form_language.".inc.php";
	}

	# check for valid form key - moved to below fetching form template to allow id coming from form template

	# activate nomail if missing $to
	if (!$to) $nomail = 1;

	# load templates
	if($tpl==$modx->documentIdentifier) return $_lang['ef_is_own_id']."'$tpl'";
	//required
	if( $tmp=efLoadTemplate($tpl) ) $tpl=$tmp; else return $_lang['ef_no_doc'] . " '$tpl'";

	# check for valid form key
	if ($formid=="") return $_lang['ef_error_formid'];

	//get formid from <form> tag id
	preg_match('/<form[^>]*?id=[\'"]([^\'"]*?)[\'"]/i',$tpl,$matches);
	$validFormId = isset($matches[1])? ($formid==$matches[1]):false;
	//for backward compatibility
	if( !$validFormId && isset($_POST['formid']) ) $validFormId = ($formid==$_POST['formid']);

	# check if postback mode
	$isPostBack	= ($validFormId && count($_POST)>0)? true:false;

	# add debug warning
	if( $isDebug ) $debugText = $_lang['ef_debug_warning'];

	if ($isPostBack) {

		$report = (($tmp=efLoadTemplate($report))!==false)?$tmp:$_lang['ef_no_report'] . " '$report'";
		$thankyou = (($tmp=efLoadTemplate($thankyou))!==false )?$tmp:$_lang['ef_no_thankyou'] . " '$thankyou'";
		$autotext = (($tmp=efLoadTemplate($autotext))!==false )?$tmp:$_lang['ef_no_autotext'] . " '$autotext'";

		# parse form for formats and generate placeholders
		$tpl = eFormParseTemplate($tpl,$isDebug);
		foreach($formats as $k => $discard)	$fields[$k] = ""; // store dummy value inside $fields

		//added in 1.4 - add a disclaimer from chunk/page id
		if(($tmp=efLoadTemplate($disclaimer))!==false ) $fields['disclaimer'] = $tmp;

		//error message containers
		$vMsg = $rMsg = $rClass = array();

		# get user post back data
		foreach($_POST as $name => $value){
			if(is_array($value)){
				//remove empty values
				$value = array_flip($value);
				unset($value['']);
				$fields[$name] = array_flip($value);
			} else
				$fields[$name]	= stripslashes(($allowhtml || $formats[$name][2]=='html')? $value:$modx->stripTags($value));
		}

		# get uploaded files
		foreach($_FILES as $name => $value){
			$fields[$name] = $value;
		}

		# check vericode
		if($vericode) {
			//add support for captcha code - thanks to Djamoer
			$code = $_SESSION['veriword'] ? $_SESSION['veriword'] : $_SESSION['eForm.VeriCode'];
			if($fields['vericode']!=$code) {
				$vMsg[count($vMsg)]=$_lang['ef_failed_vericode'];
			}
		}

		# validate fields
		foreach($fields as $name => $value) {
			$fld = $formats[$name];
			if ($fld) {
				$desc		= $fld[1];
				$datatype 	= $fld[2];
				$isRequired = $fld[3];
// mod by JJ - Separated required test field from other validation as it
// is the same for each field any field anyway (except 'file')
// isRequired now sets class var and populates extra $rMsg
// class stuff not yet implemented here (although I do have a working version somewhere)
// basic idea is to highlight fields with errors through css
				if ($isRequired==1 && $value=="" && $datatype!="file"){
					$rMsg[count($rMsg)]="$desc";
					$rClass[$name]=$requiredClass; //not used yet
//mod by JJ - extended field validation - see validation functions elsewhere
				}elseif( isset($fld[5]) && $value!="" && $datatype!="file" ) {
					$value = validateField($value,$fld,$vMsg,$isDebug);
					//if returned value is not of type boolean replace value...
					if($value!==false && $value!==true) $fields[$name]=$value; //replace value.
//end mod
				}else{
					switch ($datatype){
						case "integer":
						case "float":
							if (!is_numeric($value)) $vMsg[count($vMsg)]=$desc . $_lang["ef_invalid_number"];
							break;
						case "date":
							if (strtotime($value)===-1) $vMsg[count($vMsg)]=$desc . $_lang["ef_invalid_date"];
							break;
						case "email":
							//stricter email validation
							if (strlen($value)>0 &&  !preg_match(
							'/^(?:[a-z0-9_-]+?\.)*?[a-z0-9_-]+?@(?:[a-z0-9_-]+?\.)*?[a-z0-9_-]+?\.[a-z0-9]{2,5}$/i', $value) ) $vMsg[count($vMsg)]=$desc . $_lang["ef_invalid_email"];
							break;
						case "file":
							if ($_FILES[$name]['error']==1 || $_FILES[$name]['error']==2) $vMsg[count($vMsg)]=$desc . $_lang['ef_upload_exceeded'];
							else if ($isRequired==1 && ($_FILES[$name] && $_FILES[$name]['type']=='')) $rMsg[count($rMsg)]=$desc;
							else if ($_FILES[$name]['tmp_name']){
								if( substr($fld[5],0,5)!="#LIST" || validateField($_FILES[$name]['name'],$fld,$vMsg,$isDebug) );
									$attachments[count($attachments)] = $_FILES[$name]['tmp_name'];
							}
							break;
						case "html":
						case "checkbox":
						case "string":
						default:
							break;
					}
				}//end required test
			}
		}

//mod by JJ - print out required message once with list of fields
		if(count($vMsg)>0 || count($rMsg)>0){
			//add debugging info to fields array
			if($isDebug){
				$debugText .= "<pre>eForm configuration:\n". var_export($params,true).'</pre>';
				//$debugText .= "<pre>Field values:\n". var_export($fields,true).'</pre>';
				//$debugText .= "<pre>Format values:\n". var_export($formats,true).'</pre>';
				$fields['debug']=$debugText;
			}
			# set validation error message
			$fields['validationmessage'] .= $_lang['ef_validation_message'];
			$fields['validationmessage'] .=(count($rMsg)>0)?str_replace("{fields}", implode(", ",$rMsg),$_lang['ef_required_message']):"";
			$fields['validationmessage'] .= implode("<br />",$vMsg);
		}
		else {
			# format report fields
			foreach($fields as $name => $value) {
				$fld = $formats[$name];
				if ($fld) {
					$datatype = $fld[2];
					switch ($datatype)  {

						case "integer":
							$value = number_format($value);
							break;
						case "float":
							$value = number_format($value, 2, '.', ',');
							break;
						case "date":
							$value = ($value)? strftime("%d-%b-%Y %H:%M:%S",strtotime($value)):"";
							$value=str_replace("00:00:00","",$value);// remove trailing zero time values
							break;
						case "html":
							// convert \n to <br>
							$value = str_replace("\n","<br />",$value);
							break;
						case "file":
							// set file name
							if($value['type']!="" && $value['type']!=""){
								$value = $value["name"];
								$patharray = explode(((strpos($value,"/")===false)? "\\":"/"), $value);
								$value = $patharray[count($patharray)-1];
							}
							else {
								$value = "";
							}
							break;
					}
					$fields[$name] = $value;
				}
			}
			# set postdate
			$fields['postdate'] = strftime("%d-%b-%Y %H:%M:%S",time());;

			//check against email injection and replace suspect content
			if( hasMailHeaders($fields) ){

				//send email to webmaster??
/* not tested yet ?
				if ($reportAbuse){ //set in snippet configuration tab
					$body = $_lang['ef_mail_abuse_message'];
					$body .="<table>";
					foreach($fields as $key => $value)
						$body .= "<tr><td>$key</td><td><pre>$value</pre></td></tr>";
					$body .="</table>";
					include_once "manager/includes/controls/class.phpmailer.php";
				# send abuse alert
					$mail = new PHPMailer();
					$mail->IsMail();
					$mail->IsHTML(true);
					$mail->From		= $modx->config['emailsender'];
					$mail->FromName	= $modx->config['site_name'];
					$mail->Subject	= $_lang['ef_mail_abuse_subject'];
					$mail->Body		= $body;
					AddAddressToMailer($mail,"to",$modx->config['emailsender']);
					$mail->send(); //ignore mail errors in this case
				}
//*/
				//return empty form with error message
				return formMerge($tpl,array('validationmessage'=> $_lang['ef_mail_abuse_error']));
			}

/*
* mod - by JJ removed reference from function call as it's deprecated in current PHP
* Remember to treat parameter as a reference in function
*/
			# invoke OnBeforeMailSent event set by another script
			if ($eFormOnBeforeMailSent) {
				if( $isDebug && !function_exists($eFormOnBeforeMailSent))
					$fields['debug'] .= "eFormOnBeforeMailSent event: Could not find the function " . $eFormOnBeforeMailSent;
				else
					if ($eFormOnBeforeMailSent($fields)===false) return;
			}

			$subject	= ($subject)? formMerge($subject,$fields):"$category";
			$fields['subject'] = $subject; //make subject available in report & thank you page
			$keywords	= ($keywords)? formMerge($keywords,$fields):"";
			$from = ($from)? formMerge($from,$fields):"";
			$fromname	= ($from)? formMerge($fromname,$fields):"";

			if(!$nomail){
				# check for mail selector field - select an email from to list
				if ($mselector && $fields[$mselector]) {
					$i = (int)$fields[$mselector];
					$ar = explode(",",$to);
					if ($i>0) $i--;
					if ($ar[$i]) $to = $ar[$i];
					else $to = $ar[0];
				}

				//set reply-to address
				//$replyto snippet parameter must contain email or fieldname
				if(!strstr($replyto,'@'))
					$replyto = ( $fields[$replyto] && strstr($fields[$replyto],'@') )?$fields[$replyto]:$from;

				# include PHP Mailer
				include_once "manager/includes/controls/class.phpmailer.php";

				# send form
				if(!$noemail) {
					if($sendirect) $to = $fields['email'];
					$mail = new PHPMailer();
					$mail->IsMail();
					$mail->IsHTML(true);
					$mail->From		= $from;
					$mail->FromName	= $fromname;
					$mail->Subject	= $subject;
					$mail->Body		= $report;
					AddAddressToMailer($mail,"replyto",$replyto);
					AddAddressToMailer($mail,"to",$to);
					AddAddressToMailer($mail,"cc",$cc);
					AddAddressToMailer($mail,"bcc",$bcc);
					AttachFilesToMailer($mail,$attachments);
					if(!$mail->send()) return $mail->ErrorInfo;
				}

				# send user a copy of the report
				if($ccsender && $fields['email']) {
					$mail = new PHPMailer();
					$mail->IsMail();
					$mail->IsHTML(true);
					$mail->From		= $from;
					$mail->FromName	= $fromname;
					$mail->Subject	= $subject;
					$mail->Body		= $report;
					AddAddressToMailer($mail,"to",$fields['email']);
					AttachFilesToMailer($mail,$attachments);
					if(!$mail->send()) return $mail->ErrorInfo;
				}

				# send auto-respond email
				if ($autotext && $fields['email']!='') {
					$autotext = formMerge($autotext,$fields);
					$mail = new PHPMailer();
					$mail->IsMail();
					$mail->IsHTML(true);
					$mail->From		= ($autosender)? $autosender:$from;
					$mail->FromName	= $fromname;
					$mail->Subject	= $subject;
					$mail->Body		= $autotext;
					AddAddressToMailer($mail,"to",$fields['email']);
					if(!$mail->send()) return $mail->ErrorInfo;
				}

				# send mobile email
				if ($mobile && $mobiletext) {
					$mobiletext = formMerge($mobiletext,$fields);
					$mail = new PHPMailer();
					$mail->IsMail();
					$mail->IsHTML(false);
					$mail->From		= $from;
					$mail->FromName	= $fromname;
					$mail->Subject	= $subject;
					$mail->Body		= $mobiletext;
					AddAddressToMailer($mail,"to",$mobile);
					$mail->send();
				}
/*
* mod - by JJ removed reference from function call as it's deprecated in current PHP
* Remember to treat parameter as a reference in function!!
*/
				# invoke OnMailSent event set by another script
				if ($eFormOnMailSent) {
					if( $isDebug && !function_exists($eFormOnMailSent) )
						$fields['debug'] .= "eFormOnMailSent event: Could not find the function" . $eFormOnMailSent;
					else
						if ($eFormOnMailSent($fields)===false) return;
				}

			}//end test nomail

			if($isDebug){
				$debugText .="<strong>Mail Headers:</strong><br>From: $from ($fromname)<br/>Reply-to:$replyto<br />To: $to<br/>Subject: $subject<br />CC: $cc<br /> BCC;$bcc<br />";
				$fields['debug'] = $debugText;
			}

			# show or redirect to thank you page
			if ($gid==$modx->documentIdentifier){
//mod by JJ - added thank you chunk output
				if(!empty($thankyou) ){
					$thankyou = formMerge($thankyou,$fields);
					return $thankyou;
				}else{
					return $_lang['ef_thankyou_message'];
				}
			}
			else {
				$url = $modx->makeURL($gid);
				$modx->sendRedirect($url);
			}
			return; // stop here
		}
	}

	// set vericode
	if($vericode) {
		$_SESSION['eForm.VeriCode'] = $fields['vericode'] = substr(uniqid(''),-5);
		$fields['verimageurl'] = $modx->config['base_url'].'manager/includes/veriword.php?rand='.rand();
	}

	# build form
	return formMerge($tpl,$fields);
}

# Form Merge
function formMerge($docText, $docFields) {
	global $formats;
	$lastitems;
	if(!docText) return '';
	preg_match_all('~\[\+(.*?)\+\]~', $docText, $matches);
	for($i=0;$i<count($matches[1]);$i++) {
		$name = $matches[1][$i];
//mod - splitting name:value to get proper docFields key
		list($listName,$listValue) = explode(":",$name);
		$value = isset($docFields[$listName])? $docFields[$listName]:"";
//end mod

// support for multi checkbox, radio and select - Djamoer
		if(is_array($value)) $value=implode(', ', $value);
		$fld = $formats[$name];
		if (!isset($fld)){
			// listbox, checkbox, radio select
			$colonPost = strpos($name, ':');
			$listName = substr($name, 0, $colonPost);
			$listValue = substr($name, $colonPost+1);
			$datatype = $formats[$listName][2];
			if(is_array($docFields[$listName])) {
				if($datatype=="listbox" && in_array($listValue, $docFields[$listName])) $docText = str_replace("[+$listName:$listValue+]","selected='selected'",$docText);
				if(($datatype=="checkbox"||$datatype=="radio") && in_array($listValue, $docFields[$listName])) $docText = str_replace("[+$listName:$listValue+]","checked='checked'",$docText);
			}
			else {
				if($datatype=="listbox" && $listValue==$docFields[$listName]) $docText = str_replace("[+$listName:$listValue+]","selected='selected'",$docText);
				if(($datatype=="checkbox"||$datatype=="radio") && $listValue==$docFields[$listName]) $docText = str_replace("[+$listName:$listValue+]","checked='checked'",$docText);
			}
		}
		if(strpos($name,":")===false) $docText = str_replace("[+$name+]",$value,$docText);
		else {
			// this might be a listbox item.
			// we'll remove this field later
			$lastitems[count($lastitems)]="[+$name+]";
		}
	}
	$docText = str_replace($lastitems,"",$docText);
	return $docText;
}

# Adds Addresses to Mailer
function AddAddressToMailer(&$mail,$type,$addr){
	$a = explode(",",$addr);
	for($i=0;$i<count($a);$i++){
		if(!empty($a[$i])) {
			if ($type=="to") $mail->AddAddress($a[$i]);
			elseif ($type=="cc") $mail->AddCC($a[$i]);
			elseif ($type=="bcc") $mail->AddBCC($a[$i]);
		}
	}

}

# Attach Files to Mailer
function AttachFilesToMailer(&$mail,&$attachFiles) {
	if(count($attachFiles)>0){
		foreach($attachFiles as $attachFile){
			if(!file_exists($attachFile)) continue;
			$FileName = $attachFile;
			$contentType = "application/octetstream";
			if (is_uploaded_file($attachFile)){
				foreach($_FILES as $n => $v){
					if($_FILES[$n]['tmp_name']==$attachFile) {
						$FileName = $_FILES[$n]['name'];
						$contentType = $_FILES[$n]['type'];
					}
				}
			}
			$patharray = explode(((strpos($FileName,"/")===false)? "\\":"/"), $FileName);
			$FileName = $patharray[count($patharray)-1];
			$mail->AddAttachment($attachFile,$FileName,"base64",$contentType);
		}
	}
}
/*--- Form Parser stuff----------------------*/
function  eFormParseTemplate($tpl, $isDebug=false ){
	global $formats,$optionsName,$_lang,$debugText;

	$formats =""; //clear formats so values don't persist through multiple snippet calls
	$labels = "";

	# check if postback mode
	$isPostBack	= (count($_POST)>0)? 1:0;

	//added for 1.4 - retrieve all labels (with for="..")
	$regExpr = "#<label([a-z_09 ='\"]*?for=['\"](.*?)['\"])>(.*?)</label>#si";
	preg_match_all($regExpr,$tpl,$matches);
	foreach($matches[2] as $key => $fldName)
		$labels[$fldName]=strip_tags($matches[3][$key]);

	//retrieve all the form fields
	$regExpr = "#(<(input|select|textarea)[^>]*?>)#si";
	preg_match_all($regExpr,$tpl,$matches);

	$fieldTypes = $matches[2];
	$fieldTags = $matches[1];

	for($i=0;$i<count($fieldTypes);$i++){
		$type = $fieldTypes[$i];

		//get array of html attributes
		$tagAttributes = attr2array($fieldTags[$i]);
		//attribute values are stored including quotes
		//strip quotes as well as any brackets to get the raw name
		$name = str_replace(array("'",'"','[',']'),'',$tagAttributes['name']);
		#skip vericode field
		if($name=="vericode") continue;
		//store the field options
		if (isset($tagAttributes[$optionsName])){
			//split to max of 5 so validation rule can contain ':'
			$formats[$name] = explode(":",stripTagQuotes($tagAttributes[$optionsName]),5) ;
			array_unshift($formats[$name],$name);
		}else{
			if(!isset($formats[$name])) $formats[$name]=array($name,'','',0);
		}
		//added for 1.4 - use label if it is defined
		if(empty($formats[$name][1]))
			$formats[$name][1]=(isset($labels[$name])) ? $labels[$name] : $name;

		unset($tagAttributes[$optionsName]);

		switch($type){
			case "select":
				//replace with 'cleaned' tag and added placeholder
				$newTag = buildTagPlaceholder('select',$tagAttributes,$name);
				$tpl = str_replace($fieldTags[$i],$newTag,$tpl);
				if($formats[$name]) $formats[$name][2]='listbox';

				//Get the whole select block with option tags
				$regExp = "#<select .*?name=".$tagAttributes['name']."[^>]*?".">(.*?)</select>#si";
				preg_match($regExp,$tpl,$matches);
				$optionTags = $matches[1];

				$select = $newSelect = $matches[0];
				//get separate option tags and split them up
				preg_match_all("#(<option [^>]*?>)#si",$optionTags,$matches);
				$validValues = array();
				foreach($matches[1] as $option){
					$attr = attr2array($option);
					$value = substr($attr['value'],1,-1); //strip outer quotes
					if($value) $validValues[] = $value; //strip outer quotes
					$newTag = buildTagPlaceholder('option',$attr,$name);
					$newSelect = str_replace($option,$newTag,$newSelect);
				}
				//replace complete select block
				$tpl = str_replace($select,$newSelect,$tpl);
				//add valid values to formats... (extension to $formats)
				if($formats[$name] && !$formats[$name][5])
					$formats[$name][4] = $_lang['ef_failed_default'];
					$formats[$name][5]= "#LIST " . implode(",",$validValues);
				break;

			case "textarea":
				$newTag = buildTagPlaceholder($type,$tagAttributes,$name);
				$regExp = "#<textarea [^>]*?name=" . $tagAttributes["name"] . "[^>]*?" . ">(.*?)</textarea>#si";
				preg_match($regExp,$tpl,$matches);
				//if nothing Posted retain the content between start/end tags
				$placeholderValue = ($isPostBack)?"[+$name+]":$matches[1];

				$tpl = str_replace($matches[0],$newTag.$placeholderValue."</textarea>",$tpl);
				break;
			default: //all the rest, ie. "input"
				$newTag = buildTagPlaceholder($type,$tagAttributes,$name);
				  $fieldType = stripTagQuotes($tagAttributes['type']);
					if($formats[$name] && !$formats[$name][2]) $formats[$name][2]=($fieldType=='text')?"string":$fieldType;
					//populate automatic validation values for hidden, checbox and radio fields
					if($fieldType=='hidden'){
						if(!$isDebug) $formats[$name][1] = "[undefined]"; //do not want to disclose hidden field names
						if(!isset($formats[$name][4])) $formats[$name][4]= $_lang['ef_tamper_attempt'];
						if(!isset($formats[$name][5])) $formats[$name][5]= "#VALUE ". stripTagQuotes($tagAttributes['value']);
					}elseif($fieldType=='checkbox' || $fieldType=='radio'){
						$formats[$name][4]= $_lang['ef_failed_default'];
						$formats[$name][5] .= isset($formats[$name][5])?",":"#LIST ";
						$formats[$name][5] .= stripTagQuotes($tagAttributes['value']);
					}
					/* //not tested yet!! do all browsers include mime-type when uploading files?
					elseif($fieldType=="file"){
						if( isset($tagAttributes["accept"]) )
							$formats[$name][5] = isset($formats[$name][5])?",":"#LIST ";
							$formats[$name][5] .= stripTagQuotes($tagAttributes['accept']);
						$formats[$name][4]= $_lang['ef_failed_filetype'];
					}
* 				//*/
				$tpl = str_replace($fieldTags[$i],$newTag,$tpl);
				break;
		}
	}
	if ($isDebug) $tpl .= "<div class=\"ef_debug-text\">[+debugText+]</div>";
	return $tpl;
}

function stripTagQuotes($value){
	return substr($value,1,-1);
}

function buildTagPlaceholder($tag,$attributes,$name){
	$type = stripTagQuotes($attributes["type"]);
	$quotedValue = $attributes['value'];
	$val = stripTagQuotes($quotedValue);

	foreach ($attributes as $k => $v)
			$t .= ($k!='value' && $k!='checked' && $k!='selected')?" $k=$v":"";
	switch($tag){
		case "select":
			return "<$tag$t>"; //only the start tag mind you
		case "option":
			return "<$tag$t value=".$quotedValue."[+$name:$val+]/>";
		case "input":
			switch($type){
				case 'radio':
				case 'checkbox':
					return "<input$t value=".$quotedValue."[+$name:$val+]/>";
				case 'text':
				case 'password':
					return "<input$t value=\"[+$name+]\"/>";
				default: //leave as is - no placeholder
					return "<input$t value=".$quotedValue."/>";
			}
		case "file": //no placeholder!
		case "textarea": //placeholder needs to be added in calling code
			return "<$tag$t>";
		default:
			return "<input$t value=\"[+$name+]\"/>";
	} // switch
	return ""; //if we've arrived here we're in trouble
}

function attr2array($tag){
	$expr = "#([a-z0-9_]*?)=(([\"'])[^\\3]*?\\3)#si";
	preg_match_all($expr,$tag,$matches);
	foreach($matches[1] as $i => $key)
		$rt[$key]= $matches[2][$i];
	return $rt;
}

/*--- Validation  Code ----------------------*/
/**
 * validateField()
 *
 * @param mixed, $value
 * @param array, $fld
 * @param array, &$vMsg - reference to validation/error messages array
 * @param boolean, $isDebug - Stricter error reporting
 * @return boolean, true or false or $value in case of @FILTER. Test for
 *  type on return! (ie. $return === false) Returned value could be numeric 0!
 **/
function validateField($value,$fld,&$vMsg,$isDebug=false){
	global $modx,$_lang, $debugText,$fields;
	$output = true;
	$desc = $fld[1];
	$fldMsg = trim($fld[4]);
	if(empty($fld[5])) return $output; //corrected in 1.4 - if no rule is given value validates
	list($cmd,$param) = explode(" ",trim($fld[5]),2);
		$cmd = strtoupper(trim($cmd));
	if (substr($cmd,0,1)!='#'){
		$vMsg[count($vMsg)] = "$desc &raquo;" . $_lang['ef_error_validation_rule'];
		return false;
	}

	$v = (is_array($value))?$value:array($value);
	//init vars
	$errMsg='';
	unset($vlist);
	for($i=0;$i<count($v);$i++){
		$value = $v[$i];
		switch ($cmd) {
			//really only used internally for hidden fields
			case "#VALUE":
				if($value!=$param) $errMsg = $_lang['ef_failed_default'];
				break;
			case "#RANGE":
				if(!isset($vlist)) $vlist = explode(',',strtolower(trim($param))); //cached
				//the crude way first - will have to refine this
				foreach($vlist as $p){
					if( strpos($p,'~')>0)
						$range = explode('~',$p);
					else
						$range = array($p,$p); //yes,.. I know - cheating :)

					if($isDebug && (!is_numeric($range[0]) || !is_numeric($range[1])) )
						$modx->messageQuit('Error in validating form field!', '',$false,E_USER_WARNING,__FILE__,'','#RANGE rule contains non-numeric values: '.$fld[5],__LINE__);
					sort($range);
					if( $value>=$range[0] && $value<=$range[1] ) break 2; //valid
				}
				$errMsg = $_lang['ef_failed_range'];
				break;

			case "#LIST":		// List of comma separated values (not case sensitive)
				//added in 1.4 - file upload filetype test
				if($fld[2]=='file')$value = substr($value,strrpos($value,'.')+1); //file extension
				if(!isset($vlist)) $vlist = explode(',',strtolower(trim($param))); //cached
				if( $isDebug && count($vlist)==1 && empty($vlist[0])  ){
					 $modx->messageQuit('Error in validating form field!', '',$false,E_USER_WARNING,__FILE__,'','#LIST rule declared but no list values supplied: '.$fld[5],__LINE__);
					//if debugging bail out big time
				}elseif(!in_array(strtolower($value),$vlist))
					$errMsg = ($fld[2]=='file')? $_lang["ef_failed_upload"]: $_lang['ef_failed_list'];
				break;
			case "#SELECT":	//validates against a list of values from the cms database
				#cache all this
				if( !isset($vlist) ) {
					$rt = array();
					$param = 	str_replace('{DBASE}',$modx->db->config['dbase'],$param);
					$param = 	str_replace('{PREFIX}',$modx->db->config['table_prefix'],$param);
					//added in 1.4
					if( preg_match("/{([^}]*?)\}/",$param,$matches) ){
						$tmp = $modx->db->escape(formMerge('[+'.$matches[1].'+]',$fields));
						$param = str_replace('{'.$matches[1].'}',$tmp,$param);
					}
					$rs = $modx->db->query("SELECT $param;");
					//select value from 1st field in records only (not case sensitive)
					while( $v = $modx->db->getValue($rs) ) $vlist[]=strtolower($v);
				}
				if(!is_array($vlist)){
					//WARNING! if not debugging (and query fails) error is ignored, and value will validate!!
					//version 1.4 - replaced fatal error with friendly debug message when debugging
					$debugText .= ($isDebug)? "'<strong>$fld[1]</strong>' ".$_lang['ef_sql_no_result'].'<br />':'';
				}elseif(!in_array(strtolower($value),$vlist)){
					$errMsg = $_lang['ef_failed_list'];
				}
				break;

			case "#EVAL":	// php code should return true or false
				$tmp = $cmd; //just in case eval destroys cmd
				if( eval($param)===false )
					$errMsg = $_lang['ef_failed_eval'];
				$cmd = $tmp;
				break;
			//added in 1.4
			case "#FUNCTION":
				$tmp = $cmd; //just in case function destroys cmd
				if( function_exists($param) )
					if( !@$param($value) ) $errMsg = $_lang['ef_failed_eval'];
				else
					if($isDebug) $debugText .= "<strong>$fld[1]</strong>: ".$_lang['ef_no_function']." $param";
				$cmd = $tmp;
				break;

			case "#REGEX":
				if( !$tmp=preg_match($param,$value) )
					$errMsg = $_lang['ef_failed_ereg'];
					if($isDebug && $tmp===false ) $debugText .= "<strong>$fld[1]</strong>: ".$_lang['ef_regex_error']." $param";
				break;

			case "#FILTER":
				$v[$i] = filterEformValue($value,$param);
				break;

			default:
				$errMsg = $_lang['ef_error_validation_rule'];

		}//end switch
		if($isDebug) {
			$debugText .="'<strong>$fld[1]</strong>' ";
			$debugText .= (empty($errMsg))?'passed':'<span style="color:red;">Failed</span>';
			$debugText .= " using rule: $cmd ".$param.', (input='.htmlspecialchars($value).")<br />\n";
		}
		if(!empty($errMsg)){
			$errMsg = ($fldMsg)?"$desc &raquo; $fldMsg":"$desc &raquo; $errMsg";
			$vMsg[count($vMsg)] = $errMsg;
			$output=false;
			break; //quit testing more values
		}
	}//end for
	//make sure we have correct return value in case of #filter
	$v = (is_array($value))?implode('',$v):$v;

	return ($cmd=="#FILTER")?$v:$output;
}//end validateField

function filterEformValue($value,$param){
	list($cmd,$param) = explode(" ",trim($param),2);
	$cmd = trim($cmd);

	switch(strtoupper($cmd)){
		case "#REGEX":
			list($src,$dst) = explode("||",$param,2);
			$value = ( $v = preg_replace($src,$dst,$value) )?$v:$value;
			break;

		case "#LIST":
			$param = explode("||",$param,2);
			$src = strpos($param[0],',')?explode(',',$param[0]):$param[0];
			$dst = strpos($param[1],',')?explode(',',$param[1]):$param[1];
			$value = str_replace($src,$dst,$value);
			break;

		case "#EVAL":
			$value = ($v = @eval($param))?$v:$value;
			break;
	}
	return $value;
}
#loads tempate from chunk or document
function efLoadTemplate($tpl){
	global $modx;
	if (strlen($tpl)<50){
		if( is_numeric($tpl) ){
			//try unpublished docs first
			$tpl = ( $doc=$modx->getDocument($tpl,'content',0) )? $doc['content'] : '';
			if(!$tpl) $tpl = ( $doc=$modx->getDocument($tmp,'content',1) )? $doc['content'] : false;
		}else if($tpl)
			$tpl = ( $chunk=$modx->getChunk($tpl) )? $chunk : false;
	}
	return $tpl;
}

/**
* adapted from http://php.mirrors.ilisys.com.au/manual/en/ref.mail.php
* If any field contains newline followed by email-header specific string it is replaced by a harmless substitute
* (we hope) If any subsitutiosn are made the function returns true
* Currently tests for
* 	Content-Transfer-Encoding:
* 	MIME-Version:
* 	content-type:
* 	to:
* 	cc:
* 	bcc:
*/
function hasMailHeaders( &$fields ){
	$injectionAttempt = false;
	$re = "/(%0A|%0D|\n+|\r+)(Content-Transfer-Encoding:|MIME-Version:|content-type:|to:|cc:|bcc:)/i";
	foreach($fields as $fld => $text){
	 	if( ($match = preg_replace($re,'\\[\2]\\', $text))!=$text ){
			$injectionAttempt = true;
			$fields[$fld]=$match; //replace with 'disabled' version
		}
	}
	return ($injectionAttempt)?true:false;
}
?>
