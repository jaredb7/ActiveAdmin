<?php
$params_passed_clean = $this->passedArgs;
unset($params_passed_clean['page']);

$model = ClassRegistry::init($source_model, true);
$findMethod = array();
if ($model!=false) {
    $findMethod = $model->findMethods;
}

if ($findMethod && (strtolower($this->request->params['controller']) != "dashboard") && (strtolower($this->request->params['action'] == "admin_index"))) {
    ?>
    <ul class="scopes table_tools_segmented_control">
        <?php
        //scopes/default findTypes to exclude
        $no_scope = array('first', 'count', 'neighbors', 'list', 'threaded',);
        $scope = "all"; //Default scope

        $params_passed_clean = $this->passedArgs;

        if (array_key_exists('scope', $params_passed_clean)) {
            $scope = $params_passed_clean['scope'];
        }

        foreach ($findMethod as $key => $item) {
            unset($params_passed_clean[$key]);
        }
        //remove any of the default find types/scopes we don't want to allow/show
        //this should leave us with 'all' and whatever custom find types you've defined in your models
        foreach ($no_scope as $ns_k) {
            unset($findMethod[$ns_k]);
        }
        ?>

        <?php foreach ($findMethod as $key => $item): ?>
            <?php if ($key == $scope): ?>
                <li class="scope selected ">
                    <a class="table_tools_button"><?php echo Inflector::humanize($key) ?></a>
                    <?php if (isset($counts[$key])): ?>
                        <span class="count"><?php echo $counts[$key] ?></span>
                    <?php endif; ?>
                </li>
            <?php else: ?>
                <li class="scope">
                    <?php echo $this->Html->link(Inflector::humanize($key), array_merge($params_passed_clean, array('action' => 'index', 'scope' => $key)), array('class' => 'table_tools_button')) ?>
                    <?php if (isset($counts[$key])): ?>
                        <span class="count"><?php echo $counts[$key] ?></span>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php } ?>