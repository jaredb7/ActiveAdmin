<?php
//Get controller name
$controller_name = $this->request->params['controller'];

//If the controller name is set and we're not on the dashboard controller and not on the index or add action
if (isset($controller_name)
    && (strtolower($this->request->params['controller']) != "dashboard")
    && (strtolower($this->request->params['action'] != "admin_index") ||
        strtolower($this->request->params['action'] != "admin_add"))
) {

    //Get the resource id, form the passedArgs
    $resource_id = null;
    if (array_key_exists(0, $this->passedArgs)) {
        $resource_id = $this->request->params['pass'][0];
    }

    //If the resource id is set then we can use it to retrieve and add against
    if (isset($resource_id)) {

        echo $this->element('comment_list', array('controller_name' => $controller_name, 'resource_id' => $resource_id), array('plugin' => 'ActiveAdmin'));
        echo $this->element('comment_add', array('controller_name' => $controller_name, 'resource_id' => $resource_id), array('plugin' => 'ActiveAdmin'));
    } //end if for (isset($resource_id)
}//end if for (isset($controller_name)
