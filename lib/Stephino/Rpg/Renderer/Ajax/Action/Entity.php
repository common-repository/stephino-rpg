<?php

/**
 * Stephino_Rpg_Renderer_Ajax_Action_Entity
 * 
 * @title      Action::Entity
 * @desc       Entity actions
 * @copyright  (c) 2021, Stephino
 * @author     Mark Jivko <stephino.team@gmail.com>
 * @package    stephino-rpg
 * @license    GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */
class Stephino_Rpg_Renderer_Ajax_Action_Entity extends Stephino_Rpg_Renderer_Ajax_Action {

    // Request keys
    const REQUEST_ENTITY_KEY       = 'entityKey';
    const REQUEST_ENTITY_CONFIG_ID = 'entityConfigId';
    const REQUEST_ENTITY_COUNT     = 'entityCount';
    const REQUEST_ENTITY_QUEUE     = 'entityQueue';
    
    /**
     * Queue/dequeue effect preview
     * 
     * @param array $data Data containing <ul>
     * <li><b>cityId</b> (int) City ID</li>
     * <li><b>entityKey</b> (string) Entity Type</li>
     * <li><b>entityConfigId</b> (int) Entity configuration ID</li>
     * </ul>
     */
    public static function ajaxQueuePreview($data) {
        // Get the entity key
        $entityKey = isset($data[self::REQUEST_ENTITY_KEY]) ? trim($data[self::REQUEST_ENTITY_KEY]) : null;
        
        // Get the count
        $entityCount = isset($data[self::REQUEST_ENTITY_COUNT]) ? abs((int) $data[self::REQUEST_ENTITY_COUNT]) : 0;
        
        // Queue/dequeue the entity
        $entityQueue = isset($data[self::REQUEST_ENTITY_QUEUE]) ? !!$data[self::REQUEST_ENTITY_QUEUE] : true;
        
        if ($entityCount > 0) {
            /* @var $entityConfig Stephino_Rpg_Config_Unit|Stephino_Rpg_Config_Ship */
            list(
                $entityData, 
                $entityConfig, 
                $cityData, 
                $buildingData, 
                $queueData, 
                $costData
            ) = self::getEntityInfo(
                isset($data[self::REQUEST_CITY_ID]) ? intval($data[self::REQUEST_CITY_ID]) : null, 
                self::getEntityType($entityKey), 
                isset($data[self::REQUEST_ENTITY_CONFIG_ID]) ? intval($data[self::REQUEST_ENTITY_CONFIG_ID]) : null,
                $entityCount
            );

            if ($entityQueue) {
                // Prepare the recruitment time in seconds
                $costTime = Stephino_Rpg_Db::get()->modelEntities()->getRecruitTime(
                    $entityConfig, 
                    $entityCount, 
                    $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_LEVEL]
                );

                // Prepare the time contraction
                $costTimeContraction = self::getTimeContraction($entityConfig);

                // Set the title
                $costTitle = $entityConfig instanceof Stephino_Rpg_Config_Unit 
                    ? __('Recruitment cost', 'stephino-rpg') 
                    : __('Construction cost', 'stephino-rpg');
            } else {
                $costTitle = __('Refund', 'stephino-rpg');
                $costRefundMode = true;
                $costRefundPercent = 100;
            }

            // Load the table
            require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_COSTS
            );

            // Show garrison effect in recruit mode
            if ($entityQueue) {
                // Get the entity production data
                $productionData = Stephino_Rpg_Renderer_Ajax_Action::getProductionData(
                    $entityConfig,
                    $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_LEVEL],
                    $cityData[Stephino_Rpg_Db_Table_Cities::COL_CITY_ISLAND_ID],
                    $entityCount
                );
                if (count($productionData)) {
                    $productionTitle = __('Garrison effect', 'stephino-rpg');
                    require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                        Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_PRODUCTION
                    );
                }
                
                // Show the military points
                require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                    Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_ENTITY_MILITARY
                );
            }
        }
        
        // Create the construction queue
        return Stephino_Rpg_Renderer_Ajax::wrap(
            true,
            $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_CITY_ID]
        );
    }
    
    /**
     * Queue an entity
     * 
     * @param array $data Data containing <ul>
     * <li><b>cityId</b> (int) City ID</li>
     * <li><b>entityKey</b> (string) Entity Type</li>
     * <li><b>entityConfigId</b> (int) Entity configuration ID</li>
     * <li><b>entityQueue</b> (boolean) Entity queue/dequeue action</li>
     * </ul>
     */
    public static function ajaxQueue($data) {
        $result = null;
        
        // Get the entity key
        $entityKey = isset($data[self::REQUEST_ENTITY_KEY]) ? trim($data[self::REQUEST_ENTITY_KEY]) : null;
        
        // Get the count
        $entityCount = isset($data[self::REQUEST_ENTITY_COUNT]) ? abs((int) $data[self::REQUEST_ENTITY_COUNT]) : 1;
        
        // Queue/dequeue the entity
        $entityQueue = isset($data[self::REQUEST_ENTITY_QUEUE]) ? !!$data[self::REQUEST_ENTITY_QUEUE] : true;
        
        /* @var $entityConfig Stephino_Rpg_Config_Unit|Stephino_Rpg_Config_Ship */
        list(
            $entityData, 
            $entityConfig, 
            $cityData, 
            $buildingData, 
            $queueData, 
            $costData, 
            $productionData, 
            $affordList
        ) = self::getEntityInfo(
            isset($data[self::REQUEST_CITY_ID]) ? intval($data[self::REQUEST_CITY_ID]) : null, 
            self::getEntityType($entityKey), 
            isset($data[self::REQUEST_ENTITY_CONFIG_ID]) ? intval($data[self::REQUEST_ENTITY_CONFIG_ID]) : null,
            $entityCount
        );
        
        // Queue action
        if ($entityQueue) {
            // Validate costs
            if (count($affordList) && $entityCount > min($affordList)) {
                throw new Exception(__('Not enough resources', 'stephino-rpg'));
            }

            // Check max queue for entities
            Stephino_Rpg_Db::get()->modelQueues()->validateMaxQueue(
                $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_USER_ID], 
                $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_CITY_ID], 
                Stephino_Rpg_Db_Model_Entities::NAME
            );
        
            // Spend resources for 1 x (block cost for $entityCount)
            self::spend($costData, $cityData, 1, $entityConfig);

            // Enqueue entity
            $result = Stephino_Rpg_Db::get()->modelQueues()->queueEntity(
                $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID], 
                self::getEntityType($entityKey),
                $entityConfig->getId(), 
                $entityCount
            );
        } else {
            // Validate coount
            $entityCountMax = (null !== $queueData ? (int) $queueData[self::DATA_QUEUE_QUANTITY] : 0);
            if ($entityCount > $entityCountMax) {
                $entityCount = $entityCountMax;
            }
            if ($entityCount <= 0) {
                throw new Exception(
                    sprintf(
                        __('%s: No entities left in the queue', 'stephino-rpg'),
                        $entityConfig->getName()
                    )
                );
            }
            
            // Dequeue entity
            $result = Stephino_Rpg_Db::get()->modelQueues()->queueEntity(
                $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID], 
                self::getEntityType($entityKey),
                $entityConfig->getId(), 
                $entityCount,
                true, 
                false
            );
            
            // Get the updated cost for our canceled units
            $costData = self::getCostData(
                $entityConfig,
                null === $buildingData ? 0 : $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_LEVEL] - 1,
                true,
                $entityCount
            );
            
            // Refund resources for 1 x (block cost for $entityCount)
            self::spend($costData, $cityData, 1, $entityConfig, true);
        }
        
        // Wrap the recruitment queue
        return Stephino_Rpg_Renderer_Ajax::wrap(
            $result,
            $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID]
        );
    }
    
    /**
     * Disband effect preview
     * 
     * @param array $data Data containing <ul>
     * <li><b>cityId</b> (int) City ID</li>
     * <li><b>entityKey</b> (string) Entity Type</li>
     * <li><b>entityConfigId</b> (int) Entity configuration ID</li>
     * </ul>
     */
    public static function ajaxDisbandPreview($data) {
        // Get the entity key
        $entityKey = isset($data[self::REQUEST_ENTITY_KEY]) ? trim($data[self::REQUEST_ENTITY_KEY]) : null;
        
        // Get the count
        $entityCount = isset($data[self::REQUEST_ENTITY_COUNT]) ? abs((int) $data[self::REQUEST_ENTITY_COUNT]) : 0;
        
        if ($entityCount > 0) {
            /* @var $entityConfig Stephino_Rpg_Config_Unit|Stephino_Rpg_Config_Ship */
            list(
                $entityData, 
                $entityConfig, 
                $cityData, 
                $buildingData, 
                $queueData, 
                $costData
            ) = self::getEntityInfo(
                isset($data[self::REQUEST_CITY_ID]) ? intval($data[self::REQUEST_CITY_ID]) : null, 
                self::getEntityType($entityKey), 
                isset($data[self::REQUEST_ENTITY_CONFIG_ID]) ? intval($data[self::REQUEST_ENTITY_CONFIG_ID]) : null,
                $entityCount
            );

            // Get the entity production data
            $productionData = Stephino_Rpg_Renderer_Ajax_Action::getProductionData(
                $entityConfig,
                $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_LEVEL],
                $cityData[Stephino_Rpg_Db_Table_Cities::COL_CITY_ISLAND_ID],
                $entityCount
            );
            if (count($productionData)) {
                $productionRefundMode = true;
                $productionTitle = __('Garrison effect', 'stephino-rpg');
                require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                    Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_PRODUCTION
                );
            }
            
            // Show the military points
            $entityDisbandMode = true;
            require Stephino_Rpg_Renderer_Ajax_Dialog::dialogTemplatePath(
                Stephino_Rpg_Renderer_Ajax_Dialog::TEMPLATE_COMMON_ENTITY_MILITARY
            );
        }
        
        // Create the construction queue
        return Stephino_Rpg_Renderer_Ajax::wrap(
            true,
            $buildingData[Stephino_Rpg_Db_Table_Buildings::COL_BUILDING_CITY_ID]
        );
    }
    
    /**
     * Queue an entity
     * 
     * @param array $data Data containing <ul>
     * <li><b>cityId</b> (int) City ID</li>
     * <li><b>entityKey</b> (string) Entity Type</li>
     * <li><b>entityConfigId</b> (int) Entity configuration ID</li>
     * </ul>
     */
    public static function ajaxDisband($data) {
        // Get the entity key
        $entityKey = isset($data[self::REQUEST_ENTITY_KEY]) ? trim($data[self::REQUEST_ENTITY_KEY]) : null;
        
        // Get the count
        $entityCount = isset($data[self::REQUEST_ENTITY_COUNT]) ? abs((int) $data[self::REQUEST_ENTITY_COUNT]) : 1;
        
        /* @var $entityConfig Stephino_Rpg_Config_Unit|Stephino_Rpg_Config_Ship */
        list(
            $entityData, 
            $entityConfig, 
            $cityData
        ) = self::getEntityInfo(
            isset($data[self::REQUEST_CITY_ID]) ? intval($data[self::REQUEST_CITY_ID]) : null, 
            self::getEntityType($entityKey), 
            isset($data[self::REQUEST_ENTITY_CONFIG_ID]) ? intval($data[self::REQUEST_ENTITY_CONFIG_ID]) : null,
            $entityCount
        );
        
        // Action not allowed
        if (!$entityConfig->getDisbandable()) {
            throw new Exception(
                sprintf(
                    __('%s: Cannot disband', 'stephino-rpg'),
                    $entityConfig->getName()
                )
            );
        }
        
        // Validate the count
        if ($entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT] < $entityCount) {
            throw new Exception(
                sprintf(
                    __('%s: No entities left', 'stephino-rpg'),
                    $entityConfig->getName()
                )
            );
        }
        
        // Prepare the entities update result
        $result = Stephino_Rpg_Db::get()->modelEntities()->set(
            $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID], 
            $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_TYPE], 
            $entityConfig->getId(), 
            $entityData[Stephino_Rpg_Db_Table_Entities::COL_ENTITY_COUNT] - $entityCount
        );
        
        // A population reward was set
        if ($entityConfig->getDisbandablePopulation() > 0) {
            Stephino_Rpg_Db::get()->tableCities()->updateById(
                array(
                    Stephino_Rpg_Db_Table_Cities::COL_CITY_METRIC_POPULATION => 
                        $cityData[Stephino_Rpg_Db_Table_Cities::COL_CITY_METRIC_POPULATION] 
                        + ($entityCount * $entityConfig->getDisbandablePopulation())
                ),
                $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID]
            );
        }
        
        // Wrap the recruitment queue
        return Stephino_Rpg_Renderer_Ajax::wrap(
            $result,
            $cityData[Stephino_Rpg_Db_Table_Cities::COL_ID]
        );
    }
    
    /**
     * Get the DB entity type
     * 
     * @param int $entityKey Entity Key, one of <ul>
     *     <li>Stephino_Rpg_Config_Units::KEY</li>
     *     <li>Stephino_Rpg_Config_Ships::KEY</li>
     * </ul>
     * @return string
     */
    public static function getEntityType($entityKey) {
        // Get the entity type
        $entityType = null;
        
        switch ($entityKey) {
            case Stephino_Rpg_Config_Units::KEY:
                $entityType = Stephino_Rpg_Db_Table_Entities::ENTITY_TYPE_UNIT;
                break;
            
            case Stephino_Rpg_Config_Ships::KEY:
                $entityType = Stephino_Rpg_Db_Table_Entities::ENTITY_TYPE_SHIP;
                break;
        }
        
        return $entityType;
    }
}

/*EOF*/