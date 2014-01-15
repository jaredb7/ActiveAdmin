<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-type"/>
    <title><?php echo $title_for_layout; ?></title>
    <?php
    echo $this->Html->css('/active_admin/css/admin');
    // Admin vendor includes Jquery 1.4.2 and other misc libraries
    echo $this->Html->script('/active_admin/js/admin_vendor');
    echo $this->Html->script('/active_admin/js/admin');
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
    <!--[if lt IE 9]>
    <?php echo $this->Html->script('/active_admin/js/modernizr.js'); ?>
    <![endif]-->
</head>
<body>
<div id="wrapper">
    <div id="header">
        <h1 id="site_title"><?php echo $this->Html->link('Site', "/"); ?></h1>
        <?php if ($this->params['action'] !== 'admin_login' && $adminMenu = $this->requestAction(array('plugin' => 'active_admin', 'controller' => 'dashboard', 'action' => 'menu'))): ?>
            <ul class="tabbed_navigation" id="tabs">
                <?php foreach ($adminMenu as $menuItem): ?>
                    <li<?php if ($this->params['controller'] == $menuItem['Dashboard']['url']['controller']) echo " class='current'" ?>
                        ><?php echo $this->Html->link($menuItem['Dashboard']['display_title'], array_merge($menuItem['Dashboard']['url'], array('action' => 'index'))); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php echo $this->element('user_info', array(), array('plugin' => 'ActiveAdmin')); ?>
    </div>

    <div id="title_bar">
            <span class="breadcrumb">
                <?php echo $this->Html->link('Admin', array('plugin' => 'active_admin', 'controller' => 'dashboard', 'action' => 'index')); ?>
                <span class="breadcrumb_sep">/</span>
                <?php
                if (strtolower($this->params['action']) != "admin_index")
                    echo $this->Html->link(Inflector::humanize($this->params['controller']), array('plugin' => false, 'controller' => $this->params['controller'], 'action' => 'index'));
                ?>
            </span>

        <h1 id="page_title">
            <?php echo $this->Html->link($this->name, array('controller' => $this->params['controller'], 'action' => 'index')); ?>
        </h1>

        <div class="action_items">
            <span
                class="action_item"><?php echo $this->Html->link('Clear cache', array('plugin' => 'active_admin', 'controller' => 'apis', 'action' => 'clear_cache', $this->params['plugin'], $this->params['controller'])) ?></span>
        </div>
    </div>

    <div <?php echo ($this->params['controller'] == 'dashboard') ? 'class="without_sidebar" ' : 'class="with_sidebar"' ?> id="active_admin_content">
        <div id="main_content_wrapper">
            <div id="main_content">
                <?php echo $this->Session->flash(); ?>
                <?php echo $this->Session->flash('auth'); ?>

                <!--Scopes-->
                <div class="table_tools">
                    <?php echo $this->element('scopes', array(), array('plugin' => 'ActiveAdmin')); ?>
                </div>

                <!--Pagination text above table eg. (Displaying X-X of X)-->
                <?php
                if (isset($this->Paginator) && $this->params['controller'] != 'dashboard') {
                    echo $this->element('paging_info', array(), array('plugin' => 'ActiveAdmin'));
                }
                ?>

                <!--content as rendered by controller methods-->
                <?php
                echo $this->fetch('content');
                ?>

                <!--links to download datasets-->
                <?php if (Configure::read('ActiveAdmin.allow_downloads') == true): ?>
                    <div class="download_links">Download:&nbsp;
                        <?php echo $this->element('downloads', array(), array('plugin' => 'ActiveAdmin')); ?>
                    </div>
                <?php endif ?>

                <!--Pagination at the bottom right of content area (has <Prev and next> links)-->
                <?php
                if (isset($this->Paginator) && $this->params['controller'] != 'dashboard') {
                    echo $this->element('paging', array(), array('plugin' => 'ActiveAdmin'));
                }
                ?>

                <!--Comments-->
                <div class="comments panel">
                    <?php echo $this->element('comments', array(), array('plugin' => 'ActiveAdmin')); ?>
                </div>

            </div>
            <!-- end main_content -->
        </div>
        <!-- end main_content_wrapper -->
        <!--Sidebar with filters-->
        <div id="sidebar">
            <?php
            if ($this->params['action'] == 'admin_index' && $this->params['controller'] != 'dashboard') {
                $file = new File(APP . 'View' . DS . 'Elements' . DS . strtolower($this->name) . '_filter.ctp');
                if ($file->exists()) {
                    echo $this->element(strtolower($this->name) . '_filter');
                } else {
                    echo $this->element('sidebar_filter', array(), array('plugin' => 'ActiveAdmin'));
                }
            }
            if ($this->params['action'] == 'admin_add' || $this->params['action'] == 'admin_edit') {

                $file = new File(APP . 'View' . DS . 'Elements' . DS . strtolower($this->name) . '_edit_info.ctp');
                if ($file->exists()) {
                    echo $this->element(strtolower($this->name) . '_edit_info');
                }
            }
            ?>
        </div>
        <!-- end sidebar -->

        <div class="clear"></div>
        <div id="footer">
            <p>Base on <a href="http://www.activeadmin.info">Active Admin</a> 0.3.0</p>
            <p>CakePHP Active Admin <?php echo ACTIVEADMIN_CAKE_VERSION ?></p>
        </div>
    </div>
    <!-- end active_admin_wrapper -->

    <?php if (!CakePlugin::loaded('DebugKit')): ?>
        <?php echo $this->element('sql_dump'); ?>
    <?php endif ?>
</body>
</html>

