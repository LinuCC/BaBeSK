<?php

class AdminAdminInterface extends AdminInterface{
	function __construct() {
		parent::__construct();
		$this->PTPL = PATH_SMARTY_ADMIN_MOD.'/mod_admin/';
		$this->MOD_HEADING = $this->PTPL.'mod_admin_header.tpl';
		$this->smarty->assign('adminParent', $this->MOD_HEADING);
	}
	
	function CreateAdmin($admin_group_arr) {
		$this->smarty->assign('admin_groups', $admin_group_arr);
		$this->smarty->display($this->PTPL.'addAdmin.tpl');
	}
	
	function CreateAdminGroup($mod_array) {
		$this->smarty->assign('modules', $mod_array);
		$this->smarty->display($this->PTPL.'addAdminGroup.tpl');
	}
	
	function ShowAdmin($admins) {
		$this->smarty->assign('admins', $admins);
		$this->smarty->display($this->PTPL.'show_admins.tpl');
	}
	
	function ShowAdminGroup($admingroups) {
		$this->smarty->assign('admingroups', $admingroups);
		$this->smarty->display($this->PTPL.'show_admin_groups.tpl');
	}
	
	function CreateAdminFin($adminname, $str_admingroup) {
		$this->smarty->assign('adminname', $adminname);
		$this->smarty->assign('admingroup', $str_admingroup);
		$this->smarty->display($this->PTPL.'create_admin_fin.tpl');
	}
	
	function ChangeAdmin($ID, $name, $groups) {
		$this->smarty->assign('ID', $ID);
		$this->smarty->assign('name', $name);
		$this->smarty->assign('groups', $groups);
		$this->smarty->display($this->PTPL.'change_admin.tpl');
	}
	
	function ChangeAdminFin($ID, $name, $GID) {
		$this->smarty->assign('ID', $ID);
		$this->smarty->assign('name', $name);
		$this->smarty->assign('GID', $GID);
		$this->smarty->display($this->PTPL.'change_admin_fin.tpl');
	}
	
	function ConfirmDeleteAdmin($ID, $adminname) {
		$this->smarty->assign('ID', $ID);
		$this->smarty->assign('name', $adminname);
		$this->smarty->display($this->PTPL.'confirm_delete_admin.tpl');
	}
	
	
	function ConfirmDeleteAdminGroup($ID, $admingroup_name) {
		$this->smarty->assign('name', $admingroup_name);
		$this->smarty->assign('ID', $ID);
		$this->smarty->display($this->PTPL.'confirm_delete_admingroup.tpl');
	}
	
	function ConfirmAddAdmingroup($admingroup_name) {
		
		$this->smarty->assign('name', $admingroup_name);
		$this->smarty->display($this->PTPL.'confirm_add_admingroup.tpl');
	}
	
	
	function SelectionMenu($arr_action) {
		$this->smarty->assign('action', $arr_action);
		$this->smarty->display($this->PTPL.'index.tpl');
	}
	/**
	 * The Path to the Smarty-Templates of the AdminInterface
	 * Enter description here ...
	 * @var string
	 */
	private $PTPL;
	
	/**
	 * The Path to the heading of the module (all template-files of this module should inherit from this)
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $MOD_HEADING;
}

?>