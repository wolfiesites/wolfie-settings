<?php
wp_enqueue_media();
wp_enqueue_script('jquery-ui-sortable');
wp_enqueue_style('wolfie-settings-css');
wp_enqueue_script('wolfie-gallery-picker');
$name = (isset($name)) ? $name : '';
$nameInline = (isset($name)) ? 'name="'.$this->settings.'['.$name.']"' : '' ;
$value = (isset($this->settingsArray[$name]))? $this->settingsArray[$name] : '' ;
$value = (isset($groupVal)) ? $groupVal : $value ;
ob_start();
echo '<div class="wolfie-form-control">';
echo '<label>'.$label.'</label>';
echo '<input class="gallery-wolfie" '.$nameInline.' value="'.$value.'" type="text" hidden>';
echo '<div class="actions"><button class="add">Add Images</button><button class="remove">Remove gallery</button></div><div class="images-holder holder">';
if(!empty($value)) {
	$value = explode(',', $value);
	foreach ($value as $index => $id) {
		echo '<div class="item" data-id="'.$id.'">';
		echo $thumb = wp_get_attachment_image( $id, [100,100] );
		echo '<a href="#" class="wolfie-close"></a>';
		echo '</div>';
	}
}
echo '</div>';
echo '</div>';
$content = ob_get_clean();
if($print === true) {
    echo $content;
}