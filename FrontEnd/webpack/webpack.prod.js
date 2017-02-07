var webpack = require('webpack');
var webpackMerge = require('webpack-merge');
var commonConfig = require('./webpack.common.js');
var helpers = require('./helpers');

const OptimizeJsPlugin = require('optimize-js-plugin');

module.exports = webpackMerge(commonConfig, {
    output: {
        path: helpers.root('dist'),
        publicPath: '/',
        filename: '[name].[hash].js',
    },

    plugins: [
        // make build fail if there is any error
        new webpack.NoEmitOnErrorsPlugin(),
        new OptimizeJsPlugin({
            sourceMap: false
        }),
        new webpack.optimize.UglifyJsPlugin({
            mangle: {
                keep_fnames: true
            }
        })
    ]
});
