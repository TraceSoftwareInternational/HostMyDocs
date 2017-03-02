var webpack = require('webpack');
var webpackMerge = require('webpack-merge');
var commonConfig = require('./webpack.common.js');
var helpers = require('./helpers');

const OptimizeJsPlugin = require('optimize-js-plugin');
const SriPlugin = require('webpack-subresource-integrity');

module.exports = webpackMerge(commonConfig, {
    entry: {
        main: helpers.root('src/ts/main.prod.ts')
    },
    output: {
        path: helpers.root('dist'),
        publicPath: '/',
        filename: '[name].[hash].js',
        crossOriginLoading: 'anonymous'
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
        }),
        new SriPlugin({
            hashFuncNames: ['sha384']
        })
    ]
});
