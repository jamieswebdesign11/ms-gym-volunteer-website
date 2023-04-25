<div class="wrap">    
    <div id="ecwd-settings" class="subscribe_page">
        <div id="ecwd-settings-content">
            <div id="mail_template_list">
                <?php
                if (!empty($this->mail_template)) {
                    ?> <ul class="ecwd_list_container">
                        <li class="ecwd_list ecwd_list_head">
                            <div class="mail_name"><?php _e('Name','event-calendar-wd')?></div>
                            <div class="edit"><?php _e('Edit','event-calendar-wd')?></div>
                        </li>
                        <?php
                        foreach ($this->mail_template as $key => $mail) {                            
                            $action = (isset($mail['action'])) ? '&action=' . $mail['action'] : "";
                            $action .= (isset($mail['ecwd_event_list']) && $mail['ecwd_event_list'] == true) ? '&ecwd_event_list=true' : "";
                            ?>
                            <li class="ecwd_list">
                                <div class="mail_name"><?php echo $mail['name']; ?></div>
                                <div class="edit"><a class="button" href="<?php echo $_SERVER['REQUEST_URI'] . '&type=' . $key . $action; ?>">Edit</a></div>
                            </li>
                        <?php }
                        ?> </ul><?php
                } else {
                    echo "NO Templates";
                }
                ?>
            </div>
        </div>        
    </div>
</div>
