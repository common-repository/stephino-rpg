<?php
/**
 * Template:Timelapse:List Entities
 * 
 * @title      Timelapse template - List entities
 * @desc       Template fragment
 * @copyright  (c) 2021, Stephino
 * @author     Mark Jivko <stephino.team@gmail.com>
 * @package    stephino-rpg
 * @license    GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */
!defined('STEPHINO_RPG_ROOT') && exit();

/* @var $entityCountExact Show the exact unit count OR an ISU aproximation*/
if (!isset($entityCountExact)) {
    $entityCountExact = true;
}
if (!isset($entitiesCityId)) {
    $entitiesCityId = null;
}
/* @var $entitiesList Entities list */
if (isset($entitiesList) && is_array($entitiesList)):
?>
    <div class="row justify-content-center">
        <?php 
            foreach ($entitiesList as $entityData):
                $entityConfig = Stephino_Rpg_Db_Table_Entities::ENTITY_TYPE_UNIT == $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_TYPE]
                    ? Stephino_Rpg_Config::get()->units()->getById($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CONFIG_ID])
                    : Stephino_Rpg_Config::get()->ships()->getById($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CONFIG_ID]);
            
                if (null === $entityConfig) {
                    continue;
                }
                
                if (false !== $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT] && $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT] <= 0) {
                    continue;
                }
                
                // Get the item card details
                list($itemCardFn, $itemCardArgs) = Stephino_Rpg_Utils_Config::getItemCardAttributes($entityConfig);
        ?>
            <div class="col-12 col-lg-3 text-center"
                data-role="entity" 
                data-entity-count="<?php echo $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT];?>"
                data-entity-type="<?php echo $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_TYPE];?>"
                data-entity-config="<?php echo $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CONFIG_ID];?>" >
                <div 
                    class="item-card framed mt-4" 
                    data-click="<?php echo $itemCardFn;?>"
                    data-click-args="<?php echo $itemCardArgs;?>"
                    <?php if (null !== $entitiesCityId):?>
                        data-click-city-id="<?php echo (int) $entitiesCityId;?>"
                    <?php endif;?>
                    data-effect="background" 
                    data-effect-args="<?php echo $entityConfig->keyCollection();?>,<?php echo $entityConfig->getId();?>">
                    <span>
                        <?php echo $entityConfig->getName(true);?>
                    </span>
                    <span class="label" data-html="true" title="&times; <?php echo number_format($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT]);?>">
                        <span>
                            &times; <b><?php 
                                // false set in Stephino_Rpg_TimeLapse_Convoys::_spy()
                                if (false !== $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT]) {
                                        echo (
                                            $entityCountExact 
                                                ? number_format($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT]) 
                                                : Stephino_Rpg_Utils_Lingo::isuFormat(
                                                    // Replace all digits except the first with zeros
                                                    (int) preg_replace(
                                                        '%(?<!^)\d%', 
                                                        '0', 
                                                        round($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT], 0)
                                                    ), 
                                                    0
                                                )
                                        );
                                } else {
                                    echo '&#x1F6AB;';
                                }
                            ?></b>
                        </span>
                    </span>
                </div>
            </div>
        <?php endforeach;?>
    </div>
<?php endif;?>