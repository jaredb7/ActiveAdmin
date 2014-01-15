# Active Admin for CakePHP 2.x -- 2.0/2.1 compliance may need some tweaks 

Based on Active Admin for RoR (http://activeadmin.info/). This plugin for CakePHP gives you the same administration interface for the PHP framework. It also uses Nik Chankov's Filter component (http://nik.chankov.net).
This install assumes that you've setup your prefix to be admin using the following routing prefix:

    Configure::write('Routing.prefixes', array('admin'));

Essentially this will create the backend at a url like: http://your-domain-here.com/admin

Firstly thanks go out to these guys for patching/tweaking ActiveAdmin =D
* https://github.com/TeckniX/
* https://github.com/s-okami/
and the original author
* https://github.com/gerhardsletten/

Features
 * A awesome looking Admin page/s out of the box =D.
 * Search Filters (in addition to using $displayField, add $aaFilter variable in your model with an array of field names to have them as additional filter fields, see step 8.)
 * Scopes to creation sections/groups of data (requires some work on your behalf writing custom findTypes, see 9.)
 * Ability to export data from view in common formats (CSV, JSON, XML)
 * Admin Comments (can be disabled app-wide or on per model basis)
 * Basic Authentication (as implemented by s-okami)
 * Works with CakePHP 2.3.X

Future features & enhancements
 *   Batch actions (like ROR), but only for deleting
 *   Dashboard settings to choosing what controllers to link in menu and such (replacement of step 6.)

## Install

1 - Clone the project as "ActiveAdmin" into your apps plugins-folder (app/Plugin/)

2 - Enable the plugin in your app/Config/bootstrap.php file
    
    CakePlugin::load(array('ActiveAdmin' => array('bootstrap' => true, 'routes' => true)));

### Prepare your app's controllers

3 - For the admin_index function:
```
    function admin_index() {
        $this->Post->recursive = 0;
        // Add this 
        $filter = $this->Filter->process($this);
        $this->set('posts', $this->paginate(null, $filter));

        //Comment out this line (usually the last line if you baked your Controllers and such)
        $this->set('posts', $this->paginate());
    }
```
4 - And update your View/(Controller)/admin_index.ctp views, using a table-header element that enable table-sorting:
   > [NOTE]:: This seems to be happening by default now? I baked most of my app in 2.2.X
```
    <table cellpadding="0" cellspacing="0">
    <?php echo $this->element('table_header', array('keys'=>array('id', 'title', 'label' => 'slug','created', 'modified')), array('plugin'=>'ActiveAdmin')); ?>
      <?php
      $i = 0;
      foreach ($posts as $post):
        $class = null;
        if ($i++ % 2 == 0) {
          $class = ' class="even"';
        }
      ?>
      <tr<?php echo $class;?>>
        <td><?php echo $post['Post']['id']; ?>&nbsp;</td>
        <td><?php echo $post['Post']['title']; ?>&nbsp;</td>
        <td><?php echo $post['Post']['slug']; ?>&nbsp;</td>
        <td><?php echo $post['Post']['created']; ?>&nbsp;</td>
        <td><?php echo $post['Post']['modified']; ?>&nbsp;</td>
        <td class="actions">
          <?php echo $this->Html->link(__('View'), array('action' => 'view', $post['Post']['id'])); ?>
          <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $post['Post']['id'])); ?>
          <?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $post['Post']['id']), null, __('Are you sure you want to delete # %s?', $post['Post']['id'])); ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </table>
```
5 - Create the table for ActiveAdmin using the schema shell:
```   
    ./Console/cake schema create --plugin ActiveAdmin --name dashboard
```
#ADDITIONAL
5.1 - And run the Config/Schema/users.sql ( if you want to use Authentication provided by s.okami :) )
5.2 - And run the Config/Schema/admin_comments.sql for AdminComments

6 - Admin Comments, these are on by default, see ActiveAdmin bootstrap to turn them off globally
    To turn them off on a per model basis, simply add the public variable $aaNoComment = true to them.

Finally, data integrity, add this to your app's AppModel, this should remove comments associated with a item/resource when it gets deleted
```
      public function afterDelete()
        {
            //If the active_admin plugin is loaded remove any comments
            if (CakePlugin::loaded("ActiveAdmin")) {
                App::uses('AdminComment', 'ActiveAdmin.Model');
                $aa_comment = new AdminComment();
                $aa_comment->removeComments($this->alias, $this->id);
            }
            return true;
        }
```

7 - Adding Admin Menu controller items can be done via the provided console shell (eg. adding Posts or the Categories from Blog plugin)
```    
    ./Console/cake ActiveAdmin.resource Posts
    ./Console/cake ActiveAdmin.resource Blog.Categories
```
8 - OPTIONAL -- Additional Filters
    To obtain additional filters simply add the $aaFilter to your models
        eg. public $aaFilter = array("start_desc", "start_platform", "end_desc", "end_platform");
        This would let you search on any or either of these fields multiple fields will AND together.

9 - OPTIONAL -- Scopes
     See the CakePHP Cookbook, on writing these, essentially these can be thought of as shortcut methods.
     eg. You could write one to find Products modified in the past 30 days (using conditions), and then you
     could simple write, $Products->find('recent') to retrieve these
       http://book.cakephp.org/2.0/en/models/retrieving-your-data.html#creating-custom-find-types

You have to also add the 'Paginator' component to your AppController or on per controller basis.
```
        eg. public $components = array('Paginator');
```

> NOTE If you're experiencing some issues with the filter, make sure that the display field is set in your model:
```    
    var $displayField = "title";
```    
The above would set the filter search on the title field of the model. It will default to ID otherwise and for some reason doesn't show
