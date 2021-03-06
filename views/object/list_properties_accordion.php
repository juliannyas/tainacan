<?php
/*
 * View Responsavel em mostrar as propriedades na hora de INSERCAO do objeto NO ACCOREON
 */
include_once ('js/list_properties_accordion_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../helpers/object/object_properties_widgets_helper.php');

$view_helper = new ViewHelper();
$object_properties_widgets_helper = new ObjectWidgetsHelper();
$ids = [];
$properties_autocomplete = [];
$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
//referencias
$references = [
    'properties_autocomplete' => &$properties_autocomplete,
    'properties_terms_radio' => &$properties_terms_radio,
    'properties_terms_checkbox' => &$properties_terms_checkbox,
    'properties_terms_tree' => &$properties_terms_tree,
    'properties_terms_selectbox' => &$properties_terms_selectbox,
    'properties_terms_multipleselect' => &$properties_terms_multipleselect,
    'properties_terms_treecheckbox' => &$properties_terms_treecheckbox   
];
if (isset($property_object)):
     foreach ($property_object as $property) { 
        $ids[] = $property['id']; ?>
        <div id="meta-item-<?php echo $property['id']; ?>"  class="form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                    if(has_action('modificate_label_insert_item_properties')):
                        do_action('modificate_label_insert_item_properties', $property);
                    endif;
                    //acao para modificaco da propriedade de objeto na insercao do item
                    if(has_action('modificate_insert_item_properties_object')): 
                             do_action('modificate_insert_item_properties_object',$property,$object_id,'property_value_'. $property['id'] .'_'.$object_id.'_add'); 
                    endif;
                    $object_properties_widgets_helper->generateValidationIcons($property);
                    ?>
            </h2>
            <?php if((isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true')): ?>
                <div>
                    <?php if(isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!=''): $property['metas']['value'][] = $property['metas']['socialdb_property_default_value']; ?>
                        <p><?php  echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $property['metas']['socialdb_property_default_value']. "'" . ',' . $property['id'] . ')">' .get_post($property['metas']['socialdb_property_default_value'])->post_title . '</a>';  ?></p>
                        <input type="hidden" 
                           name="socialdb_property_<?php echo $property['id']; ?>[]" 
                           value="<?php echo $property['metas']['socialdb_property_default_value'] ?>">
                    <?php else: ?>
                        <p><?php  _e('Empty field', 'tainacan') ?></p>
                    <?php endif ?>
                </div> 
            <?php else: ?>
                <div>
                    <?php $object_properties_widgets_helper->generateWidgetPropertyRelated($property,$object_id,$collection_id) ?>
                </div>    
            <?php endif; ?>
        </div>        
    <?php } ?>
    <input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
<?php endif; ?>

<?php if (isset($property_data)): 
    foreach ($property_data as $property) { 
        if($property['id']=='license'):
            continue;
        endif;
        $properties_autocomplete[] = $property['id']; 
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                $object_properties_widgets_helper->generateValidationIcons($property);
                ?>
            </h2>
            <?php $cardinality = $view_helper->render_cardinality_property($property);   ?>
            <div>
                <?php if((isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true')): ?>
                    <div>
                        <?php if(isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!=''): ?>
                            <p><?php  echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $property['metas']['socialdb_property_default_value']. "'" . ',' . $property['id'] . ')">' .$property['metas']['socialdb_property_default_value'] . '</a>';  ?></p>
                        <?php else: ?>
                            <p><?php  _e('Empty field', 'tainacan') ?></p>
                        <?php endif ?>
                    </div> 
                <?php else: ?>
                    <div>
                        <input type="hidden" class="form_autocomplete_value_<?php echo $property['id']; ?>_mask" 
                                   value="<?php echo ($property['metas']['socialdb_property_data_mask'] ) ? $property['metas']['socialdb_property_data_mask'] : '' ?>">
                        <?php for($i = 0; $i<$cardinality;$i++):   ?>
                        <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" class="row"
                             style="padding-bottom: 10px;margin-bottom: 10px;<?php echo ($i===0) ? 'display:block': 'display:none'; ?>">
                              <div class="col-md-11">
                               <?php if ($property['type'] == 'text') { ?>     
                                    <input type="text" 
                                           id="form_autocomplete_value_<?php echo $property['id']; ?>_origin" 
                                           class="form-control auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                                           value="<?php
                                                        if ($property['metas']['socialdb_property_default_value']): 
                                                            echo $property['metas']['socialdb_property_default_value'];
                                                        endif; ?>"
                                           name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                           >
                                <?php }elseif ($property['type'] == 'textarea') { ?>   
                                    <textarea class="form-control auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                                              id="form_autocomplete_value_<?php echo $property['id']; ?>_origin" 
                                              rows='9'
                                              name="socialdb_property_<?php echo $property['id']; ?>[]"
                                              ><?php if ($property['metas']['socialdb_property_default_value']):
                                                     echo $property['metas']['socialdb_property_default_value'];
                                                     endif; ?></textarea>
                                <?php }elseif ($property['type'] == 'numeric') { ?>   
                                    <input type="text" 
                                           id="form_autocomplete_value_<?php echo $property['id']; ?>_origin" 
                                           value="<?php  if ($property['metas']['socialdb_property_default_value']):
                                                            echo $property['metas']['socialdb_property_default_value'];
                                                        endif;  ?>" 
                                           class="form-control auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                                           onkeypress='return onlyNumbers(event)'
                                           name="socialdb_property_<?php echo $property['id']; ?>[]" <?php
                                           if ($property['metas']['socialdb_property_required'] == 'true'): echo 'required="required"';
                                           endif;
                                           ?>>
                                <?php }elseif ($property['type'] == 'autoincrement') { ?>   
                                    <input disabled="disabled"  type="number" class="form-control" name="only_showed_<?php echo $property['id']; ?>" value="<?php
                                    if (is_numeric($property['metas']['socialdb_property_data_value_increment'])): echo $property['metas']['socialdb_property_data_value_increment'] + 1;
                                    endif;
                                    ?>">
                                    <!--input type="hidden"  name="socialdb_property_<?php echo $property['id']; ?>" value="<?php
                                    if ($property['metas']['socialdb_property_data_value_increment']): echo $property['metas']['socialdb_property_data_value_increment'] + 1;
                                    endif;
                                    ?>" -->
                                <?php }elseif ($property['type'] == 'radio' && $property['name'] == 'Status') { ?>   
                                    <br>
                                    <input   type="radio" onchange="validate_status(<?php echo $property['id']; ?>)" checked="checked" name="socialdb_property_<?php echo $property['id']; ?>" value="current">&nbsp;<?php _e('Current', 'tainacan') ?><br>
                                    <input   type="radio" onchange="validate_status(<?php echo $property['id']; ?>)" name="socialdb_property_<?php echo $property['id']; ?>" value="intermediate">&nbsp;<?php _e('Intermediate', 'tainacan') ?><br>
                                    <input   type="radio" onchange="validate_status(<?php echo $property['id']; ?>)" name="socialdb_property_<?php echo $property['id']; ?>" value="permanently">&nbsp;<?php _e('Permanently', 'tainacan') ?><br>
                                <?php } else if($property['type'] == 'date'&&!has_action('modificate_insert_item_properties_data')) { ?>
                                    <script>
                                        $(function() {
                                            init_metadata_date( "#form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i ?>" );
                                        });
                                    </script>    
                                    <input 
                                        style="margin-right: 5px;" 
                                        size="13" 
                                        class="input_date auto-save form_autocomplete_value_<?php echo $property['id']; ?>" 
                                        type="text" value="<?php
                                        if ($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value'];endif;?>" 
                                        id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i ?>" 
                                        name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                        >
                               <?php } 
                                // gancho para tipos de metadados de dados diferentes
                                else if(has_action('modificate_insert_item_properties_data')){
                                    do_action('modificate_insert_item_properties_data',$property);
                                    continue;
                                }else{ ?>
                                    <input type="text" 
                                           value="<?php 
                                                    if ($property['metas']['socialdb_property_default_value']): 
                                                        echo $property['metas']['socialdb_property_default_value'];
                                                    endif; ?>" 
                                         class="form-control auto-save form_autocomplete_value_<?php echo $property['id']; ?>"
                                         name="socialdb_property_<?php echo $property['id']; ?>[]" >
                                       <?php } ?>
                                </div>
                                <?php echo $view_helper->render_button_cardinality($property,$i) ?>    
                             </div>         
                        <?php endfor;  ?>            
                </div> 
                <?php endif; ?>
            </div>    
        </div>              
    <?php } ?>
    <?php
endif;
if ((isset($property_term) && count($property_term) > 1) || (count($property_term) == 1 )):
     foreach ($property_term as $property) { 
        //if(!isset($property['has_children'])||empty($property['has_children'])){
         //   continue;
        //}
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" <?php do_action('item_property_term_attributes') ?>>
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                else: // validacoes e labels
                        $property['metas']['socialdb_property_help'] = ($property['metas']['socialdb_property_help']==''&&$property['type'] == 'tree')? __('Select one option','tainacan') : '';
                        $object_properties_widgets_helper->generateValidationIcons($property);
                 endif; 
                 ?>
            </h2>   
            <?php if((isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true')): ?>
                    <div>
                        <?php if(isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!=''): ?>
                            <p><?php  echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $property['metas']['socialdb_property_default_value']. "'" . ',' . $property['id'] . ')">' .get_term_by('id', $property['metas']['socialdb_property_default_value'], 'socialdb_category_type')->name . '</a>';  ?></p>
                            <input  type="hidden" 
                                    name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                    value="<?php echo $property['metas']['socialdb_property_default_value'] ?>">
                            <script> append_category_properties(<?php echo $property['metas']['socialdb_property_default_value'] ?>,0,<?php echo $property['id']; ?> )</script>
                        <?php else: ?>
                            <p><?php  _e('Empty field', 'tainacan') ?></p>
                        <?php endif ?>
                    </div> 
            <?php else: ?>
            <div>
            <?php
            if ($property['type'] == 'radio') {
                $properties_terms_radio[] = $property['id'];
                ?>
                <div id='field_property_term_<?php echo $property['id']; ?>'></div>
                <input type="hidden" 
                           id='socialdb_propertyterm_<?php echo $property['id']; ?>_value'
                           name="socialdb_propertyterm_<?php echo $property['id']; ?>_value" 
                           value="">
                <?php
            } elseif ($property['type'] == 'tree') {
                $properties_terms_tree[] = $property['id'];
                ?>
                <button type="button"
                        onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>')" 
                        class="btn btn-primary btn-xs">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php _e('Add Category','tainacan'); ?>
                </button>
                <br><br>
                <div st class="row">
                    <div  style='height: 150px;' class='col-lg-12'  id='field_property_term_<?php echo $property['id']; ?>'></div>
                    <!--select  name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php
                    if ($property['metas']['socialdb_property_required'] == 'true'): echo 'required="required"';
                    endif;
                    ?>></select-->
                    <input type="hidden" 
                           id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                           name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                           value="">
                </div>
                <?php
            }elseif ($property['type'] == 'selectbox') {
                $properties_terms_selectbox[] = $property['id'];
                ?>
                <select class="form-control auto-save" 
                        name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                        onchange="list_validate_selectbox(this,'<?php echo $property['id']; ?>')" 
                        id='field_property_term_<?php echo $property['id']; ?>'>
                    <option><?php _e('Select...','tainacan') ?></option>
                </select>
                <input type="hidden" 
                           id='socialdb_propertyterm_<?php echo $property['id']; ?>_value'
                           name="socialdb_propertyterm_<?php echo $property['id']; ?>_value" 
                           value="">
                <?php
            }elseif ($property['type'] == 'checkbox') {
                        $properties_terms_checkbox[] = $property['id'];
                        ?>
                <div id='field_property_term_<?php echo $property['id']; ?>'></div>
                <?php
            } elseif ($property['type'] == 'multipleselect') {
                $properties_terms_multipleselect[] = $property['id'];
                ?>
                <select size='1' 
                        multiple 
                        onclick="validate_multipleselectbox(this,'<?php echo $property['id']; ?>')"
                        class="form-control auto-save" 
                        name="socialdb_propertyterm_<?php echo $property['id']; ?>[]" 
                        id='field_property_term_<?php echo $property['id']; ?>' <?php
                if ($property['metas']['socialdb_property_required'] == 'true'): echo 'required="required"';
                endif;
                ?>></select>
                        <?php
            }elseif ($property['type'] == 'tree_checkbox') {
                $properties_terms_treecheckbox[] = $property['id']; ?>
                <button type="button"
                        onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>')" 
                        class="btn btn-primary btn-xs">
                        <span class="glyphicon glyphicon-plus"></span>
                            <?php _e('Add Category','tainacan'); ?>
                </button>
                <br><br>
                <div class="row">
                    <div style='height: 150px;' class='col-lg-12'  id='field_property_term_<?php echo $property['id']; ?>'></div>
                    <!--select multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php
                    if ($property['metas']['socialdb_property_required'] == 'true'): echo 'required="required"';
                    endif;
                    ?>>
                    </select -->
                    <div id='socialdb_propertyterm_<?php echo $property['id']; ?>' ></div>
                </div>
                <?php
            }
            ?> 
            </div>   
            <?php endif; ?>
            <div  id="append_properties_categories_<?php echo $property['id']; ?>"></div>
        </div>
    <?php } ?>
<?php endif; ?>
<?php $object_properties_widgets_helper->list_properties_compounds($property_compounds, $object_id,$references)  ?>    
<input type="hidden" name="properties_autocomplete" id='properties_autocomplete' value="<?php echo implode(',', array_unique($properties_autocomplete)); ?>">
<input type="hidden" name="properties_terms_radio" id='properties_terms_radio' value="<?php echo implode(',', array_unique($properties_terms_radio)); ?>">
<input type="hidden" name="properties_terms_tree" id='properties_terms_tree' value="<?php echo implode(',', array_unique($properties_terms_tree)); ?>">
<input type="hidden" name="properties_terms_selectbox" id='properties_terms_selectbox' value="<?php echo implode(',', array_unique($properties_terms_selectbox)); ?>">
<input type="hidden" name="properties_terms_checkbox" id='properties_terms_checkbox' value="<?php echo implode(',', array_unique($properties_terms_checkbox)); ?>">
<input type="hidden" name="properties_terms_multipleselect" id='properties_terms_multipleselect' value="<?php echo implode(',', array_unique($properties_terms_multipleselect)); ?>">
<input type="hidden" name="properties_terms_treecheckbox" id='properties_terms_treecheckbox' value="<?php echo implode(',', array_unique($properties_terms_treecheckbox)); ?>">
<?php if (isset($all_ids)): ?>
    <input type="hidden" id="properties_id" name="properties_id" value="<?php echo $all_ids; ?>">
    <input type="hidden" id="property_origin" name="property_origin" value="<?php echo $all_ids; ?>">
    <input type="hidden" id="property_added" name="property_added" value="">
    <input type="hidden" id="selected_categories" name="selected_categories" value="">
    <div id="append_properties_categories" class="hide"></div>
<?php
 endif; 