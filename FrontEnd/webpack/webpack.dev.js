const webpackMerge = require('webpack-merge');
const commonConfig = require('./webpack.common.js');

const helpers = require('./helpers');

module.exports = webpackMerge(commonConfig, {
    performance: {
        hints: false
    },
    devtool: 'cheap-module-eval-source-map'
})
