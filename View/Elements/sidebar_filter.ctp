<?php
$params_passed_clean = $this->passedArgs;
unset($params_passed_clean['page']);

$model = ClassRegistry::init($source_model, true);

if ($model) {
    $displayField = $model->displayField;
    $filter_fields = $model->filter_fields ? $model->filter_fields : array();
}

if ($displayField){
?>
<div class="panel sidebar_section" id="filters_sidebar_section">
    <h3>Filters</h3>

    <div class="panel_contents">
        <?php echo $this->Form->create($source_model, array('url' => array_merge($params_passed_clean, array('action' => 'index')), 'class' => 'filter_form')); ?>

        <!--default filter for model display field-->
        <div
            class="filter_form_field <?php echo "filter_" . $filter_field_type = $model->getColumnType($displayField); ?>">
            <label> <?php echo __('Search %s', Inflector::humanize($displayField)) ?></label>
            <?php echo $this->Form->input($source_model . '.' . $displayField, array('type' => $filter_field_type, 'label' => false, 'required' => false, 'div' => false)); ?>
        </div>

        <!--Loop over any defined extra filter fields-->
        <?php if (!empty($filter_fields)): foreach ($filter_fields as $filter_field): ?>
            <?php $filter_field_type = "filter_" . $model->getColumnType($filter_field); ?>

            <div class="filter_form_field <?php echo $filter_field_type; ?>">
                <label> <?php echo __('Search %s', Inflector::humanize($filter_field)) ?></label>
                <?php echo $this->Form->input($source_model . '.' . $filter_field, array('label' => false, 'required' => false, 'div' => false)); ?>
            </div>

        <?php endforeach; endif ?>

        <!--default filter for creation date-->
        <div class="filter_form_field filter_date_range">
            <label><?php echo __('Created Between'); ?></label>
            <?php echo $this->Form->text($source_model . '.created.BETWEEN.0', array('class' => 'datepicker')); ?>
            <span class="seperator">-</span>
            <?php echo $this->Form->text($source_model . '.created.BETWEEN.1', array('class' => 'datepicker')); ?>
        </div>

        <!--filter form buttons-->
        <div class="buttons">
            <?php echo $this->Form->submit(__('Filter'), array('div' => false, 'id' => 'SubmitBtn')) ?>
            <?php echo $this->Html->link(__('Clear Filters'), "#", array('class' => 'clear_filters_btn clear_action')) ?>
        </div>

        <!--close form->
         <?php echo $this->Form->end(); ?>

  </div>
<?php } ?>

