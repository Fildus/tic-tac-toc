const Encore = require('@symfony/webpack-encore');
const path = require('path')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/front/build')
    .setPublicPath('/front/build')
    .addEntry('js/app', './assets/front/js/app.js')
    .addStyleEntry('css/app', './assets/front/css/app.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableIntegrityHashes(Encore.isProduction())
    .addAliases({
        '@front': path.resolve('assets', 'front', 'js')
    })
    .enableVueLoader()
    .enableSassLoader()
;

// build the second configuration
const front = Encore.getWebpackConfig();

// Set a unique name for the config (needed later!)
front.name = 'front';

Encore.reset()

Encore
    .setOutputPath('public/admin/build')
    .setPublicPath('/admin/build')
    .addEntry('js/app', './assets/admin/js/app.js')
    .addStyleEntry('css/app', './assets/admin/css/app.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enableIntegrityHashes(Encore.isProduction())
    .addAliases({
        '@admin': path.resolve('assets', 'admin', 'js')
    })
;

// build the second configuration
const admin = Encore.getWebpackConfig();

// Set a unique name for the config (needed later!)
admin.name = 'admin';

module.exports = [
    front,
    admin
];
