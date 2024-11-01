<?php

/**
 * ThemeWarlock - Stephino_Rpg_Renderer_Ajax_Dialog_Convoy
 * 
 * @title      Dialog::Convoy
 * @desc       Convoy dialogs
 * @copyright  (c) 2021, Stephino
 * @author     Mark Jivko <stephino.team@gmail.com>
 * @package    ThemeWarlock
 * @since      TW 1.0
 */
class Stephino_Rpg_Renderer_Ajax_Dialog_Convoy extends Stephino_Rpg_Renderer_Ajax_Dialog {
    
    // Dialog templates
    const TEMPLATE_LIST = 'convoy/convoy-list';
    
    /**
     * Show all the current convoys
     */
    public static function ajaxList() {
        // Get the convoys
        $convoyList = Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Convoys::KEY)->getData();
        
        // Show the dialog
        require self::dialogTemplatePath(self::TEMPLATE_LIST);
        
        return Stephino_Rpg_Renderer_Ajax::wrap(
            array(
                self::RESULT_TITLE => __('Convoys', 'stephino-rpg'),
            )
        );
    }
}

/*EOF*/