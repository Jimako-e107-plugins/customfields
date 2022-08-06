<?php

//v2.x Standard for extending admin areas.

class customfields_admin implements e_admin_addon_interface
{
    /**
     * Populate custom field values.
     * Integrate e_addon data into the list model. !not edit.
     *
     * @param string $event
     * @param string $ids
     *
     * @return array
     */
    public function load($event, $ids)
    {
        $controller = e107::getAdminUI()->getController();

        $cf_config = e107::getDb()->retrieve('customfields', '*', "plugin_event='".$event."'  LIMIT 1 ");
        $plugin_table = $cf_config['plugin_table'];

        $data = e107::getDb()->retrieve('datafields', 'plugin_id, data_fields', "
		`plugin_table` LIKE '".$plugin_table."' AND `plugin_id` IN(".$ids.')', true);

        foreach ($data as $row) {
            $id = (int) $row['plugin_id'];
            $ret[$id]['data_fields'] = $row['data_fields'];
        }

        return $ret;
    }

    /**
     * Extend Admin-ui Configuration Parameters eg. Fields etc.
     *
     * @param $ui admin-ui object
     *
     * @return array
     */
    public function config(e_admin_ui $ui)
    {
        $action = $ui->getAction(); // current mode: create, edit, list
        $type = $ui->getEventName(); // 'wmessage', 'news' etc. (core or plugin)
        $id = $ui->getId();
        $table = $ui->getTableName();

        $config = [];

        //needed for plugin without tabs, see featurebox
		$tabs = $ui->getTabs();
        if (empty($tabs)) {
            $tabs = ['Data'];
            $tabs = $ui->addTab(0, 'Data');
        }
	
		//$tab_id = 99; //not display field itself, find another way 

		//if ($type == 'news') {
		if(true)
		{
        	$tab_id = e107::getCustomFields()->getTabId();
		}

		//get config
        $cf_config = e107::getDb()->retrieve('customfields', '*', "plugin_event='".$type."'  LIMIT 1 ");

        if ($cf_config) {
            $config['fields']['data_fields'] = ['title' => 'Custom Fields', 'writeParms' => array("nolabel"=>true),
            'tab' => $tab_id, 'type' => 'method', 'data' => 'json', 'width' => 'auto', ];

            e107::getCustomFields()->loadConfig($cf_config['config_fields']);

            e107::getCustomFields()->setAdminUIConfig('x_customfields_data_fields', $ui);

            /* because newspost.php and ukfield sort - otherwise there is php error of missing key */
            /* see #4842 */
            if ($type == 'news') {
                $tmp = e107::getCustomFields()->getConfig();

                foreach ($tmp as $key => $item) {
                    $newkey = 'data_fields__'.$key;
                    $config['fields'][$newkey] = $item;
                }
            } 
			/* using this for another plugin doesn't display CF */ 
			/* config fields are reordered alphabeticaly  Why? */
        }

		return $config;
         
    }

    /**
     * Process Posted Data.
     *
     * @param object    $ui admin-ui
     * @param int|array $id - Primary ID of the record being created/edited/deleted or array data of a batch process
     */
    public function process(e_admin_ui $ui, $id = null)
    {
        $data = $ui->getPosted(); // ie $_POST field-data
        $type = $ui->getEventName(); // eg. 'news'
        $action = $ui->getAction(); // current mode: create, edit, list, batch
        $changed = $ui->getModel()->dataHasChanged(); // true when data has changed from what is in the DB.

        switch ($action) {
            case 'create':
            case 'edit':
                $cf_config = e107::getDb()->retrieve('customfields', '*', "plugin_event='".$type."'  LIMIT 1 ");

                if (!empty($id) && $cf_config) {
                    $new_data = e107::getCustomFields()->processDataPost('x_customfields_data_fields', $data);

                    $value = e107::serialize($new_data['x_customfields_data_fields'], 'json');

                    $insert = [];
                    $insert = [
                            'plugin_name' => $cf_config['plugin_name'],
                            'plugin_table' => $cf_config['plugin_table'],
                            'plugin_id' => intval($id),
                            'data_fields' => $value,
                            '_DUPLICATE_KEY_UPDATE' => true,
                    ];

                    $var_dump = e107::getDb()->insert('datafields', $insert);
                }

                break;

            case 'delete':
                break;

            case 'batch':
                $id = (array) $id;
                $arrayOfRecordIds = $id['ids'];
                $command = $id['cmd'];
                break;

            default:
                // code to be executed if n is different from all labels;
        }
    }
}

/**
 * Custom field methods.
 */
class customfields_admin_form extends e_form
{

    /**
     * @param mixed      $curval
     * @param string     $mode
     * @param array|null $att
     *
     * @return string|null
     */

    public function x_customfields_data_fields($curval, $mode, $att = null) // 'x_' + plugin-folder + custom-field name.
    {
        e107::getDebug()->log($event);

        /** @var e_admin_controller_ui $controller */
        $controller = e107::getAdminUI()->getController();

        //$ui = $controller->getUI();
        $id = $controller->getId();

        $event = $controller->getEventName(); // eg 'news' 'page' etc.

        $cf_config = e107::getDb()->retrieve('customfields', '*', "plugin_event='".$event."'  LIMIT 1 ");
        $plugin_table = $cf_config['plugin_table'];

        $text = '';

        switch ($mode) {
            case 'read':
                $text = $curval;
                break;
            break;
            case 'write':
                $cf_config = e107::getDb()->retrieve('customfields', '*', "plugin_event='".$event."'  LIMIT 1 ");

                $plugin_table = $cf_config['plugin_table'];

                $data = e107::getDb()->retrieve('datafields', 'data_fields', "
				`plugin_table` LIKE '".$plugin_table."' AND `plugin_id` ='".$id."' LIMIT 1");

				//echo "`plugin_table` LIKE '".$plugin_table."' AND `plugin_id` ='".$id."' LIMIT 1" ; 
				//print_a($data);
                
				// e107::getCustomFields()->loadConfig($cf_config['config_fields']);
                e107::getCustomFields()->loadData($data);

                e107::getCustomFields()->setAdminUIData('x_customfields_data_fields', $controller);

               // e107::getCustomFields()->setAdminUIConfig('x_customfields_data_fields', $controller);

            break;
        }

        return $text='';
    }
}
