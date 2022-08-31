'use strict';

const Encore = require('@symfony/webpack-encore');
const path = require('path');

function reconfigureEncore(Encore, name) {
    Encore.reset();

    if (!Encore.isRuntimeEnvironmentConfigured()) {
        Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
    }

    Encore.splitEntryChunks()
        .enableSingleRuntimeChunk()
        .cleanupOutputBeforeBuild()
        .enableSourceMaps(!Encore.isProduction())
        .enableVersioning(true)
        .setOutputPath('public/dist/' + name)
        .setPublicPath('/dist/' + name)
        .setManifestKeyPrefix('');

    return Encore;
}

const appConfig = reconfigureEncore(Encore, 'app')
    .enablePostCssLoader(options => {
        options.postcssOptions = {
            config: './postcss.config.js',
        };
    })
    .configureBabel(config => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv(config => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .autoProvidejQuery()
    .addAliases({
        Vendor: path.resolve(__dirname,  'vendor'),
    })
    .addRule({
        test: /\.svg/,
        type: 'asset/source',
    })
    .addEntry('app','./assets/app.js')
    .addEntry('nette.ajax.init', './assets/nette.ajax.init.js')
    .addEntry('recaptcha', './assets/recaptcha.js')
    .getWebpackConfig();

const mailConfig = reconfigureEncore(Encore, 'mail')
    .setOutputPath('public/dist/mail')
    .setPublicPath('/dist/mail')
    .enablePostCssLoader(options => {
        options.postcssOptions = {
            config: './postcss-mail.config.js',
        };
    })
    .addStyleEntry('mail', './assets/css/mail/style.css')
    .copyFiles({
        from: './assets/images/mail'
    })
    .getWebpackConfig();

appConfig.name = 'app';
mailConfig.name = 'mail';

module.exports = [appConfig, mailConfig];
