const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.common.js');

const helpers = require('./helpers');

module.exports = webpackMerge(commonConfig, {
    performance: {
        hints: false
    },
    devtool: 'source-map',
    devServer: {
        proxy: {
            "/BackEnd": "http://localhost:3000",
            "/data": "http://localhost:3000"
        }
    }
})
