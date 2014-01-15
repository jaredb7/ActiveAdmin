<?php
//Plugin version
define('ACTIVEADMIN_CAKE_VERSION', "0.4.5");

//Allow ActiveAdmin comments
Configure::write('ActiveAdmin.allow_comments', true);
//Allow BatchActions? --NOT IMPLEMENTED
Configure::write('ActiveAdmin.allow_batch_actions', false);
//Allow downloading of data
Configure::write('ActiveAdmin.allow_downloads', true);

//Controllers to ignore for filter, scopes and comments
Configure::write('ActiveAdmin.ignore', array());