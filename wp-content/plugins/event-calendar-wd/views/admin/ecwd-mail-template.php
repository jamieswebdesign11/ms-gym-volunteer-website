<div class="wrap">
    <div id="ecwd-settings" class="subscribe_page">
        <div id="ecwd-settings-content">
            <div id="mail_template" style="position: relative;">
                <div class="ecwd_post_title"><h2><?php echo $name; ?></h2></div>
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] . '&type=' . $type ?>">
                    <input type="submit" class="ecwd_reset_template" style="position: absolute ; z-index: 999 ; left: 100px ; bottom: 0px; cursor: pointer;" value="<?php _e('Reset','event-calendar-wd')?>"  name="reset_template"/>
                    <input type="hidden" value="<?php echo $type; ?>" name="ecwd_template" />
                    <input type="hidden" value="<?php echo $ajax_action; ?>" name="ecwd_action" />
                    <div style="float:right">
                        <?php if (!empty($events)) { ?>
                            <select id="ecwd_events_list">
                                <?php
                                foreach ($events as $ev) {
                                    ?>
                                    <option value="<?php echo $ev->ID; ?>"><?php echo $ev->post_title ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        <?php } ?>
                        <button type="button" id="preview_mail" class="button">Preview</button></div>
                </form>
                <?php if(isset($_POST["save_teamplate"]) && $_POST["save_teamplate"]==="Save"){
                    echo'<div id="message" class="updated notice notice-success is-dismissible">
                        <p>Template Saved</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
                };?>
                <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] . '&type=' . $type ?>">
                    <?php
                    echo wp_editor(stripslashes($html), "mailtemplate", array(
                        'textarea_name' => 'mail_content',
                        'textarea_rows' => 20,
                        "media_buttons" => 0,
                        'tinymce' => false
                    ));
                    ?>
                    <?php wp_nonce_field($type, 'ecwd_edit_template'); ?>
                    <input style="cursor: pointer;" class="ecwd_save_template" name="save_teamplate" type="submit" value="Save" />
                    <div class="ecwd_mail_popup_container"></div>
                </form>
            </div>
        </div>
    </div>
</div>