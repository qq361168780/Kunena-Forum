<?php
/**
 * @version		$Id: view.html.php 3901 2010-11-15 14:14:02Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 */
defined ( '_JEXEC' ) or die ();

kimport ( 'kunena.view' );

// FIXME: convert to full MVC

/**
 * User View
 */
class KunenaViewUser extends KunenaView {
	function displayDefault($tpl = null) {
		// TODO: handle redirect to integrated component
		$this->displayCommon($tpl);
	}

	function displayEdit($tpl = null) {
		$this->displayCommon($tpl);
	}

	protected function displayCommon($tpl = null) {
		$userid = JFactory::getUser()->id;

		kimport('kunena.html.parser');
		require_once(KPATH_SITE.'/lib/kunena.timeformat.class.php');
		$this->_db = JFactory::getDBO ();
		$this->_app = JFactory::getApplication ();
		$this->config = KunenaFactory::getConfig ();
		$this->my = JFactory::getUser ();
		$this->do = 'default'; // $do;

		if (!$userid) {
			$this->user = $this->my;
		}
		else {
			$this->user = JFactory::getUser( $userid );
		}
		if ($this->user->id == 0) {
			$this->_app->enqueueMessage ( JText::_('COM_KUNENA_PROFILEPAGE_NOT_ALLOWED_FOR_GUESTS'), 'notice' );
			return;
		}

		$integration = KunenaFactory::getProfile();
		$activityIntegration = KunenaFactory::getActivityIntegration();
		$template = KunenaFactory::getTemplate();
		$this->params = $template->params;

		if (get_class($integration) == 'KunenaProfileNone') {
			$this->_app->enqueueMessage ( JText::_('COM_KUNENA_PROFILE_DISABLED'), 'notice' );
			return;
		}

		$this->allow = true;

		$this->profile = KunenaFactory::getUser ( $this->user->id );
		if ($this->profile->posts === null) {
			$this->profile->save();
		}
		if ($this->profile->userid == $this->my->id) {
			if ($this->do != 'edit') $this->editlink = CKunenaLink::GetMyProfileLink ( $this->profile->userid, JText::_('COM_KUNENA_EDIT'), 'nofollow', 'edit' );
			else $this->editlink = CKunenaLink::GetMyProfileLink ( $this->profile->userid, JText::_('COM_KUNENA_BACK'), 'nofollow' );
		}
		$this->name = $this->user->username;
		if ($this->config->userlist_name) $this->name = $this->user->name . ' (' . $this->name . ')';
		if ($this->config->showuserstats) {
			if ($this->config->userlist_usertype) $this->usertype = $this->user->usertype;
			$this->rank_image = $this->profile->getRank (0, 'image');
			$this->rank_title = $this->profile->getRank (0, 'title');
			$this->posts = $this->profile->posts;
			$this->userpoints = $activityIntegration->getUserPoints($this->profile->userid);
			$this->usermedals = $activityIntegration->getUserMedals($this->profile->userid);
		}
		if ($this->config->userlist_joindate || CKunenaTools::isModerator($this->my->id)) $this->registerdate = $this->user->registerDate;
		if ($this->config->userlist_lastvisitdate || CKunenaTools::isModerator($this->my->id)) $this->lastvisitdate = $this->user->lastvisitDate;
		$this->avatarlink = $this->profile->getAvatarLink('kavatar','profile');
		$this->personalText = $this->profile->personalText;
		$this->signature = $this->profile->signature;
		$this->timezone = $this->user->getParam('timezone', $this->_app->getCfg ( 'offset', 0 ));
		$this->moderator = CKunenaTools::isModerator($this->profile->userid);
		$this->admin = CKunenaTools::isAdmin($this->profile->userid);
		switch ($this->profile->gender) {
			case 1:
				$this->genderclass = 'male';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE');
				break;
			case 2:
				$this->genderclass = 'female';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE');
				break;
			default:
				$this->genderclass = 'unknown';
				$this->gender = JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN');
		}
		if ($this->profile->location)
			$this->locationlink = '<a href="http://maps.google.com?q='.$this->escape($this->profile->location).'" target="_blank">'.$this->escape($this->profile->location).'</a>';
		else
			$this->locationlink = JText::_('COM_KUNENA_LOCATION_UNKNOWN');

		$this->online = $this->profile->isOnline();
		$this->showUnusedSocial = true;

		$avatar = KunenaFactory::getAvatarIntegration();
		$this->editavatar = is_a($avatar, 'KunenaAvatarKunena') ? true : false;

		kimport('kunena.user.ban');
		$this->banInfo = KunenaUserBan::getInstanceByUserid($userid, true);
		$this->canBan = $this->banInfo->canBan();
		if ( $this->config->showbannedreason ) $this->banReason = $this->banInfo->reason_public;

		$user = JFactory::getUser();
		if ($user->id != $this->profile->userid)
		{
			$this->profile->uhits++;
			$this->profile->save();
		}

		parent::display();
	}

	function displayReviewPosts()
	{
		require_once (KUNENA_PATH_LIB . DS . 'kunena.review.php');
		$review = new CKunenaReview();
		$obj->embedded = 1;
		$review->display();
	}

	function displayUserPosts()
	{
		// TODO: pass parameters
		KunenaForum::display('topics', 'posts');
	}

	function displayGotThankYou()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('gotthankyouposts',0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getGotThankYouPosts();
		$obj->displayPosts();
	}

	function displaySaidThankYou()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('saidthankyouposts',0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getSaidThankYouPosts();
		$obj->displayPosts();
	}
	function displayOwnTopics()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('owntopics', 0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getOwnTopics();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayUserTopics()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('usertopics', 0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getUserTopics();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayFavorites()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('favorites', 0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getFavorites();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displaySubscriptions()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('subscriptions', 0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getSubscriptions();
		$obj->displayFlat();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayCategoriesSubscriptions()
	{
		//FIXME
		return;

		require_once (KUNENA_PATH_FUNCS . DS . 'latestx.php');
		$obj = new CKunenaLatestX('catsSubscriptions', 0);
		$obj->user = $this->user;
		$obj->embedded = 1;
		$obj->getCategoriesSubscriptions();
		$obj->displayFlatCats();
		//echo $obj->getPagination ( $obj->func, $obj->show_list_time, $obj->page, $obj->totalpages, 3 );
	}

	function displayBanUser() {
		kimport('kunena.user.ban');
		$this->baninfo = KunenaUserBan::getInstanceByUserid($this->profile->userid, true);
		echo $this->loadTemplate('ban');
	}

	function displayBanHistory() {
		kimport('kunena.user.ban');
		$this->banhistory = KunenaUserBan::getUserHistory($this->profile->userid);
		echo $this->loadTemplate('history');
	}

	function displayBanManager() {
		kimport('kunena.user.ban');
		$this->bannedusers = KunenaUserBan::getBannedUsers();
		echo $this->loadTemplate('banmanager');
	}

	function displaySummary() {
		echo $this->loadTemplate('summary');
	}

	function displayTab() {
		switch ($this->do) {
			case 'edit':
				$user = JFactory::getUser();
				if ($user->id == $this->profile->userid) CKunenaTools::loadTemplate('/profile/edittab.php');
				break;
			default:
				echo $this->loadTemplate('tab');
		}
	}

	function displayKarma() {
		$userkarma = '';
		if ($this->config->showkarma && $this->profile->userid) {
			$userkarma = '<strong>'. JText::_('COM_KUNENA_KARMA') . "</strong>: " . $this->profile->karma;

			if ($this->my->id && $this->my->id != $this->profile->userid) {
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'decrease', '', '', $this->profile->userid, '<span class="kkarma-minus" title="' . JText::_('COM_KUNENA_KARMA_SMITE') . '"> </span>' );
				$userkarma .= ' '.CKunenaLink::GetKarmaLink ( 'increase', '', '', $this->profile->userid, '<span class="kkarma-plus" title="' . JText::_('COM_KUNENA_KARMA_APPLAUD') . '"> </span>' );
			}
		}

		return $userkarma;
	}

	function getAvatarGallery($path) {
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($path,'(\.gif|\.png|\.jpg|\.jpeg)$');
		return $files;
	}

	// This function was modified from the one posted to PHP.net by rockinmusicgv
	// It is available under the readdir() entry in the PHP online manual
	function getAvatarGalleries($path, $select_name) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.utilities.string');
		$folders = JFolder::folders($path,'.',true, true);
		foreach ($folders as $key => $folder) {
			$folder = substr($folder, strlen($path)+1);
			$folders[$key] = $folder;
		}

		$selected = JString::trim($this->gallery);
		$str =  "<select name=\" {$this->escape($select_name)}\" id=\"avatar_category_select\" onchange=\"switch_avatar_category(this.options[this.selectedIndex].value)\">\n";
		$str .=  "<option value=\"default\"";

		if ($selected == "") {
			$str .=  " selected=\"selected\"";
		}

		$str .=  ">" . JText::_ ( 'COM_KUNENA_DEFAULT_GALLERY' ) . "</option>\n";

		asort ( $folders );

		foreach ( $folders as $key => $val ) {
			$str .=  '<option value="' . urlencode($val) . '"';

			if ($selected == $val) {
				$str .=  " selected=\"selected\"";
			}

			$str .=  ">{$this->escape(JString::ucwords(JString::str_ireplace('/', ' / ', $val)))}</option>\n";
		}

		$str .=  "</select>\n";
		return $str;
	}

	function displayEditUser() {
		jimport ( 'joomla.version' );
		$jversion = new JVersion ();

		$this->user = JFactory::getUser();

		// check to see if Frontend User Params have been enabled
		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$check = $usersConfig->get('frontend_userparams');

		if ($check == 1 || $check == NULL) {
			if($this->user->authorize( 'com_user', 'edit' )) {
				if ($jversion->RELEASE == '1.5') {
					$lang = JFactory::getLanguage();
					$lang->load('com_user', JPATH_SITE);
					$params = $this->user->getParameters(true);
					// Legacy template support:
					$this->userparams = $params->renderToArray();
					$i=0;
					// New templates use this:
					foreach ($this->userparams as $userparam) {
						$this->userparameters[$i]->input = $userparam[1];
						$this->userparameters[$i]->label = '<label for="params'.$userparam[5].'" title="'.$userparam[2].'">'.$userparam[0].'</label>';
						$i++;
					}
				} elseif ($jversion->RELEASE == '1.6') {
					$lang = JFactory::getLanguage();
					$lang->load('com_users', JPATH_ADMINISTRATOR);

					jimport( 'joomla.form.form' );
					$form = JForm::getInstance('juserprofilesettings', JPATH_ADMINISTRATOR.'/components/com_users/models/forms/user.xml');
					// this get only the fields for user settings (template, editor, language...)
					$this->userparameters = $form->getFieldset('settings');
				}
			}
		}
		echo $this->loadTemplate('user');
	}

	function displayEditProfile() {
		$bd = @explode("-" , $this->profile->birthdate);

		$this->birthdate["year"] = $bd[0];
		$this->birthdate["month"] = $bd[1];
		$this->birthdate["day"] = $bd[2];

		$this->genders[] = JHTML::_('select.option', '0', JText::_('COM_KUNENA_MYPROFILE_GENDER_UNKNOWN'));
		$this->genders[] = JHTML::_('select.option', '1', JText::_('COM_KUNENA_MYPROFILE_GENDER_MALE'));
		$this->genders[] = JHTML::_('select.option', '2', JText::_('COM_KUNENA_MYPROFILE_GENDER_FEMALE'));

		echo $this->loadTemplate('profile');
	}

	function displayEditAvatar() {
		if (!$this->editavatar) return;
		$this->gallery = JRequest::getVar('gallery', 'default');
		if ($this->gallery == 'default') {
			$this->gallery = '';
		}
		$path = KUNENA_PATH_AVATAR_UPLOADED .'/gallery';
		$this->galleryurl = KUNENA_LIVEUPLOADEDPATH . '/avatars/gallery';
		$this->galleries = $this->getAvatarGalleries($path, 'gallery');
		$this->galleryimg = $this->getAvatarGallery($path . '/' . $this->gallery);
		echo $this->loadTemplate('avatar');
	}

	function displayEditSettings() {
		echo $this->loadTemplate('settings');
	}
}