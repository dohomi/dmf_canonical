<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Dominic Garms <http://www.dmfmedia.de>
 *  Inspiration by Georg Ringer (EXT: canonical)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Userfunction 'user_canonical_check' for the 'dmf_canonical' extension.
 * This extension is a fork from extension 'canonical' by Georg Ringer
 *
 * @author     Dominic Garms <http://www.dmfmedia.de>
 * @package    TYPO3
 */
class user_canonical_check {

	/**
	 * Check for duplicate content in cases of MountPoints
	 * and the page settings "Show content of this page"
	 *
	 * @param    string $content : The Plugin content
	 * @param    array $conf : The Plugin configuration
	 *
	 * @return string
	 */
	function checkForCanonicalUrl($content, $conf) {
		// default settings
		$link = '';

		$typoScriptService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
		$options = $typoScriptService->convertTypoScriptArrayToPlainArray(
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_dmfcanonical.']['settings.']
		);

		// Check for Mountpoints
		if ($options['checkMP'] == 1) {
			$link = $this->checkMountPoints();
		}

		// Check for "Show content of this page"		 
		if ($link == '' && $options['checkContentOfPage'] == 1) {
			$link = $this->checkContentOfThisPage();
		}

		// Check tt_news for backPid
		if ($link == '' && $options['checkTTnews'] == 1) {
			$link = $this->checkNewsBackPid();
		}

		// Check for extension GET parameter with key and detail pid
		if ($options['checkExtensions'] == 1) {
			$extensionLink = $this->checkExtensionDetailPage($options['extensions']);
			if ($extensionLink) {
				$link = $extensionLink;
			}
		}

		// create the canonical tag
		if ($link != '') {
			// output: <link rel="canonical" href="'.$link.'" />
			$tag = $this->cObj->stdWrap($link, $conf['link.']);

			if ($conf['debug'] == 1) {
				echo htmlspecialchars($tag);
			}

			return $tag;
		} else {
			// if all pages enable link to current page
			if ($options['enableAllPages'] == 1) {
				$link = $this->linkToCurrentPage();
				$link = $this->cObj->stdWrap($link, $conf['link.']);
				if ($conf['debug'] == 1) {
					echo htmlspecialchars($link);
				}
			}

			return $link;
		}
	}

	function linkToCurrentPage() {
		$linkConf = array(
			'parameter' => intval($GLOBALS['TSFE']->id),
			'forceAbsoluteUrl' => 1,
			'returnLast' => 'url',
			'htmlSpecialChars' => 1,
			'addQueryString' => 0
		);
		$link = $this->cObj->typolink('', $linkConf);

		return $link;
	}

	/**
	 * @param $conf
	 *
	 * @author Dominic Garms (modified)
	 *
	 * @return string
	 */
	function checkExtensionDetailPage($conf) {


		$link = '';
		foreach ($conf as $key => $value) {
			$extensionKey = $value['key'];
			$getParam = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($key);
			if ($getParam && $getParam[$extensionKey]) {
				$linkConf = array();

				$id = $value['pid'];
				// generate the link
				if ($id) {
					$linkConf['parameter'] = $id;
					$linkConf['returnLast'] = 'url';
					$linkConf['addQueryString'] = 1;
					$linkConf['addQueryString.method'] = 'GET';
					$linkConf['addQueryString.']['exclude'] = 'id,MP';

					$link = $this->cObj->typolink('', $linkConf);
				}
			}
		}

		return $link;

	}


	/**
	 * Check for used MountPoints
	 * @author Georg Ringer (initial)
	 * @author Dominic Garms (modified)
	 *
	 *
	 * @return string
	 */
	function checkMountPoints() {
		$link = '';
		$vars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('MP');
		$mpdisable = $GLOBALS['TSFE']->config['config']['MP_disableTypolinkClosestMPvalue']; // default value

		// if there is a mountpoint get vars
		if ($vars != '') {
			// build the link
			$linkConf = array();
			$linkConf['returnLast'] = 'url';
			$linkConf['parameter'] = $GLOBALS['TSFE']->id;
			$linkConf['addQueryString'] = 1;
			$linkConf['addQueryString.']['exclude'] = 'id,MP';

			// disable the mountpoint rendering
			$GLOBALS['TSFE']->config['config']['MP_disableTypolinkClosestMPvalue'] = 1;

			// generate the link
			$link = $this->cObj->typolink('', $linkConf);

			// enable mountpoint rendering
			$GLOBALS['TSFE']->config['config']['MP_disableTypolinkClosestMPvalue'] = $mpdisable;
		}

		return $link;
	}


	/**
	 * Check for "Show content of this page"
	 * @author Georg Ringer (initial)
	 * @author Dominic Garms (modified)
	 *
	 * @return string
	 */
	function checkContentOfThisPage() {
		$link = '';

		// get the field which holds the page id with the content to show
		$id = $this->cObj->data['content_from_pid'];

		// generate the link
		if ($id > 0) {
			$linkConf = array();
			$linkConf['parameter'] = $id;
			$linkConf['returnLast'] = 'url';
			$linkConf['addQueryString'] = 1;
			$linkConf['addQueryString.method'] = 'GET';


			$link = $this->cObj->typolink('', $linkConf);
		}

		return $link;
	}


	/**
	 * Check for tt_news backPid and create a link to the record without backpid
	 * Important: Doesn't help with indexed_search or bigger cache tables!
	 * @author Georg Ringer (initial)
	 * @author Dominic Garms (modified)
	 *
	 * @return string
	 */
	function checkNewsBackPid() {
		$link = '';

		// news params
		$vars = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_ttnews');

		// if this is a news single record with a backpid set
		if (intval($vars['tt_news']) > 0 && intval($vars['backPid']) > 0) {
			$found = TRUE;

			// build the link without backpid
			$linkConf = array();
			$linkConf['returnLast'] = 'url';
			$linkConf['parameter'] = $GLOBALS['TSFE']->id;
			$linkConf['addQueryString'] = 1;
			$linkConf['addQueryString.']['exclude'] = 'id,tx_ttnews[backPid]';

			$link = $this->cObj->typolink('', $linkConf);
		}

		return $link;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dmf_canonical/class.user_canonical_check.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dmf_canonical/class.user_canonical_check.php']);
}

?>
