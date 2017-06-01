const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.common.js');
const helpers = require('./helpers');

const DashboardPlugin = require('webpack-dashboard/plugin');

module.exports = webpackMerge(commonConfig, {
    performance: {
        hints: false
    },
    devtool: 'source-map',
    module: {
        loaders: [
            {
                test: /\.ts$/,
                loaders: [
                    'angular2-template-loader',
                    'awesome-typescript-loader',
                    '@angularclass/hmr-loader'
                ]
            }
        ]
    },
    plugins: [
        // sweet interface to replace verbose webpack output
        new DashboardPlugin()
    ],
    devServer: {
        proxy: {
            "/BackEnd": "http://localhost:3000",
            "/data": "http://localhost:3000"
        }
    }
})
