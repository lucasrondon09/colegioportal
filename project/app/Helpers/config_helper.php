<?php

use App\Models\Admin\RedesSociaisModel;
use App\Models\Admin\GoogleAnalyticsModel;
use App\Models\Admin\MetaTagsModel;

function googleAnalytics()
{

	$modelGoogleAnalytics	= new GoogleAnalyticsModel();

	$googleAnalytics     = $modelGoogleAnalytics->get(1);

	return $googleAnalytics;					
	


}

function redesSociais()
{

	$modelRedesSociais	= new RedesSociaisModel();

	$redesSociais     = $modelRedesSociais->get(1);

	return $redesSociais;					
	


}

function metaTags()
{

	$modelMetaTags	= new MetaTagsModel();

	$metaTags     = $modelMetaTags->get(1);

	return $metaTags;					
	


}