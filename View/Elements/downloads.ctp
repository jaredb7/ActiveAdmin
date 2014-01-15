<?php
//http://s1.brislink.me/v1/admin/export/c:trips/type:csv?start_desc=central+station&created%5BBETWEEN%5D%5B0%5D=&created%5BBETWEEN%5D%5B1%5D=
//Read passed args
$params_passed_clean = $this->passedArgs;
unset($params_passed_clean['page']);
//What's our controller name
$controller_name = $this->request->params['controller'];
$clean_controller_name = Inflector::camelize(Inflector::singularize($controller_name));

//If we are not on the download or dashboard controllers
if ($controller_name != "download" && $controller_name != "dashboard") {
    //Following CakePHP conventions the model name is in the singularized version of the controller name.
    $modelName = $clean_controller_name;
    $request_post = $params_passed_clean;
    if (!empty($this->request->data)) {
        $request_post = $this->request->data[$modelName];
    }

    if (isset($request_post)) {
        ?>

        <?php echo $this->Html->link('CSV',
            array_merge(array('controller' => 'download', 'plugin' => 'active_admin', 'action' => 'export', 'c' => $controller_name, 'type' => 'csv'), array('?' => $request_post))
        );

        ?> &nbsp; <?php

        echo $this->Html->link('XML',
            array_merge(array('controller' => 'download', 'plugin' => 'active_admin', 'action' => 'export', 'c' => $controller_name, 'type' => 'xml'), array('?' => $request_post))

        );

        ?> &nbsp; <?php

        echo $this->Html->link('JSON',
            array_merge(array('controller' => 'download', 'plugin' => 'active_admin', 'action' => 'export', 'c' => $controller_name, 'type' => 'json'), array('?' => $request_post))
        );
        ?>

    <?php
    }
}?>

