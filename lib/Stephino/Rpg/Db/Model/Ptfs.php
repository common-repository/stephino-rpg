<?php
/**
 * Stephino_Rpg_Db_Model_Platformers
 * 
 * @title     Model:Platformers
 * @desc      Platformers Model
 * @copyright (c) 2021, Stephino
 * @author    Mark Jivko <stephino.team@gmail.com>
 * @package   stephino-rpg
 * @license   GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */

class Stephino_Rpg_Db_Model_Ptfs extends Stephino_Rpg_Db_Model {

    /**
     * Platformers Model Name
     */
    const NAME = 'ptfs';

    /**
     * Tile: side length in pixels
     */
    const TILE_SIDE = 64;
    
    /**
     * Tile set: width in tiles
     */
    const TILE_SET_HORIZONTAL = 11;
    
    /**
     * Tile set: height in tiles
     */
    const TILE_SET_VERTICAL = 4;
    
    /**
     * Pagination: items per page
     */
    const PAGINATION_ITEMS_PER_PAGE = 8;
    
    // Platformer definition
    const PTF_DEF_NAME    = 'name';
    const PTF_DEF_VERSION = 'version';
    const PTF_DEF_WIDTH   = 'width';
    const PTF_DEF_HEIGHT  = 'height';
    
    // Extra columns
    const PTF_EXTRA_REWARD  = 'ptf_x_reward';
    const PTF_EXTRA_PREVIEW = 'ptf_x_preview';
    
    // Minimum dimensions
    const PTF_MIN_WIDTH  = 26;
    const PTF_MIN_HEIGHT = 15;
    const PTF_MAX_WIDTH  = 250;
    const PTF_MAX_HEIGHT = 150;
    
    /**
     * Create a platformer with this author; checks the author limit
     * 
     * @param int $userId User ID
     * @return int|null New Ptf ID or Null on error
     * @throws Exception
     */
    public function create($userId) {
        $userId = abs((int) $userId);
        if (0 === $userId) {
            throw new Exception(__('Invalid author ID', 'stephino-rpg'));
        }
        
        // Player authorship limits
        if (!Stephino_Rpg_Cache_User::get()->isElevated(Stephino_Rpg_Cache_User::PERM_MOD_PTFS)) {
            // Not allowed to create mini-games
            if (0 === Stephino_Rpg_Config::get()->core()->getPtfAuthorLimit()) {
                throw new Exception(__('You cannot create games', 'stephino-rpg'));
            }

            // Total platformers limit
            $authorPlatformers = $this->getDb()
                ->tablePtfs()
                ->getByUserId($userId);
            if (count($authorPlatformers) >= Stephino_Rpg_Config::get()->core()->getPtfAuthorLimit()) {
                throw new Exception(__('You cannot create more games', 'stephino-rpg'));
            }
        }
        
        // Prepare the platformer name
        $ptfName = Stephino_Rpg_Utils_Lingo::generateIslandName();
        if (strlen($ptfName) < 3 || strlen($ptfName) > 64) {
            $ptfName = md5(mt_rand(1, 999));
        }
        
        // Compressed tile set
        $tileSetC = Stephino_Rpg_Utils_Math::getIntListZip(
            array_fill(
                0, 
                self::PTF_MIN_WIDTH * self::PTF_MIN_HEIGHT, 
                1
            )
        );
            
        // Sanitize the platformer definition and tile set
        $ptfDef = $this->sanitize(
            array(
                self::PTF_DEF_VERSION => 1,
                self::PTF_DEF_NAME    => $ptfName,
                self::PTF_DEF_WIDTH   => self::PTF_MIN_WIDTH,
                self::PTF_DEF_HEIGHT  => self::PTF_MIN_HEIGHT,
            ), 
            $tileSetC
        );
        
        // Get the platformer ID
        return $this->getDb()->tablePtfs()->create(
            $userId, 
            $ptfDef[self::PTF_DEF_NAME], 
            $ptfDef[self::PTF_DEF_WIDTH], 
            $ptfDef[self::PTF_DEF_HEIGHT], 
            $tileSetC
        );
    }
    
    /**
     * Delete all platformers belonging to this user
     * 
     * @param int $userId User ID
     * @return int|false The number of rows deleted or false on error
     */
    public function deleteByUser($userId) {
        return $this->getDb()->tablePtfs()->deleteByUser($userId);
    }
    
    /**
     * Mark the game as started of finished, updating the corresponding fields; must be used in Time-Lapse mode
     * 
     * @param int     $ptfId    Platformer Id
     * @param boolean $ptfStart (optional) Start/Finish; default <b>true</b>
     * @param boolean $ptfWon   (optional) When finishing a game, mark it as a win; default <b>false</b>
     * @throws Exception
     */
    public function play($ptfId, $ptfStart = true, $ptfWon = false) {
        if (Stephino_Rpg_TimeLapse::get()->userId()) {
            if (is_array($ptfRow = $this->getDb()->tablePtfs()->getById($ptfId))) {
                // Get the user cache
                $userCache = Stephino_Rpg_Cache_User::get()->data();
                
                // Get the PTF data
                $ptfData = isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA])
                    ? (
                        is_array($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA]) 
                            ? $userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA] 
                            : array()
                    )
                    : array();
                
                // Get the PTF time
                $ptfTime = isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME])
                    ? abs((int) $userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME])
                    : 0;
                
                // Time to reset
                if (Stephino_Rpg_Config::get()->core()->getPtfRewardResetHours() > 0) {
                    $currentTime = time();
                    if (($currentTime - $ptfTime) >= Stephino_Rpg_Config::get()->core()->getPtfRewardResetHours() * 3600) {
                        $ptfData = array();
                        $ptfTime = $currentTime;
                    }
                }
                
                // Never played the game
                if (!isset($ptfData[$ptfId])) {
                    if(!$ptfStart) {
                        throw new Exception(__('Cannot finish this game', 'stephino-rpg'));
                    }
                    
                    // Start a new play
                    $ptfData[$ptfId] = Stephino_Rpg_Cache_User::PTF_DATA_STARTED;
                } else {
                    // Mark only if not already successful
                    if (!$ptfStart && Stephino_Rpg_Cache_User::PTF_DATA_WON != $ptfData[$ptfId]) {
                        $ptfData[$ptfId] = $ptfWon
                            ? Stephino_Rpg_Cache_User::PTF_DATA_WON
                            : Stephino_Rpg_Cache_User::PTF_DATA_LOST;
                    }
                }
                
                // Update the PTF cache
                Stephino_Rpg_Cache_User::get()->write(Stephino_Rpg_Cache_User::KEY_PTF_DATA, $ptfData);
                if (!isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME]) 
                    || $ptfTime != $userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME]) {
                    Stephino_Rpg_Cache_User::get()->write(Stephino_Rpg_Cache_User::KEY_PTF_TIME, $ptfTime);
                }
                Stephino_Rpg_Cache_User::get()->commit();
                
                // Update stats for public, approved games only
                if (Stephino_Rpg_Db_Table_Ptfs::PTF_VISIBILITY_PUBLIC === $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VISIBILITY]
                    && Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED === $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_REVIEW]) {
                    // Prepare the user update
                    if (!$ptfStart) {
                        $userData = Stephino_Rpg_TimeLapse::get()->userData();
                        
                        // Update the user
                        $userUpdate = array(
                            Stephino_Rpg_Db_Table_Users::COL_USER_PTF_PLAYED => $userData[Stephino_Rpg_Db_Table_Users::COL_USER_PTF_PLAYED] + 1
                        );

                        // Mark the victory
                        if ($ptfWon) {
                            $userUpdate[Stephino_Rpg_Db_Table_Users::COL_USER_PTF_WON] = $userData[Stephino_Rpg_Db_Table_Users::COL_USER_PTF_WON] + 1;
                        }

                        // Update the user stats
                        $this->getDb()->tableUsers()->updateById($userUpdate, Stephino_Rpg_TimeLapse::get()->userId());
                    }

                    // Update the platformer
                    $ptfUpdate = $ptfStart
                        ? array(
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_STARTED => $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_STARTED] + 1,
                        )
                        : array(
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED => $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED] + 1,
                        );

                    // Mark the victory
                    if (!$ptfStart && $ptfWon) {
                        $ptfUpdate[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED_WON] = $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED_WON] + 1;
                    }

                    // Update the PTF stats
                    $this->getDb()->tablePtfs()->updateById($ptfUpdate, $ptfId);
                }
            }
        }
    }
    
    /**
     * Reward a user; if <b>ptfId</b> and <b>playerId</b> are both set, the user is considered an author and a diplomacy message is sent
     * 
     * @param int $userId     User ID
     * @param int $rewardGems Gems reward
     * @param int $ptfId      (optional) Platformer ID; when specified, send a message to the user (author royalties); default <b>null</b>
     * @param int $playerId   (optional) Player ID; used for the royalties message; default <b>null</b>
     */
    public function reward($userId, $rewardGems, $ptfId = null, $playerId = null) {
        $userId = abs((int) $userId);
        $rewardGems = abs((int) $rewardGems);
        
        // Valid user ID
        if ($userId > 0 && $rewardGems > 0) {
            $userRow = $this->getDb()->tableUsers()->getById($userId);
            if (is_array($userRow)) {
                // Get the current gems count
                $userGems = (int) $userRow[Stephino_Rpg_Db_Table_Users::COL_USER_RESOURCE_GEM];
                
                do {
                    // Author royalties
                    if (null !== $ptfId && null !== $playerId) {
                        $playerId = abs((int) $playerId);
                        
                        // Don't send author royalties to yourself
                        if ($userId === $playerId) {
                            break;
                        }
                        
                        // Valid game
                        if (is_array($ptfRow = $this->getDb()->tablePtfs()->getById($ptfId))) {
                            // Get the player name
                            $playerName = Stephino_Rpg_Utils_Lingo::getUserName(
                                $this->getDb()->tableUsers()->getById($playerId)
                            );

                            // Send the notification
                            $this->getDb()->modelMessages()->notify(
                                $userId, 
                                Stephino_Rpg_Db_Model_Messages::TEMPLATE_NOTIF_PTF_AUTHOR_REWARD,
                                array(
                                    // ptfId
                                    $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_ID], 
                                    // ptfName
                                    $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_NAME], 
                                    $playerId, 
                                    $playerName, 
                                    $rewardGems
                                ),
                                Stephino_Rpg_Db_Table_Messages::MESSAGE_TYPE_ECONOMY
                            );
                        }
                    }
                    
                    // Add the reward
                    $this->getDb()->tableUsers()->updateById(
                        array(
                            Stephino_Rpg_Db_Table_Users::COL_USER_RESOURCE_GEM => $userGems + $rewardGems
                        ), 
                        $userId
                    );
                } while(false);
            }
        }
    }
    
    /**
     * Sanitize the platformer definition for the provide tile set
     * 
     * @param array   $ptfDef    Associative array containing the following keys:<ul>
     * <li><b>Stephino_Rpg_Db_Model_Platformers::PTF_DEF_NAME</b>    => (string) Platformer name [3,64] characters long</li>
     * <li><b>Stephino_Rpg_Db_Model_Platformers::PTF_DEF_VERSION</b> => (int) Platformer version [1,]</li>
     * <li><b>Stephino_Rpg_Db_Model_Platformers::PTF_DEF_WIDTH</b>   => (int) Platformer width [self::PTF_MIN_WIDTH,self::PTF_MAX_WIDTH]</li>
     * <li><b>Stephino_Rpg_Db_Model_Platformers::PTF_DEF_HEIGHT</b>  => (int) Platformer height [self::PTF_MIN_HEIGHT,self::PTF_MAX_HEIGHT]</li>
     * </ul>
     * @param array   $tileSetC  <b>Compressed</b> unassociative array of integers
     * @param boolean $checkGate (optional) Fail if the gate tile was not placed; default <b>false</b>
     * @return array Sanitized platformer definition
     * @throws Exception
     */
    public function sanitize($ptfDef, $tileSetC, $checkGate = false) {
        // Pre-validate the array
        if (!is_array($ptfDef) 
            || !isset($ptfDef[self::PTF_DEF_NAME])
            || !isset($ptfDef[self::PTF_DEF_VERSION])
            || !isset($ptfDef[self::PTF_DEF_WIDTH])
            || !isset($ptfDef[self::PTF_DEF_HEIGHT])) {
            throw new Exception(__('Incomplete definition', 'stephino-rpg'));
        }

        // Validate platformer name
        $preDefPtfName = trim(preg_replace('%[^\w\- ]+%i', '', $ptfDef[self::PTF_DEF_NAME]));
        if (strlen($preDefPtfName) < 3 || strlen($preDefPtfName) > 64) {
            throw new Exception(__('Name must be between 3 and 64 characters long', 'stephino-rpg'));
        }

        // Validate version
        $preDefPtfVersion = abs((int) $ptfDef[self::PTF_DEF_VERSION]);
        if ($preDefPtfVersion < 1) {
            throw new Exception(__('Version must be greater than 1', 'stephino-rpg'));
        }

        // Validate width
        $preDefPtfWidth  = abs((int) $ptfDef[self::PTF_DEF_WIDTH]);
        if ($preDefPtfWidth < self::PTF_MIN_WIDTH) {
            throw new Exception(
                sprintf(
                    __('Width must be greater than %d', 'stephino-rpg'),
                    self::PTF_MIN_WIDTH
                )
            );
        }
        
        // Validate height
        $preDefPtfHeight = abs((int) $ptfDef[self::PTF_DEF_HEIGHT]);
        if ($preDefPtfHeight < self::PTF_MIN_HEIGHT) {
            throw new Exception(
                sprintf(
                    __('Height must be greater than %d', 'stephino-rpg'),
                    self::PTF_MIN_HEIGHT
                )
            );
        }

        // Validate the tile data
        if (!is_array($tileSetC)) {
            throw new Exception(__('Invalid tile set data', 'stephino-rpg'));
        }
        
        // Decompress the tile set
        $tileSetC = Stephino_Rpg_Utils_Math::getIntListZip($tileSetC, false);
        
        // Sanitize the tile data; tile IDs must be between 1 and W*H
        $tileSetC = array_values(array_filter(array_map('intval', $tileSetC), function($pieceId) {
            return $pieceId >= 1 && $pieceId <= self::TILE_SET_HORIZONTAL * self::TILE_SET_VERTICAL;
        }));
        
        // No gate tile placed
        if ($checkGate && !in_array(20, $tileSetC)) {
            throw new Exception(__('You must place at least one gate', 'stephino-rpg'));
        }

        // Invalid number of pieces
        if (count($tileSetC) !== $preDefPtfWidth * $preDefPtfHeight) {
            throw new Exception(
                sprintf(
                    __('Invalid tile set size (%d instead of %d)', 'stephino-rpg'),
                    count($tileSetC),
                    $preDefPtfWidth * $preDefPtfHeight
                )
            );
        }
        
        return array(
            self::PTF_DEF_NAME    => $preDefPtfName,
            self::PTF_DEF_VERSION => $preDefPtfVersion,
            self::PTF_DEF_WIDTH   => $preDefPtfWidth,
            self::PTF_DEF_HEIGHT  => $preDefPtfHeight,
        );
    }
    
    /**
     * Store/update the references to pre-defined platformers in the database<br/>
     * Performs multi-updates, multi-inserts and multi-deletes as necessary
     * 
     * Callable only once per thread
     */
    public function reload() {
        do {
            // Get the platformers definition
            $preDefPlatformersData = null;
            if (is_file($ptfsDataPath = STEPHINO_RPG_ROOT . '/' . Stephino_Rpg::FOLDER_UI_JS . '/ptf/' . Stephino_Rpg_Renderer_Ajax::FILE_PTF_LIST . '.json')) {
                $preDefPlatformersData = @json_decode(file_get_contents($ptfsDataPath), true);
            }
            if (!is_array($preDefPlatformersData)) {
                Stephino_Rpg_Log::check() && Stephino_Rpg_Log::warning(
                    "Db_Model_Ptfs.reload: " . Stephino_Rpg_Renderer_Ajax::FILE_PTF_LIST . '.json file is not a valid associative array'
                );
                break;
            }
            
            // Clean-up the platformers data
            foreach ($preDefPlatformersData as $preDefPtfId => $preDefPtfDef) {
                try {
                    $preDefPtfId = abs((int) $preDefPtfId);
                    
                    // No definition found
                    if (!is_file($preDefPtfPath = STEPHINO_RPG_ROOT . '/' . Stephino_Rpg::FOLDER_UI_JS . '/ptf/' . Stephino_Rpg_Renderer_Ajax::FILE_PTF_LIST . '/' . $preDefPtfId . '.json')) {
                        throw new Exception('JSON data file missing');
                    }
                    
                    // Sanitize the data
                    $preDefPlatformersData[$preDefPtfId] = $this->sanitize(
                        $preDefPtfDef, 
                        @json_decode(file_get_contents($preDefPtfPath), true)
                    );
                } catch (Exception $exc) {
                    Stephino_Rpg_Log::check() && Stephino_Rpg_Log::warning(
                        "Db_Model_Ptfs.reload: {$exc->getMessage()}"
                    );
                    unset($preDefPlatformersData[$preDefPtfId]);
                }
            }
            
            // Prepare the DB changes
            $dbInserts = array();
            $dbUpdates = array();
            $dbDeletes = array();
            $timestamp = time();
            
            // Store {pre-defined platformer ID} => {database ID} association from the database
            $dbPreDefPtfIds = array();
            
            // Store {pre-defined platformer ID} => {database version} association from the database
            $dbPreDefPtfVer = array();
            
            // Get the pre-defined platformers from the database
            foreach ($this->getDb()->tablePtfs()->getAll(true) as $dbRow) {
                // Sanitize the pre-defined platformer ID
                $dbPreDefPtfId = abs((int) $dbRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_CONTENT]);
                
                // Store the database ID
                $dbPreDefPtfIds[$dbPreDefPtfId] = (int) $dbRow[Stephino_Rpg_Db_Table_Ptfs::COL_ID];
                $dbPreDefPtfVer[$dbPreDefPtfId] = (int) $dbRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VERSION];
                
                // DB platformer removed from the pre-defined list or invalid content
                if (!isset($preDefPlatformersData[$dbPreDefPtfId])) {
                    $dbDeletes[] = (int) $dbPreDefPtfIds[$dbPreDefPtfId];
                }
            }
            
            // Parse the changes
            foreach ($preDefPlatformersData as $preDefPtfId => $preDefPtfDef) {
                // Sanitize the platformer ID
                $preDefPtfId = (int) $preDefPtfId;
                
                // New platformer
                if (!isset($dbPreDefPtfIds[$preDefPtfId])) {
                    $dbInserts[] = array(
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_USER_ID       => 0,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_CONTENT       => $preDefPtfId,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_WIDTH         => $preDefPtfDef[self::PTF_DEF_WIDTH],
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_HEIGHT        => $preDefPtfDef[self::PTF_DEF_HEIGHT],
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_NAME          => $preDefPtfDef[self::PTF_DEF_NAME],
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VERSION       => $preDefPtfDef[self::PTF_DEF_VERSION],
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_CREATED_TIME  => $timestamp,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_MODIFIED_TIME => $timestamp,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VISIBILITY    => Stephino_Rpg_Db_Table_Ptfs::PTF_VISIBILITY_PUBLIC,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_REVIEW        => Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_RATING        => 5,
                        Stephino_Rpg_Db_Table_Ptfs::COL_PTF_RATING_COUNT  => 1,
                    );
                } else {
                    // Version change
                    if ($dbPreDefPtfVer[$preDefPtfId] != $preDefPtfDef[self::PTF_DEF_VERSION]) {
                        $dbUpdates[$dbPreDefPtfIds[$preDefPtfId]] = array(
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_WIDTH         => $preDefPtfDef[self::PTF_DEF_WIDTH],
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_HEIGHT        => $preDefPtfDef[self::PTF_DEF_HEIGHT],
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_NAME          => $preDefPtfDef[self::PTF_DEF_NAME],
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VERSION       => $preDefPtfDef[self::PTF_DEF_VERSION],
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_MODIFIED_TIME => $timestamp,
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VISIBILITY    => Stephino_Rpg_Db_Table_Ptfs::PTF_VISIBILITY_PUBLIC,
                            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_REVIEW        => Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED,
                        );
                    }
                }
            }
            
            // Execute the queries
            if (count($dbInserts) && null !== $multiInsertQuery = Stephino_Rpg_Utils_Db::multiInsert(
                $this->getDb()->tablePtfs()->getTableName(),
                $dbInserts
            )) {
                $this->getDb()->getWpDb()->query($multiInsertQuery);
            }
            
            if (count($dbUpdates) && null !== $multiUpdateQuery = Stephino_Rpg_Utils_Db::multiUpdate(
                $this->getDb()->tablePtfs()->getTableName(), 
                Stephino_Rpg_Db_Table_Ptfs::COL_ID,
                $dbUpdates
            )) {
                $this->getDb()->getWpDb()->query($multiUpdateQuery);
            }
            
            if (count($dbDeletes) && null !== $multiDeleteQuery = Stephino_Rpg_Utils_Db::multiDelete(
                $this->getDb()->tablePtfs()->getTableName(), 
                Stephino_Rpg_Db_Table_Ptfs::COL_ID,
                $dbDeletes
            )) {
                $this->getDb()->getWpDb()->query($multiDeleteQuery);
            }
            
        } while(false);
    }
    
    /**
     * Get the list of available tiles, i18n ready and HTML escaped
     * 
     * @return array List of tiles as {tile ID} => [{tile name}, {tile description}]
     */
    public function getTileList() {
        return array(
            1 => array(
                esc_html__('Void', 'stephino-rpg'),
                esc_html__('An empty slot', 'stephino-rpg'),
            ),
            8 => array(
                esc_html__('Pipe', 'stephino-rpg'),
                esc_html__('A solid structure to stand on', 'stephino-rpg')
            ),
            18 => array(
                esc_html__('Crystal', 'stephino-rpg'),
                esc_html__('A rare item that must be collected to open the gate', 'stephino-rpg')
            ),
            19 => array(
                esc_html__('Bomb', 'stephino-rpg'),
                esc_html__('Instant death', 'stephino-rpg')
            ),
            20 => array(
                esc_html__('Gate', 'stephino-rpg'),
                esc_html__('Escape the current level with your loot', 'stephino-rpg')
            ),
        );
    }
    
    /**
     * Get the sanitized tile set from the specified platformer row
     * 
     * @param array   $ptfRow  Platformer DB Row
     * @param boolean $preview (optional) Preview Mode: get the top-left square; default <b>false</b>
     * @param boolean $compressed (optional) Compress the tile set; default <b>false</b>
     * @return array|null
     */
    public function getTileSet($ptfRow, $preview = false, $compressed = false) {
        // Prepare the tile data
        $tileSet = null;

        // This is a pre-defined platformer
        if (0 === abs((int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_USER_ID])) {
            // Prepare the file name
            $preDefPtfId = abs((int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_CONTENT]);

            // Store the enclosed data
            if (is_file($preDefPtfPath = STEPHINO_RPG_ROOT . '/' . Stephino_Rpg::FOLDER_UI_JS . '/ptf/' . Stephino_Rpg_Renderer_Ajax::FILE_PTF_LIST . '/' . $preDefPtfId . '.json')) {
                $tileSet = @json_decode(file_get_contents($preDefPtfPath), true);
            }
        } else {
            $tileSet = @json_decode($ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_CONTENT], true);
        }
        
        // Sanitize the content; pieces must be between 1 and W*H
        if (is_array($tileSet)) {
            // Decomporess
            $tileSet = Stephino_Rpg_Utils_Math::getIntListZip($tileSet, false);
            
            // Cast to int and validate range
            $tileSet = array_values(array_filter(array_map('intval', $tileSet), function($pieceId) {
                return $pieceId >= 1 && $pieceId <= self::TILE_SET_HORIZONTAL * self::TILE_SET_VERTICAL;
            }));
            
            // Preview mode
            if ($preview) {
                // Prepare the square preview
                $previewTileSet = array();
                
                // Get the current platformer width
                $ptfWidth  = (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_WIDTH];
                
                // Only keep the top-left square
                for ($i = 0; $i < self::PTF_MIN_HEIGHT; $i++) {
                    for ($j = 0; $j < self::PTF_MIN_HEIGHT; $j++) {
                        $previewTileSet[] = $tileSet[$i * $ptfWidth + $j];
                    }
                }
                $tileSet = $previewTileSet;
            }
            
            // Compress
            if ($compressed) {
                $tileSet = Stephino_Rpg_Utils_Math::getIntListZip($tileSet);
            }
        }
        
        return $tileSet;
    }
    
    /**
     * Get the platformer tile map as expected by the Phaser script<br/>
     * Tile data is sanitized and validated
     * 
     * @param int $ptfId Platformer ID
     * @return array|null
     */
    public function getTileMap($ptfId) {
        $ptfTileMap = null;
        
        do {
            // Get the platformer data
            if (!is_array($ptfRow = $this->getDb()->tablePtfs()->getById($ptfId))) {
                break;
            }
            
            // Preliminary check
            if (!is_array($tileSet = $this->getTileSet($ptfRow))) {
                break;
            }
            
            // Sanitize the platformer width and height
            $ptfWidth  = (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_WIDTH];
            $ptfHeight = (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_HEIGHT];

            // Invalid number of pieces
            if (count($tileSet) !== $ptfWidth * $ptfHeight) {
                break;
            }
            
            // Prepare the final platformer configuration array
            $ptfTileMap = array(
                'version'     => (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_VERSION],
                'width'       => $ptfWidth,
                'height'      => $ptfHeight,
                'orientation' => 'orthogonal',
                'tilewidth'   => self::TILE_SIDE,
                'tileheight'  => self::TILE_SIDE,
                'layers'      => array(
                    array(
                        'name'    => 'stephino-rpg-layer',
                        'data'    => $tileSet,
                        'width'   => $ptfWidth,
                        'height'  => $ptfHeight,
                        'opacity' => 1,
                        'type'    => 'tilelayer',
                        'visible' => true,
                        'x'       => 0,
                        'y'       => 0,
                    )
                ),
                'tilesets'    => array(
                    array(
                        'name'        => 'stephino-rpg-tiles',
                        'image'       => Stephino_Rpg_Utils_Themes::getActive()->getFileUrl(
                            Stephino_Rpg_Theme::FOLDER_IMG_UI . '/' . Stephino_Rpg_Db_Model_Ptfs::NAME . '/ptf-tiles.png?ver=' . Stephino_Rpg::PLUGIN_VERSION
                        ),
                        'imagewidth'  => self::TILE_SIDE * self::TILE_SET_HORIZONTAL,
                        'imageheight' => self::TILE_SIDE * self::TILE_SET_VERTICAL,
                        'firstgid'    => 1,
                        'margin'      => 0,
                        'spacing'     => 0,
                        'tilewidth'   => self::TILE_SIDE,
                        'tileheight'  => self::TILE_SIDE,
                    )
                )
            );
        } while(false);
        
        return $ptfTileMap;
    }
    
    /**
     * Get the view box size in pixels
     * 
     * @param boolean $getString (optional) Get the result as a string "{width} {height}"; default <b>false</b>
     * @return int[]|string array({width}, {height}) OR "{width} {height}"
     */
    public function getViewBox($getString = false) {
        $viewBox = array(
            self::TILE_SIDE * self::PTF_MIN_WIDTH,
            self::TILE_SIDE * self::PTF_MIN_HEIGHT,
        );
        return $getString ? implode(' ', $viewBox) : $viewBox;
    }

    /**
     * Get the next platformer ID
     * 
     * @param int $userId User ID
     * @param int $ptfId  Platformer ID
     * @return int|null Null if no results found
     */
    public function getNextId($userId, $ptfId) {
        $result = null;
        
        // Get the platformers for this user
        $ptfs = $this->getDb()->tablePtfs()->getForUserId($userId);
        
        do {
            // Nothing found
            if (!count($ptfs)) {
                break;
            }

            // Get the IDs only
            $ptfIds = array_values(
                array_map(
                    function($item) {
                        return (int) $item[Stephino_Rpg_Db_Table_Ptfs::COL_ID];
                    }, 
                    $ptfs
                )
            );

            // Get the current game key
            $ptfKey = array_search($ptfId, $ptfIds);

            // Next
            $newPtfKey = (int) $ptfKey + 1;

            // Key not found or reached the end
            if (false === $ptfKey || $newPtfKey >= count($ptfIds)) {
                $newPtfKey = 0;
            }
            
            $result = $ptfIds[$newPtfKey];
        } while(false);
        
        return $result;
    }
    
    /**
     * Is this user suspended?
     * 
     * @param int     $playerId      User ID
     * @param boolean $playerIsAdmin (optional) User is a site admin; default <b>false</b>
     * @return boolean
     */
    public function playerIsSuspended($playerId, $playerIsAdmin = false) {
        return !$playerIsAdmin 
            && $this->getDb()->tablePtfs()->getCountSuspended($playerId) 
                >= Stephino_Rpg_Config::get()->core()->getPtfStrikes();
    }
    
    /**
     * Can this game be edited by this user?
     * 
     * @param int     $playerId      User ID
     * @param boolean $playerIsAdmin User is a site admin
     * @param int     $ptfUserId     Game author ID; 0 for pre-defined games
     * @param string  $ptfReview     Game review status; suspended games cannot be edited by their authors
     * @return boolean
     */
    public function playerCanEdit($playerId, $playerIsAdmin, $ptfUserId, $ptfReview) {
        $result = false;
        
        // User-defined game
        if ((int) $ptfUserId > 0) {
            // Site admin OR author of game that was not suspended
            $result = (
                $playerIsAdmin
                || (
                    (int) $playerId === (int) $ptfUserId 
                    && Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_SUSPENDED != $ptfReview
                )
            );
        }
        
        return $result;
    }
    
    /**
     * Get the list of available categories
     * 
     * @return string[]
     */
    public function getCategories() {
        return array(
            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_USER_ID       => __('Creator', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_FINISHED      => __('Popularity', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::COL_PTF_MODIFIED_TIME => __('Date', 'stephino-rpg'),
        );
    }
    
    /**
     * Get the list of allowed review values and their labels
     * 
     * @param string $ptfReview (optional) Get the label for the specified review key only
     * @return string[]|string|null List of labels OR string if <b>$ptfReview</b> was specified OR null if <b>$ptfReview</b> is invalid
     */
    public function getReviewLabels($ptfReview = null) {
        $data = array(
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_PENDING                     => __('Pending', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_APPROVED                    => __('Approved', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_REJECTED_TITLE_SPAM         => __('Rejected', 'stephino-rpg') 
                . ': ' . __('Title', 'stephino-rpg') . ' / ' . __('Spam', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_REJECTED_TITLE_INVALID      => __('Rejected', 'stephino-rpg') 
                . ': ' . __('Title', 'stephino-rpg') . ' / ' . __('Invalid', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_REJECTED_CONTENT_SPAM       => __('Rejected', 'stephino-rpg') 
                . ': ' . __('Content', 'stephino-rpg') . ' / ' . __('Spam', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_REJECTED_CONTENT_TOO_EASY   => __('Rejected', 'stephino-rpg') 
                . ': ' . __('Content', 'stephino-rpg') . ' / ' . __('Too easy', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_REJECTED_CONTENT_IMPOSSIBLE => __('Rejected', 'stephino-rpg') 
                . ': ' . __('Content', 'stephino-rpg') . ' / ' . __('Impossible', 'stephino-rpg'),
            Stephino_Rpg_Db_Table_Ptfs::PTF_REVIEW_SUSPENDED                   => __('Suspended', 'stephino-rpg'),
        );
        
        // Found the label
        if (is_string($ptfReview) && strlen($ptfReview)) {
            $result = isset($data[$ptfReview]) ? $data[$ptfReview] : null;
        } else {
            $result = $data;
        }
        
        return $result;
    }
    
    /**
     * Get all the platformers this user has access to (own or public platformers) supplemented with the following columns:<ul>
     * <li>self::PTF_EXTRA_PREVIEW => <b>(int[])</b> Top-left square tile set used for preview</li>
     * <li>self::PTF_EXTRA_REWARD  => <b>(int)</b> Reward in gems for finishing this platformer</li>
     * </ul>
     * 
     * @param int     $userId      User ID
     * @param boolean $viewAll     (optional) View all results (no review filtering); default <b>true</b>
     * @param string  $orderBy     (optional) Column to order by; default <b>null</b>
     * @param boolean $orderAsc    (optional) Order in ASC or DESC order; default <b>true</b>
     * @param int     $limitCount  (optional) Limit count; default <b>null</b>
     * @param int     $limitOffset (optional) Limit offset; default <b>null</b>
     * @return array List of platformers; array may be empty
     */
    public function getForUserId($userId, $viewAll = true, $orderBy = null, $orderAsc = true, $limitCount = null, $limitOffset = null) {
        // Validate the category
        if (null !== $orderBy) {
            if (!in_array($orderBy, array_keys($this->getCategories()))) {
                $orderBy = null;
            }
        }
        
        return $this->_supplementRows(
            $this->getDb()->tablePtfs()->getForUserId($userId, $viewAll, $orderBy, $orderAsc, $limitCount, $limitOffset),
            $userId
        );
    }
    
    /**
     * Get all the platformers this user has created supplemented with the following columns:<ul>
     * <li>self::PTF_EXTRA_PREVIEW => <b>(int[])</b> Top-left square tile set used for preview</li>
     * <li>self::PTF_EXTRA_REWARD  => <b>(int)</b> Reward in gems for finishing this platformer</li>
     * </ul>
     * 
     * @param int     $userId      User ID
     * @param boolean $viewAll     (optional) View all results (no review filtering); default <b>true</b>
     * @param string  $orderBy     (optional) Column to order by; default <b>null</b>
     * @param boolean $orderAsc    (optional) Order in ASC or DESC order; default <b>true</b>
     * @param int     $limitCount  (optional) Limit count; default <b>null</b>
     * @param int     $limitOffset (optional) Limit offset; default <b>null</b>
     * @return array List of platformers; array may be empty
     */
    public function getByUserId($userId, $viewAll = true, $orderBy = null, $orderAsc = true, $limitCount = null, $limitOffset = null) {
        // Validate the category
        if (null !== $orderBy) {
            if (!in_array($orderBy, array_keys($this->getCategories()))) {
                $orderBy = null;
            }
        }
        
        return $this->_supplementRows(
            $this->getDb()->tablePtfs()->getByUserId($userId, $viewAll, $orderBy, $orderAsc, $limitCount, $limitOffset),
            $userId
        );
    }
    
    /**
     * Get a PTF row supplemented with the following columns:<ul>
     * <li>self::PTF_EXTRA_PREVIEW => <b>(int[])</b> Top-left square tile set used for preview</li>
     * <li>self::PTF_EXTRA_REWARD  => <b>(int)</b> Reward in gems for finishing this platformer</li>
     * </ul>
     * 
     * @param int $ptfId  Platformer ID
     * @param int $userId User ID - required to calculate reward
     * @return array|null
     */
    public function getById($ptfId, $userId) {
        $ptfRows = $this->_supplementRows(
            array(
                $this->getDb()->tablePtfs()->getById($ptfId)
            ), 
            $userId
        );
        
        return count($ptfRows) ? current($ptfRows) : null;
    }
    
    /**
     * Supplement the rows with the following columns:<ul>
     * <li>self::PTF_EXTRA_PREVIEW => <b>(int[])</b> Top-left square tile set used for preview</li>
     * <li>self::PTF_EXTRA_REWARD  => <b>(int)</b> Reward in gems for finishing this platformer</li>
     * </ul>
     * 
     * @param array $ptfsList Platformer DB Rows
     * @param int   $userId   User ID
     * @return array
     */
    protected function _supplementRows($ptfsList, $userId) {
        // Get the platformers win times
        $ptfsWon = array();
        
        // Reward is available
        if (Stephino_Rpg_Config::get()->core()->getPtfRewardPlayer()) {
            // Get the user data
            $userData = $this->getDb()->tableUsers()->getById($userId, true);

            // Get the cache
            $userCache = is_array($userData) && isset($userData[Stephino_Rpg_Db_Table_Users::COL_USER_GAME_SETTINGS]) 
                ? json_decode($userData[Stephino_Rpg_Db_Table_Users::COL_USER_GAME_SETTINGS], true) 
                : array();
            if (!is_array($userCache)) {
                $userCache = array();
            }

            // Store the games that were won
            if (isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA])
                && is_array($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA])
                && isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME])) {
                foreach ($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA] as $ptfId => $ptfStatus) {
                    if (Stephino_Rpg_Cache_User::PTF_DATA_WON == $ptfStatus) {
                        $ptfsWon[(int) $ptfId] = (int) $userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME];
                    }
                }
            }
        }
        
        // Prepare the current timestamp
        $currentTime = time();

        // Prepare the available rewards
        foreach ($ptfsList as $dbKey => &$ptfRow) {
            // Prepare the tile set
            if (!is_array($ptfRow) || !is_array($tileSet = $this->getTileSet($ptfRow, true, true))) {
                unset($ptfsList[$dbKey]);
                continue;
            }
            
            // Store the tile set
            $ptfRow[self::PTF_EXTRA_PREVIEW] = $tileSet;
            
            // Prepare the reward
            $ptfRow[self::PTF_EXTRA_REWARD] = 0;
            if (Stephino_Rpg_Config::get()->core()->getPtfRewardPlayer() && $userId != $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_PTF_USER_ID]) {
                // Get the platformer ID
                $ptfId = (int) $ptfRow[Stephino_Rpg_Db_Table_Ptfs::COL_ID];

                // Reward reset
                $resetSeconds = 3600 * Stephino_Rpg_Config::get()->core()->getPtfRewardResetHours();

                // First time or enough time has passed
                if (!isset($ptfsWon[$ptfId]) || ($resetSeconds > 0 && $currentTime - $ptfsWon[$ptfId] >= $resetSeconds)) {
                    $ptfRow[self::PTF_EXTRA_REWARD] = Stephino_Rpg_Config::get()->core()->getPtfRewardPlayer();
                }
            }
        }
        
        // Reset the pointer
        reset($ptfsList);
        
        return $ptfsList;
    }
    
    /**
     * Calculate the rewards for this player and the author when winning a platformer
     * 
     * @param int $userId User ID
     * @param int $ptfId  Platformer ID
     * @return int[] Array of 2 values, reward for the player and reward for the author
     */
    public function getRewards($userId, $ptfId) {
        $result = array(0,0);
        
        // Get the user data
        $userData = $this->getDb()->tableUsers()->getById($userId, true);
        
        // Get the cache
        $userCache = is_array($userData) && isset($userData[Stephino_Rpg_Db_Table_Users::COL_USER_GAME_SETTINGS]) 
            ? json_decode($userData[Stephino_Rpg_Db_Table_Users::COL_USER_GAME_SETTINGS], true) 
            : array();
        if (!is_array($userCache)) {
            $userCache = array();
        }
        
        // Store the games that were won
        if (isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA])
            && is_array($userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA])
            && isset($userCache[Stephino_Rpg_Cache_User::KEY_PTF_TIME])) {
            // Get the played games data
            $ptfData = $userCache[Stephino_Rpg_Cache_User::KEY_PTF_DATA];
            
            // This game was not won before
            if (!isset($ptfData[$ptfId]) || Stephino_Rpg_Cache_User::PTF_DATA_WON != $ptfData[$ptfId]) {
                $result = array(
                    Stephino_Rpg_Config::get()->core()->getPtfRewardPlayer(),
                    Stephino_Rpg_Config::get()->core()->getPtfRewardAuthor(),
                );
            }
        }

        return $result;
    }
}

/* EOF */