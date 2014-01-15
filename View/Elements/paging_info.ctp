<div class="pagination_information">
<?php 
if(isset($this->Paginator)){
    echo $this->Paginator->counter(array('format' => __('Displaying <b>%start%-%end%</b> of <b>%count%</b> in total.')));
}
?>
</div>
