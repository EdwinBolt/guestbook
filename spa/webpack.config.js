const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const webPack = require('webpack');

Encore
    .setOutputPath('public/')
    .setPublicPath('/')
    .cleanupOutputBeforeBuild()
    .addEntry('app', './src/app.js')
    .enablePreactPreset()
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .addPlugin(new HtmlWebpackPlugin({ template: 'src/index.ejs', alwaysWriteToDisk: true }))
    .addPlugin(new webPack.DefinePlugin({
        'ENV_API_ENDPOINT':JSON.stringify(process.env.API_ENDPOINT),
        // 'ENV_API_ENDPOINT':'https://127.0.0.1:8000/',
        // symfosymfony var:exportny var:export SYMFONY_DEFAULT_ROUTE_URL --dir=..
    }))

;

module.exports = Encore.getWebpackConfig();
