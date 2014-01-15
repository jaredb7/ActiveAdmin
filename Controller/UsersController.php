<?php
App::uses('AppController', 'Controller');

/*
    ログイン管理をします
*/
class UsersController extends ActiveAdminAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        /*
            If under development mode, enable to add user.
            But now, this does not work correctly...
        */
        if (Configure::read('debug') > 0) {
            $this->Auth->allow('admin_add');
            //TODO: Cant access user add when in dev mode
        }
    }
    
    /*
        When you want to implement more actions, visit at below.
        
        http://book.cakephp.org/2.0/ja/tutorials-and-examples/blog-auth-example/auth.html
    */
    public function admin_add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                $this->redirect(array(
                    'controller' => 'users', 
                    'action' => 'login'
                ));
            }
            $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
        }
    }
    
    public function admin_login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirect(array(
                    'controller' => 'dashboard', 
                    'action' => 'index'
                )));
            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }
    
    public function admin_logout() {
        $this->redirect($this->Auth->logout());
    }
}