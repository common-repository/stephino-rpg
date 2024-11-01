<?php
/**
 * Stephino_Rpg_Config_Trait_Labels
 * 
 * @title     Item labels
 * @desc      Item labels - used in Stephino_Rpg_Config_Core
 * @copyright (c) 2021, Stephino
 * @author    Mark Jivko <stephino.team@gmail.com>
 * @package   stephino-rpg
 * @license   GPL v3+, https://gnu.org/licenses/gpl-3.0.txt
 */

trait Stephino_Rpg_Config_Trait_Labels {
    
    /**
     * Gold Name
     * 
     * @var string|null
     */
    protected $_resourceGoldName = null;

    /**
     * Gold Description
     * 
     * @var string|null
     */
    protected $_resourceGoldDescription = null;

    /**
     * Gem Name
     * 
     * @var string|null
     */
    protected $_resourceGemName = null;

    /**
     * Gem Description
     * 
     * @var string|null
     */
    protected $_resourceGemDescription = null;
    
    /**
     * Research Name
     * 
     * @var string|null
     */
    protected $_resourceResearchName = null;

    /**
     * Research Description
     * 
     * @var string|null
     */
    protected $_resourceResearchDescription = null;
    
    /**
     * Alpha Name
     * 
     * @var string|null
     */
    protected $_resourceAlphaName = null;

    /**
     * Alpha Description
     * 
     * @var string|null
     */
    protected $_resourceAlphaDescription = null;

    /**
     * Beta Name
     * 
     * @var string|null
     */
    protected $_resourceBetaName = null;

    /**
     * Beta Description
     * 
     * @var string|null
     */
    protected $_resourceBetaDescription = null;

    /**
     * Gamma Name
     * 
     * @var string|null
     */
    protected $_resourceGammaName = null;

    /**
     * Gamma Description
     * 
     * @var string|null
     */
    protected $_resourceGammaDescription = null;

    /**
     * Extra 1 Name
     * 
     * @var string|null
     */
    protected $_resourceExtra1Name = null;

    /**
     * Extra 1 Description
     * 
     * @var string|null
     */
    protected $_resourceExtra1Description = null;

    /**
     * Extra 2 Name
     * 
     * @var string|null
     */
    protected $_resourceExtra2Name = null;

    /**
     * Extra 2 Description
     * 
     * @var string|null
     */
    protected $_resourceExtra2Description = null;

    /**
     * Population Name
     * 
     * @var string|null
     */
    protected $_metricPopulationName = null;

    /**
     * Population Description
     * 
     * @var string|null
     */
    protected $_metricPopulationDescription = null;
    
    /**
     * Satisfaction Name
     * 
     * @var string|null
     */
    protected $_metricSatisfactionName = null;

    /**
     * Satisfaction Description
     * 
     * @var string|null
     */
    protected $_metricSatisfactionDescription = null;
    
    /**
     * Storage Name
     * 
     * @var string|null
     */
    protected $_metricStorageName = null;

    /**
     * Storage Description
     * 
     * @var string|null
     */
    protected $_metricStorageDescription = null;
    
    /**
     * Attack name
     * 
     * @var string|null
     */
    protected $_militaryAttackName = null;
    
    /**
     * Defense name
     * 
     * @var string|null
     */
    protected $_militaryDefenseName = null;
    
    /**
     * Config Sentry name
     * 
     * @var string|null
     */
    protected $_configSentryName = null;
    
    /**
     * Config Sentries name
     * 
     * @var string|null
     */
    protected $_configSentriesName = null;

    /**
     * Config Government name
     * 
     * @var string|null
     */
    protected $_configGovernmentName = null;

    /**
     * Config Governments name
     *
     * @var string|null
     */
    protected $_configGovernmentsName = null;

    /**
     * Config Island name
     * 
     * @var string|null
     */
    protected $_configIslandName = null;

    /**
     * Config Islands name
     *
     * @var string|null
     */
    protected $_configIslandsName = null;

    /**
     * Config Island Statue name
     * 
     * @var string|null
     */
    protected $_configIslandStatueName = null;

    /**
     * Config Island Statues name
     *
     * @var string|null
     */
    protected $_configIslandStatuesName = null;

    /**
     * Config City name
     * 
     * @var string|null
     */
    protected $_configCityName = null;

    /**
     * Config Cities name
     *
     * @var string|null
     */
    protected $_configCitiesName = null;

    /**
     * Config Building name
     * 
     * @var string|null
     */
    protected $_configBuildingName = null;

    /**
     * Config Buildings name
     *
     * @var string|null
     */
    protected $_configBuildingsName = null;

    /**
     * Config Unit name
     * 
     * @var string|null
     */
    protected $_configUnitName = null;

    /**
     * Config Units name
     *
     * @var string|null
     */
    protected $_configUnitsName = null;

    /**
     * Config Ship name
     * 
     * @var string|null
     */
    protected $_configShipName = null;

    /**
     * Config Ships name
     *
     * @var string|null
     */
    protected $_configShipsName = null;
    
    /**
     * Config Research Field name
     * 
     * @var string|null
     */
    protected $_configResearchFieldName = null;

    /**
     * Config Research Fields name
     * 
     * @var string|null
     */
    protected $_configResearchFieldsName = null;
    
    /**
     * Config Research Area name
     * 
     * @var string|null
     */
    protected $_configResearchAreaName = null;

    /**
     * Config Research Areas name
     *
     * @var string|null
     */
    protected $_configResearchAreasName = null;

    /**
     * The Gold resource name
     * 
     * @section Game Labels
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Gold label
     */
    public function getResourceGoldName($escape = false) {
        return null === $this->_resourceGoldName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_GOLD 
            : ($escape ? esc_html($this->_resourceGoldName) : $this->_resourceGoldName);
    }

    /**
     * Set the "Gold Name" parameter
     * 
     * @param string|null $goldName Gold Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGoldName($goldName) {
        $this->_resourceGoldName = Stephino_Rpg_Utils_Lingo::cleanup($goldName);

        return $this;
    }

    /**
     * The Gold resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Gold description
     */
    public function getResourceGoldDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceGoldDescription) : $this->_resourceGoldDescription);
    }

    /**
     * Set the "Gold Description" parameter
     * 
     * @param string|null $goldDescription Gold Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGoldDescription($goldDescription) {
        $this->_resourceGoldDescription = Stephino_Rpg_Utils_Lingo::cleanup($goldDescription);

        return $this;
    }
    
    /**
     * The Gem resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Gem label
     */
    public function getResourceGemName($escape = false) {
        return null === $this->_resourceGemName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_GEM 
            : ($escape ? esc_html($this->_resourceGemName) : $this->_resourceGemName);
    }

    /**
     * Set the "Gem Name" parameter
     * 
     * @param string|null $gemName Gem Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGemName($gemName) {
        $this->_resourceGemName = Stephino_Rpg_Utils_Lingo::cleanup($gemName);

        return $this;
    }

    /**
     * The Gem resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Gem description
     */
    public function getResourceGemDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceGemDescription) : $this->_resourceGemDescription);
    }

    /**
     * Set the "Gem Description" parameter
     * 
     * @param string|null $gemDescription Gem Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGemDescription($gemDescription) {
        $this->_resourceGemDescription = Stephino_Rpg_Utils_Lingo::cleanup($gemDescription);

        return $this;
    }

    /**
     * The Research resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Research name
     */
    public function getResourceResearchName($escape = false) {
        return null === $this->_resourceResearchName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_RESEARCH 
            : ($escape ? esc_html($this->_resourceResearchName) : $this->_resourceResearchName);
    }

    /**
     * The "Research" feature name
     * 
     * @param string|null $resourceResearchName Research Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceResearchName($resourceResearchName) {
        $this->_resourceResearchName = Stephino_Rpg_Utils_Lingo::cleanup($resourceResearchName);

        return $this;
    }

    /**
     * The Research resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Research description
     */
    public function getResourceResearchDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceResearchDescription) : $this->_resourceResearchDescription);
    }

    /**
     * Set the "Research Description" parameter
     * 
     * @param string|null $resourceResearchDescription Research Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceResearchDescription($resourceResearchDescription) {
        $this->_resourceResearchDescription = Stephino_Rpg_Utils_Lingo::cleanup($resourceResearchDescription);

        return $this;
    }

    /**
     * The First resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Alpha name
     */
    public function getResourceAlphaName($escape = false) {
        return null === $this->_resourceAlphaName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_ALPHA 
            : ($escape ? esc_html($this->_resourceAlphaName) : $this->_resourceAlphaName);
    }

    /**
     * Set the "Alpha Name" parameter
     * 
     * @param string|null $alphaName Alpha Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceAlphaName($alphaName) {
        $this->_resourceAlphaName = Stephino_Rpg_Utils_Lingo::cleanup($alphaName);

        return $this;
    }

    /**
     * The First resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Alpha description
     */
    public function getResourceAlphaDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceAlphaDescription) : $this->_resourceAlphaDescription);
    }

    /**
     * Set the "Alpha Description" parameter
     * 
     * @param string|null $alphaDescription Alpha Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceAlphaDescription($alphaDescription) {
        $this->_resourceAlphaDescription = Stephino_Rpg_Utils_Lingo::cleanup($alphaDescription);

        return $this;
    }

    /**
     * The Second resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Beta name
     */
    public function getResourceBetaName($escape = false) {
        return null === $this->_resourceBetaName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_BETA 
            : ($escape ? esc_html($this->_resourceBetaName) : $this->_resourceBetaName);
    }

    /**
     * Set the "Beta Name" parameter
     * 
     * @param string|null $betaName Beta Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceBetaName($betaName) {
        $this->_resourceBetaName = Stephino_Rpg_Utils_Lingo::cleanup($betaName);

        return $this;
    }

    /**
     * The Second resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Beta description
     */
    public function getResourceBetaDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceBetaDescription) : $this->_resourceBetaDescription);
    }

    /**
     * Set the "Beta Description" parameter
     * 
     * @param string|null $betaDescription Beta Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceBetaDescription($betaDescription) {
        $this->_resourceBetaDescription = Stephino_Rpg_Utils_Lingo::cleanup($betaDescription);

        return $this;
    }

    /**
     * The Third resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Gamma name
     */
    public function getResourceGammaName($escape = false) {
        return null === $this->_resourceGammaName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_GAMMA 
            : ($escape ? esc_html($this->_resourceGammaName) : $this->_resourceGammaName);
    }

    /**
     * Set the "Gamma Name" parameter
     * 
     * @param string|null $gammaName Gamma Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGammaName($gammaName) {
        $this->_resourceGammaName = Stephino_Rpg_Utils_Lingo::cleanup($gammaName);

        return $this;
    }

    /**
     * The Third resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Gamma description
     */
    public function getResourceGammaDescription($escape = false) {
        return ($escape ? esc_html($this->_resourceGammaDescription) : $this->_resourceGammaDescription);
    }

    /**
     * Set the "Gamma Description" parameter
     * 
     * @param string|null $gammaDescription Gamma Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceGammaDescription($gammaDescription) {
        $this->_resourceGammaDescription = Stephino_Rpg_Utils_Lingo::cleanup($gammaDescription);

        return $this;
    }

    /**
     * The first extra resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Extra resource 1 name
     */
    public function getResourceExtra1Name($escape = false) {
        return null === $this->_resourceExtra1Name 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_EXTRA1 
            : ($escape ? esc_html($this->_resourceExtra1Name) : $this->_resourceExtra1Name);
    }

    /**
     * Set the "Extra 1 Name" parameter
     * 
     * @param string|null $extra1Name Extra 1 Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceExtra1Name($extra1Name) {
        $this->_resourceExtra1Name = Stephino_Rpg_Utils_Lingo::cleanup($extra1Name);

        return $this;
    }

    /**
     * The first extra resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Extra resource 1 description
     */
    public function getResourceExtra1Description($escape = false) {
        return ($escape ? esc_html($this->_resourceExtra1Description) : $this->_resourceExtra1Description);
    }

    /**
     * Set the "Extra 1 Description" parameter
     * 
     * @param string|null $extra1Description Extra 1 Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceExtra1Description($extra1Description) {
        $this->_resourceExtra1Description = Stephino_Rpg_Utils_Lingo::cleanup($extra1Description);

        return $this;
    }

    /**
     * The second extra resource name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Extra resource 2 name
     */
    public function getResourceExtra2Name($escape = false) {
        return null === $this->_resourceExtra2Name 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_RES_EXTRA2 
            : ($escape ? esc_html($this->_resourceExtra2Name) : $this->_resourceExtra2Name);
    }

    /**
     * Set the "Extra 2 Name" parameter
     * 
     * @param string|null $extra2Name Extra 2 Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceExtra2Name($extra2Name) {
        $this->_resourceExtra2Name = Stephino_Rpg_Utils_Lingo::cleanup($extra2Name);

        return $this;
    }

    /**
     * The second extra resource description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Extra resource 2 description
     */
    public function getResourceExtra2Description($escape = false) {
        return ($escape ? esc_html($this->_resourceExtra2Description) : $this->_resourceExtra2Description);
    }

    /**
     * Set the "Extra 2 Description" parameter
     * 
     * @param string|null $extra2Description Extra 2 Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setResourceExtra2Description($extra2Description) {
        $this->_resourceExtra2Description = Stephino_Rpg_Utils_Lingo::cleanup($extra2Description);

        return $this;
    }
    
    /**
     * Population metric name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Population name
     */
    public function getMetricPopulationName($escape = false) {
        return null === $this->_metricPopulationName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_METRIC_POPULATION 
            : ($escape ? esc_html($this->_metricPopulationName) : $this->_metricPopulationName);
    }

    /**
     * Set the "Population Name" parameter
     * 
     * @param string|null $populationName Population Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricPopulationName($populationName) {
        $this->_metricPopulationName = Stephino_Rpg_Utils_Lingo::cleanup($populationName);

        return $this;
    }

    /**
     * The Population metric description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Population description
     */
    public function getMetricPopulationDescription($escape = false) {
        return ($escape ? esc_html($this->_metricPopulationDescription) : $this->_metricPopulationDescription);
    }

    /**
     * Set the "Population Description" parameter
     * 
     * @param string|null $populationDescription Population Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricPopulationDescription($populationDescription) {
        $this->_metricPopulationDescription = Stephino_Rpg_Utils_Lingo::cleanup($populationDescription);

        return $this;
    }
    
    /**
     * The Satisfaction metric name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Satisfaction name
     */
    public function getMetricSatisfactionName($escape = false) {
        return null === $this->_metricSatisfactionName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_METRIC_SATISFACTION 
            : ($escape ? esc_html($this->_metricSatisfactionName) : $this->_metricSatisfactionName);
    }

    /**
     * Set the "Satisfaction Name" parameter
     * 
     * @param string|null $satisfactionName Satisfaction Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricSatisfactionName($satisfactionName) {
        $this->_metricSatisfactionName = Stephino_Rpg_Utils_Lingo::cleanup($satisfactionName);

        return $this;
    }

    /**
     * The Satisfaction metric description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Satisfaction description
     */
    public function getMetricSatisfactionDescription($escape = false) {
        return ($escape ? esc_html($this->_metricSatisfactionDescription) : $this->_metricSatisfactionDescription);
    }

    /**
     * Set the "Satisfaction Description" parameter
     * 
     * @param string|null $satisfactionDescription Satisfaction Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricSatisfactionDescription($satisfactionDescription) {
        $this->_metricSatisfactionDescription = Stephino_Rpg_Utils_Lingo::cleanup($satisfactionDescription);

        return $this;
    }
    
    /**
     * Storage metric name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Storage name
     */
    public function getMetricStorageName($escape = false) {
        return null === $this->_metricStorageName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_METRIC_STORAGE 
            : ($escape ? esc_html($this->_metricStorageName) : $this->_metricStorageName);
    }

    /**
     * Set the "Storage Name" parameter
     * 
     * @param string|null $storageName Storage Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricStorageName($storageName) {
        $this->_metricStorageName = Stephino_Rpg_Utils_Lingo::cleanup($storageName);

        return $this;
    }

    /**
     * Storage metric description
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string|null Storage description
     */
    public function getMetricStorageDescription($escape = false) {
        return ($escape ? esc_html($this->_metricStorageDescription) : $this->_metricStorageDescription);
    }

    /**
     * Set the "Storage Description" parameter
     * 
     * @param string|null $storageDescription Storage Description
     * @return Stephino_Rpg_Config_Core
     */
    public function setMetricStorageDescription($storageDescription) {
        $this->_metricStorageDescription = Stephino_Rpg_Utils_Lingo::cleanup($storageDescription);

        return $this;
    }
    
    /**
     * Military attack name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Attack name
     */
    public function getMilitaryAttackName($escape = false) {
        return null === $this->_militaryAttackName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_MILITARY_ATTACK 
            : ($escape ? esc_html($this->_militaryAttackName) : $this->_militaryAttackName);
    }

    /**
     * Set the "Attack Name" parameter
     * 
     * @param string|null $attackName Attack Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setMilitaryAttackName($attackName) {
        $this->_militaryAttackName = Stephino_Rpg_Utils_Lingo::cleanup($attackName);

        return $this;
    }
    
    /**
     * Military defense name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Defense name
     */
    public function getMilitaryDefenseName($escape = false) {
        return null === $this->_militaryDefenseName 
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_MILITARY_DEFENSE 
            : ($escape ? esc_html($this->_militaryDefenseName) : $this->_militaryDefenseName);
    }

    /**
     * Set the "Defense Name" parameter
     * 
     * @param string|null $defense Defense Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setMilitaryDefenseName($defense) {
        $this->_militaryDefenseName = Stephino_Rpg_Utils_Lingo::cleanup($defense);

        return $this;
    }
    
    /**
     * Sentry configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Sentry name
     */
    public function getConfigSentryName($escape = false) {
        return null === $this->_configSentryName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_SENTRY
            : ($escape ? esc_html($this->_configSentryName) : $this->_configSentryName);
    }
    
    /**
     * Set the "Config Sentry Name" parameter
     * 
     * @param string|null $configSentryName Config Sentry Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigSentryName($configSentryName) {
        $this->_configSentryName = Stephino_Rpg_Utils_Lingo::cleanup($configSentryName);
        
        return $this;
    }
    
    /**
     * Sentries configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Sentries name
     */
    public function getConfigSentriesName($escape = false) {
        return null === $this->_configSentriesName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_SENTRIES
            : ($escape ? esc_html($this->_configSentriesName) : $this->_configSentriesName);
    }
    
    /**
     * Set the "Config Sentries Name" parameter
     * 
     * @param string|null $configSentriesName Config Sentries Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigSentriesName($configSentriesName) {
        $this->_configSentriesName = Stephino_Rpg_Utils_Lingo::cleanup($configSentriesName);
        
        return $this;
    }
    
    /**
     * Government configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Government name
     */
    public function getConfigGovernmentName($escape = false) {
        return null === $this->_configGovernmentName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_GOVERNMENT
            : ($escape ? esc_html($this->_configGovernmentName) : $this->_configGovernmentName);
    }

    /**
     * Set the "Config Government Name" parameter
     * 
     * @param string|null $configGovernmentName Config Government Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigGovernmentName($configGovernmentName) {
        $this->_configGovernmentName = Stephino_Rpg_Utils_Lingo::cleanup($configGovernmentName);
        
        return $this;
    }

    /**
     * Governments configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Governments name
     */
    public function getConfigGovernmentsName($escape = false) {
        return null === $this->_configGovernmentsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_GOVERNMENTS
            : ($escape ? esc_html($this->_configGovernmentsName) : $this->_configGovernmentsName);
    }

    /**
     * Set the "Config Governments Name" parameter
     * 
     * @param string|null $configGovernmentsName Config Governments Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigGovernmentsName($configGovernmentsName) {
        $this->_configGovernmentsName = Stephino_Rpg_Utils_Lingo::cleanup($configGovernmentsName);
        
        return $this;
    }

    /**
     * Island configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Island name
     */
    public function getConfigIslandName($escape = false) {
        return null === $this->_configIslandName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_ISLAND
            : ($escape ? esc_html($this->_configIslandName) : $this->_configIslandName);
    }

    /**
     * Set the "Config Island Name" parameter
     * 
     * @param string|null $configIslandName Config Island Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigIslandName($configIslandName) {
        $this->_configIslandName = Stephino_Rpg_Utils_Lingo::cleanup($configIslandName);
        
        return $this;
    }

    /**
     * Islands configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Islands name
     */
    public function getConfigIslandsName($escape = false) {
        return null === $this->_configIslandsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_ISLANDS
            : ($escape ? esc_html($this->_configIslandsName) : $this->_configIslandsName);
    }

    /**
     * Set the "Config Islands Name" parameter
     * 
     * @param string|null $configIslandsName Config Islands Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigIslandsName($configIslandsName) {
        $this->_configIslandsName = Stephino_Rpg_Utils_Lingo::cleanup($configIslandsName);
        
        return $this;
    }

    /**
     * Island Statue configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Island Statue name
     */
    public function getConfigIslandStatueName($escape = false) {
        return null === $this->_configIslandStatueName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_ISLAND_STATUE
            : ($escape ? esc_html($this->_configIslandStatueName) : $this->_configIslandStatueName);
    }

    /**
     * Set the "Config Island Statue Name" parameter
     * 
     * @param string|null $configIslandStatueName Config Island Statue Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigIslandStatueName($configIslandStatueName) {
        $this->_configIslandStatueName = Stephino_Rpg_Utils_Lingo::cleanup($configIslandStatueName);
        
        return $this;
    }

    /**
     * Island Statues configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Island Statues name
     */
    public function getConfigIslandStatuesName($escape = false) {
        return null === $this->_configIslandStatuesName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_ISLAND_STATUES
            : ($escape ? esc_html($this->_configIslandStatuesName) : $this->_configIslandStatuesName);
    }

    /**
     * Set the "Config Island Statues Name" parameter
     * 
     * @param string|null $configIslandStatuesName Config Island Statues Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigIslandStatuesName($configIslandStatuesName) {
        $this->_configIslandStatuesName = Stephino_Rpg_Utils_Lingo::cleanup($configIslandStatuesName);
        
        return $this;
    }

    /**
     * City configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string City name
     */
    public function getConfigCityName($escape = false) {
        return null === $this->_configCityName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_CITY
            : ($escape ? esc_html($this->_configCityName) : $this->_configCityName);
    }

    /**
     * Set the "Config City Name" parameter
     * 
     * @param string|null $configCityName Config City Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigCityName($configCityName) {
        $this->_configCityName = Stephino_Rpg_Utils_Lingo::cleanup($configCityName);
        
        return $this;
    }

    /**
     * Cities configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Cities name
     */
    public function getConfigCitiesName($escape = false) {
        return null === $this->_configCitiesName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_CITIES
            : ($escape ? esc_html($this->_configCitiesName) : $this->_configCitiesName);
    }

    /**
     * Set the "Config Cities Name" parameter
     * 
     * @param string|null $configCitiesName Config Cities Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigCitiesName($configCitiesName) {
        $this->_configCitiesName = Stephino_Rpg_Utils_Lingo::cleanup($configCitiesName);
        
        return $this;
    }

    /**
     * Building configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Building name
     */
    public function getConfigBuildingName($escape = false) {
        return null === $this->_configBuildingName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_BUILDING
            : ($escape ? esc_html($this->_configBuildingName) : $this->_configBuildingName);
    }

    /**
     * Set the "Config Building Name" parameter
     * 
     * @param string|null $configBuildingName Config Building Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigBuildingName($configBuildingName) {
        $this->_configBuildingName = Stephino_Rpg_Utils_Lingo::cleanup($configBuildingName);
        
        return $this;
    }

    /**
     * Buildings configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Buildings name
     */
    public function getConfigBuildingsName($escape = false) {
        return null === $this->_configBuildingsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_BUILDINGS
            : ($escape ? esc_html($this->_configBuildingsName) : $this->_configBuildingsName);
    }

    /**
     * Set the "Config Buildings Name" parameter
     * 
     * @param string|null $configBuildingsName Config Buildings Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigBuildingsName($configBuildingsName) {
        $this->_configBuildingsName = Stephino_Rpg_Utils_Lingo::cleanup($configBuildingsName);
        
        return $this;
    }

    /**
     * Unit configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Unit name
     */
    public function getConfigUnitName($escape = false) {
        return null === $this->_configUnitName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_UNIT
            : ($escape ? esc_html($this->_configUnitName) : $this->_configUnitName);
    }

    /**
     * Set the "Config Unit Name" parameter
     * 
     * @param string|null $configUnitName Config Unit Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigUnitName($configUnitName) {
        $this->_configUnitName = Stephino_Rpg_Utils_Lingo::cleanup($configUnitName);
        
        return $this;
    }

    /**
     * Units configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Units name
     */
    public function getConfigUnitsName($escape = false) {
        return null === $this->_configUnitsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_UNITS
            : ($escape ? esc_html($this->_configUnitsName) : $this->_configUnitsName);
    }

    /**
     * Set the "Config Units Name" parameter
     * 
     * @param string|null $configUnitsName Config Units Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigUnitsName($configUnitsName) {
        $this->_configUnitsName = Stephino_Rpg_Utils_Lingo::cleanup($configUnitsName);
        
        return $this;
    }

    /**
     * Ship configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Ship name
     */
    public function getConfigShipName($escape = false) {
        return null === $this->_configShipName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_SHIP
            : ($escape ? esc_html($this->_configShipName) : $this->_configShipName);
    }

    /**
     * Set the "Config Ship Name" parameter
     * 
     * @param string|null $configShipName Config Ship Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigShipName($configShipName) {
        $this->_configShipName = Stephino_Rpg_Utils_Lingo::cleanup($configShipName);
        
        return $this;
    }

    /**
     * Ships configuration name
     *
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Ships name
     */
    public function getConfigShipsName($escape = false) {
        return null === $this->_configShipsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_SHIPS
            : ($escape ? esc_html($this->_configShipsName) : $this->_configShipsName);
    }

    /**
     * Set the "Config Ships Name" parameter
     * 
     * @param string|null $configShipsName Config Ships Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigShipsName($configShipsName) {
        $this->_configShipsName = Stephino_Rpg_Utils_Lingo::cleanup($configShipsName);
        
        return $this;
    }
    
    /**
     * Research Field configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Research Field name
     */
    public function getConfigResearchFieldName($escape = false) {
        return null === $this->_configResearchFieldName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_RES_FIELD
            : ($escape ? esc_html($this->_configResearchFieldName) : $this->_configResearchFieldName);
    }

    /**
     * Set the "Config Research Field Name" parameter
     * 
     * @param string|null $configResearchFieldName Config Research Field Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigResearchFieldName($configResearchFieldName) {
        $this->_configResearchFieldName = Stephino_Rpg_Utils_Lingo::cleanup($configResearchFieldName);
        
        return $this;
    }

    /**
     * Research Fields configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Research Fields name
     */
    public function getConfigResearchFieldsName($escape = false) {
        return null === $this->_configResearchFieldsName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_RES_FIELDS
            : ($escape ? esc_html($this->_configResearchFieldsName) : $this->_configResearchFieldsName);
    }
    
    /**
     * Set the "Config Research Fields Name" parameter
     * 
     * @param string|null $configResearchFieldsName Config Research Fields Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigResearchFieldsName($configResearchFieldsName) {
        $this->_configResearchFieldsName = Stephino_Rpg_Utils_Lingo::cleanup($configResearchFieldsName);
        
        return $this;
    }
    
    /**
     * Research Area configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Research Area name
     */
    public function getConfigResearchAreaName($escape = false) {
        return null === $this->_configResearchAreaName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_RES_AREA
            : ($escape ? esc_html($this->_configResearchAreaName) : $this->_configResearchAreaName);
    }

    /**
     * Set the "Config Research Area Name" parameter
     * 
     * @param string|null $configResearchAreaName Config Research Area Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigResearchAreaName($configResearchAreaName) {
        $this->_configResearchAreaName = Stephino_Rpg_Utils_Lingo::cleanup($configResearchAreaName);
        
        return $this;
    }

    /**
     * Research Areas configuration name
     * 
     * @param boolean $escape (optional) HTML escape the output; default <b>false</b>
     * @return string Research Areas name
     */
    public function getConfigResearchAreasName($escape = false) {
        return null === $this->_configResearchAreasName
            ? Stephino_Rpg_Config_Core::DEFAULT_LABEL_CONFIG_RES_AREAS
            : ($escape ? esc_html($this->_configResearchAreasName) : $this->_configResearchAreasName);
    }
    
    /**
     * Set the "Config Research Areas Name" parameter
     * 
     * @param string|null $configResearchAreasName Config Research Areas Name
     * @return Stephino_Rpg_Config_Core
     */
    public function setConfigResearchAreasName($configResearchAreasName) {
        $this->_configResearchAreasName = Stephino_Rpg_Utils_Lingo::cleanup($configResearchAreasName);
        
        return $this;
    }
}

/* EOF */