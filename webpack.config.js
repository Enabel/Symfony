var Encore = require("@symfony/webpack-encore");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It"s useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    .setOutputPath("public/build/")
    .setPublicPath("/build")
    .addEntry("app", "./assets/js/app.js")
    .addStyleEntry("global","./assets/scss/global.scss")
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage";
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
// .enableReactPreset()
// .addEntry("admin", "./assets/js/admin.js")
;

module.exports = Encore.getWebpackConfig();
