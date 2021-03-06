<?php
wp_enqueue_media();
wp_enqueue_style('wolfie-settings-css');
wp_enqueue_script('wolfie-image-picker');
$name = (isset($name)) ? $name : '';
$nameInline = (isset($name)) ? 'name="'.$this->settings.'['.$name.']"' : '' ;
$value = (isset($this->settingsArray[$name]))? $this->settingsArray[$name] : '' ;
$value = (isset($groupVal)) ? $groupVal : $value ;
ob_start();
echo '<div class="wolfie-form-control">';
echo '<label>'.$label.'</label>';
echo '<input class="image" '.$nameInline.' value="'.$value.'" type="text" hidden>';
echo '<div class="actions"><button class="add">Add Image</button><button class="remove">Remove Image</button></div><div class="image-holder holder">';
if(!empty($value)) {
	echo '<div class="item" data-id="'.$value.'">';
	echo $thumb = wp_get_attachment_image( $value, [100,100] );
	echo '<a href="#" class="wolfie-close"></a>';
	echo '</div>';
}
echo '</div>';
echo '</div>';
$content = ob_get_clean();
if($print === true) {
	echo $content;
}