<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// extend tt_content for the ratio between text and image
$tmp_column = array(
	'tx_dmfcanonical' => array(
		'exclude' => 1,
		'label' => 'Canonical tag for this page',
		'config' => array(
			'type' => 'group',
			'maxitems' => 1,
			'minitems' => 0,
			'show_thumbs' => 1,
			'size' => 1,
			'allowed' => 'pages',
			'internal_type' => 'db',
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest'
				)
			)
		)
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tmp_column, 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('pages', 'metatags', 'tx_dmfcanonical', '');
