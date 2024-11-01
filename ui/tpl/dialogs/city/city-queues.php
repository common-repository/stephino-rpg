<?php //
/**
 * Template:Dialog:City
 * 
 * @title      City dialog - queues
 * @desc       Template for the city queues dialog
 * @copyright  (c) 2021, Stephino
 * @author     Mark Jivko <stephino.team@gmail.com>
 * @package    stephino-rpg
 * @license    GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */
!defined('STEPHINO_RPG_ROOT') && exit();

/* @var $queueData array */
/* @var $cityInfo array */
foreach ($queueData as $queueType => $queueRows):
    list($queueMax, $queueMaxConfigId) = Stephino_Rpg_Db::get()->modelPremiumModifiers()->getMaxQueue($queueType);
?>
    <div class="framed col-12 mb-4">
        <h5>
            <span>
                <?php switch ($queueType): case Stephino_Rpg_Db_Model_Buildings::NAME: ?>
                    <?php echo Stephino_Rpg_Config::get()->core()->getConfigBuildingsName(true);?>:
                <?php break; case Stephino_Rpg_Db_Model_Entities::NAME: ?>
                    <?php echo esc_html__('Entities', 'stephino-rpg');?>:
                <?php break; case Stephino_Rpg_Db_Model_ResearchFields::NAME: ?>
                    <?php echo Stephino_Rpg_Config::get()->core()->getConfigResearchFieldsName(true);?>:
                <?php break; endswitch;?>
                <b>
                    <?php echo count($queueRows);?>
                </b> /
                <span
                    <?php if (0 != $queueMaxConfigId):?>
                        data-effect="help"
                        data-effect-args="<?php echo Stephino_Rpg_Config_PremiumModifiers::KEY;?>,<?php echo $queueMaxConfigId;?>"
                    <?php endif;?>>
                    <?php echo $queueMax; ?>
                </span>
            </span>
        </h5>
        <?php if (count($queueRows)):?>
            <?php 
                foreach ($queueRows as $queueRow): 
                    $entityConfig = null;
                    $entityDelta = null;
                    switch ($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_TYPE]) {
                        case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_BUILDING:
                            // Find the building info
                            if (is_array(Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Resources::KEY)->getData())) {
                                foreach (Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Resources::KEY)->getData() as $dbRow) {
                                    // Found our city
                                    if ($cityInfo[Stephino_Rpg_Db_Table_Cities::COL_ID] == $dbRow[Stephino_Rpg_Db_Table_Cities::COL_ID]
                                        && $queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_ID] == $dbRow[Stephino_Rpg_Db_Table_Buildings::COL_ID]) {
                                        // New building level
                                        $entityDelta = intval($dbRow[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_LEVEL]) + 1;
                                        
                                        // Building config
                                        $entityConfig = Stephino_Rpg_Config::get()->buildings()->getById(
                                            $dbRow[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_CONFIG_ID]
                                        );
                                        break;
                                    }
                                }
                            }
                            break;
                        
                        case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_UNIT:
                        case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_SHIP:
                            // Entities spawned
                            $entityDelta = $queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_QUANTITY];
                            
                            // Find the entity config
                            if (is_array(Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Support_Entities::KEY)->getData())) {
                                foreach (Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Support_Entities::KEY)->getData() as $dbRow) {
                                    // Found our city
                                    if ($cityInfo[Stephino_Rpg_Db_Table_Cities::COL_ID] == $dbRow[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CITY_ID]
                                        && $queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_ID] == $dbRow[Stephino_Rpg_Db_Table_Entities::COL_ID]) {
                                        // Entity config
                                        $entityConfig = Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_UNIT == $queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_TYPE]
                                            ? Stephino_Rpg_Config::get()->units()->getById($dbRow[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CONFIG_ID])
                                            : Stephino_Rpg_Config::get()->ships()->getById($dbRow[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_CONFIG_ID]);
                                        break;
                                    }
                                }
                            }
                            break;
                            
                        case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_RESEARCH:
                            // Find the research field config
                            if (is_array(Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Support_ResearchFields::KEY)->getData())) {
                                foreach (Stephino_Rpg_TimeLapse::get()->worker(Stephino_Rpg_TimeLapse_Support_ResearchFields::KEY)->getData() as $dbRow) {
                                    // Found our city
                                    if ($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_ID] == $dbRow[Stephino_Rpg_Db_Table_ResearchFields::COL_ID]) {
                                        // Research field config
                                        $entityConfig = Stephino_Rpg_Config::get()->researchFields()->getById(
                                            $dbRow[Stephino_Rpg_Db_Table_ResearchFields::COL_RESEARCH_FIELD_CONFIG_ID]
                                        );
                                        
                                        // Levels enabled
                                        if ($entityConfig->getLevelsEnabled()) {
                                            $entityDelta = $dbRow[Stephino_Rpg_Db_Table_ResearchFields::COL_RESEARCH_FIELD_LEVEL] + 1;
                                        }
                                        break;
                                    }
                                }
                            }
                            break;
                    }
                    
                    // Invalid queue
                    if (null === $entityConfig) {
                        continue;
                    }
                    
                    // Get the item card details
                    list($itemCardFn, $itemCardArgs) = Stephino_Rpg_Utils_Config::getItemCardAttributes($entityConfig);
                    
                    // Prepare the countdown details
                    $queueLeft = intval($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_TIME]) - time();
                    $queueTotal = intval($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_DURATION]);
            ?>
                <div class="row col-12 m-0 align-items-center">
                    <div class="col-12 col-lg-3 text-center">
                        <div 
                            class="item-card framed mt-4" 
                            data-click="<?php echo $itemCardFn;?>"
                            data-click-args="<?php echo $itemCardArgs;?>"
                            data-effect="background" 
                            data-effect-args="<?php echo $entityConfig->keyCollection();?>,<?php echo $entityConfig->getId();?>">
                            <span>
                                <?php echo $entityConfig->getName(true);?>
                            </span>
                            <?php if (null !== $entityDelta):?>
                                <span class="label">
                                    <span>
                                        <?php switch ($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_TYPE]): 
                                            case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_UNIT:
                                            case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_SHIP:
                                        ?>
                                            &plus;<b><?php echo $entityDelta;?></b>
                                        <?php break; default:?>
                                            &#x25B2; <?php echo esc_html__('Level', 'stephino-rpg');?> <b><?php echo $entityDelta;?></b>
                                        <?php break; endswitch;?>
                                    </span>
                                </span>
                            <?php endif;?>
                        </div>
                        <div class="col-12 text-center">
                            <span 
                                data-effect="countdownTime" 
                                data-effect-args="<?php echo ($queueLeft . ',' . $queueTotal);?>">
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-lg-9">
                        <h6>
                            <span 
                                data-effect="help"
                                data-effect-args="<?php echo $entityConfig->keyCollection();?>,<?php echo $entityConfig->getId();?>">
                                <?php echo $entityConfig->getName(true);?>
                            </span>
                        </h6>
                        <div 
                            class="item-level-uc-bar" 
                            title="<?php echo esc_attr__('Progress', 'stephino-rpg');?>" 
                            data-effect="countdownBar" 
                            data-effect-args="<?php echo ($queueLeft . ',' . $queueTotal);?>">
                        </div>
                        <?php switch ($queueRow[Stephino_Rpg_Db_Table_Queues::COL_QUEUE_ITEM_TYPE]): case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_BUILDING: ?>
                            <button 
                                class="btn btn-default w-100" 
                                data-click="buildingUpgradeCancelDialog"
                                data-click-args="<?php echo $entityConfig->getId();?>">
                                <span><b><?php echo esc_html__('Stop', 'stephino-rpg');?></b></span>
                            </button>
                        <?php break; case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_UNIT: case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_SHIP:?>
                            <button 
                                class="btn btn-default w-100"
                                data-click="entityDialog" 
                                data-click-args="<?php echo $entityConfig->keyCollection();?>,<?php echo $entityConfig->getId();?>,<?php echo Stephino_Rpg_Renderer_Ajax_Dialog_Entity::QUEUE_ACTION_DEQUEUE;?>">
                                <span><b><?php echo esc_html__('Cancel', 'stephino-rpg');?></b></span>
                            </button>
                        <?php break; case Stephino_Rpg_Db_Table_Queues::ITEM_TYPE_RESEARCH: ?>
                            <button 
                                class="btn btn-warning w-100" 
                                data-click="researchFieldQueue" 
                                data-click-args="<?php echo $entityConfig->getId();?>,0">
                                <span><b><?php echo esc_html__('Cancel', 'stephino-rpg');?></b></span>
                            </button>
                        <?php break; endswitch;?>
                    </div>
                </div>
            <?php endforeach;?>
        <?php else:?>
            <div class="col-12 text-center">
                <?php echo esc_html__('The queue is empty', 'stephino-rpg');?>
            </div>
        <?php endif;?>
    </div>
<?php endforeach; ?>