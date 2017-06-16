<?php
namespace ytubes\components\bootstrap;

use Yii;

/**
 * ModuleAutoLoader automatically searches for autostart.php files in module folder an executes them.
 *
 * @author luke
 */
class ModuleAutoLoader implements \yii\base\BootstrapInterface
{
    const CACHE_ID = 'module_configs';

    public function bootstrap($app)
    {
        $modules = Yii::$app->cache->get(self::CACHE_ID);
        if ($modules === false) {
            $modules = [];

            foreach (Yii::$app->params['moduleAutoloadPaths'] as $modulePath) {
                $modulePath = Yii::getAlias($modulePath);

                foreach (scandir($modulePath) as $moduleId) {
                    if ($moduleId == '.' || $moduleId == '..')
                        continue;

                    $moduleDir = $modulePath . DIRECTORY_SEPARATOR . $moduleId;

                    if (is_dir($moduleDir) && is_file($moduleDir . DIRECTORY_SEPARATOR . 'config.php')) {
                        try {
                            $modules[$moduleDir] = require($moduleDir . DIRECTORY_SEPARATOR . 'config.php');
                        } catch (\Exception $ex) {
                        }
                    }
                }
            }

            if (!YII_DEBUG) {
                Yii::$app->cache->set(self::CACHE_ID, $modules);
            }
        }
        Yii::$app->moduleManager->registerBulk($modules);
    }
}
