<?php
//
// iTop module definition file
//

SetupWebPage::AddModule(
	__FILE__, // Path to the current file, all other file names are relative to the directory containing this file
	'combodo-powerbi-integration/1.0.0',
	array(
		// Identification
		//
		'label' => 'Combodo PowerBI reporting template',
		'category' => 'business',

		// Setup
		//
		'dependencies' => array(
			'itop-request-mgmt-itil/2.7.0||itop-request-mgmt/2.7.0',
		),
		'mandatory' => false,
		'visible' => true,
		'installer' => 'PowerBiIntegrationInstaller',

		// Components
		//
		'datamodel' => array(
			'model.combodo-powerbi-integration.php', // Contains the PHP code generated by the "compilation" of datamodel.combodo-powerbi-integration.xml
		),
		'webservice' => array(
			
		),
		'data.struct' => array(
			// add your 'structure' definition XML files here,
		),
		'data.sample' => array(
			// add your sample data XML files here,
		),
		
		// Documentation
		//
		'doc.manual_setup' => '', // hyperlink to manual setup documentation, if any
		'doc.more_information' => '', // hyperlink to more information, if any 

		// Default settings
		//
		'settings' => array(
			// Module specific settings go here, if any
		),
	)
);

if (!class_exists('PowerBiIntegrationInstaller'))
{
	// Module installation handler
	//
	class PowerBiIntegrationInstaller extends ModuleInstallerAPI
	{
		public static function BeforeWritingConfig(Config $oConfiguration)
		{
			// If you want to override/force some configuration values, do it here
			return $oConfiguration;
		}

		/**
		 * Handler called before creating or upgrading the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function BeforeDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{
			// If you want to migrate data from one format to another, do it here
		}

		/**
		 * Handler called after the creation/update of the database schema
		 * @param $oConfiguration Config The new configuration of the application
		 * @param $sPreviousVersion string PRevious version number of the module (empty string in case of first install)
		 * @param $sCurrentVersion string Current version number of the module
		 */
		public static function AfterDatabaseCreation(Config $oConfiguration, $sPreviousVersion, $sCurrentVersion)
		{
			// Load OQL Queries related to the module
			if (version_compare($sPreviousVersion, $sCurrentVersion, '!=')) {
				$oDataLoader = new XMLDataLoader();

				CMDBObject::SetTrackInfo("Initialization");
				$oMyChange = CMDBObject::GetCurrentChange();

				$sLang = null;
				// Try to get app. language from configuration fil (app. upgrade)
				$sConfigFileName = APPCONF.'production/'.ITOP_CONFIG_FILE;
				if (file_exists($sConfigFileName)) {
					$oFileConfig = new Config($sConfigFileName);
					if (is_object($oFileConfig)) {
						$sLang = str_replace(' ', '_', strtolower($oFileConfig->GetDefaultLanguage()));
					}
				}

				// If still no language, get the default one
				if (null === $sLang) {
					$sLang = str_replace(' ', '_', strtolower($oConfiguration->GetDefaultLanguage()));
				}

				$sFileName = dirname(__FILE__)."/data/{$sLang}.data.combodo-powerbi-integration.xml";
				SetupLog::Info("Searching file: $sFileName");
				if (!file_exists($sFileName)) {
					$sFileName = dirname(__FILE__)."/data/en_us.data.combodo-powerbi-integration.xml";
				}
				SetupLog::Info("Loading file: $sFileName");
				$oDataLoader->StartSession($oMyChange);
				$oDataLoader->LoadFile($sFileName, false, true);
				$oDataLoader->EndSession();
			}
		}
	}
}

