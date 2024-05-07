{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
	<!-- tpl-install-tpl-Header -->
	<!DOCTYPE html>
	<html lang="{$HTMLLANG}">

	<head>
		<title>YetiForce</title>
		<link REL="SHORTCUT ICON" HREF="../{\App\Layout::getImagePath('favicon.ico')}">
		{if !empty($IS_IE)}
			<meta http-equiv="x-ua-compatible" content="IE=11,edge">
		{/if}
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		{foreach key=index item=cssModel from=$STYLES}
			<link rel="{$cssModel->getRel()}" href="../{$cssModel->getHref()}">
		{/foreach}
		{foreach key=index item=jsModel from=$HEADER_SCRIPTS}
			<script type="{$jsModel->getType()}" src="../{$jsModel->getSrc()}"></script>
		{/foreach}
		{* For making pages - print friendly *}
		<style type="text/css">
			@media print {
				.noprint {
					display: none;
				}
			}
		</style>
		<script type="text/javascript">
			var CONFIG = {\App\Config::getJsEnv()};
			var LANG = {\App\Json::encode($LANGUAGE_STRINGS)};
		</script>
	</head>

	<body data-language="{$LANGUAGE}">
		<input type="hidden" id="start_day" value="">
		<input type="hidden" id="row_type" value="">
		<input type="hidden" id="current_user_id" value="">
		<!-- /tpl-install-tpl-Header -->

{/strip}
