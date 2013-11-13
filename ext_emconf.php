<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "canonical".
 *
 * Auto generated 18-06-2013 08:53
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title'              => 'Add canonical tag on pages where duplicated content is possible.',
	'description'        => 'Ads canonical tag for detail pid extensions, mountpoints, "Show content of this page" and backPid of tt_news.',
	'category'           => 'fe',
	'shy'                => 0,
	'version'            => '0.0.1',
	'dependencies'       => '',
	'conflicts'          => '',
	'priority'           => '',
	'loadOrder'          => '',
	'module'             => '',
	'state'              => 'alpha',
	'uploadfolder'       => 0,
	'createDirs'         => '',
	'modify_tables'      => '',
	'clearcacheonload'   => 0,
	'lockType'           => '',
	'author'             => 'Dominic Garms',
	'author_email'       => 'djgarms@gmail.com',
	'author_company'     => 'DMFmedia GmbH',
	'CGLcompliance'      => NULL,
	'CGLcompliance_note' => NULL,
	'constraints'        =>
	array(
		'depends'   =>
		array(
			'typo3' => '6.0',
			'extbase' => '6.0',
		),
		'conflicts' => '',
		'suggests'  =>
		array(),
	),
);

?>