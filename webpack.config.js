'use strict';

const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore.splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enablePostCssLoader()
    .enableVersioning(true)
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .autoProvidejQuery();

// issue: https://github.com/symfony/webpack-encore/issues/808
/*Encore.configureUrlLoader({
    images: {
        limit: 0, // Avoids files from being inlined
        esModule: false
    }
});*/

Encore.addAliases({
    Vendor: path.resolve(__dirname,  'vendor'),
});

Encore.setOutputPath('public/dist')
    .setPublicPath('/dist')
    .setManifestKeyPrefix('')
    .addEntry('app','./assets/app.js')
    .addEntry('nette.ajax.init', './assets/nette.ajax.init.js')
    .addEntry('recaptcha', './assets/recaptcha.js');

module.exports = Encore.getWebpackConfig();
