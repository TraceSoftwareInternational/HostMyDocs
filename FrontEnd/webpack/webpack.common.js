const webpack = require('webpack');
const path    = require("path");
const helpers = require('./helpers');

const CleanWebpackPlugin = require('clean-webpack-plugin');
const { CheckerPlugin }  = require('awesome-typescript-loader');
const ExtractTextPlugin  = require("extract-text-webpack-plugin");
const HtmlWebpackPlugin  = require('html-webpack-plugin');
const CommonsChunkPlugin = require('webpack/lib/optimize/CommonsChunkPlugin');

module.exports = {
    entry: {
        polyfills: helpers.root('src/ts/polyfills.ts'),
        main: helpers.root('src/ts/main.ts')
    },
    output: {
        filename: 'js/[hash].[name].js',
        path: helpers.root('dist'),
        sourceMapFilename: '[name].map'
    },
    resolve: {
        extensions: ['.ts', '.js', '.html', '.sass'],
        alias: {
            clarityIconsShapes: helpers.root('node_modules/clarity-icons/shapes')
        }
    },
    module: {
        loaders: [
            {
                test: /\.html$/,
                loader: 'html-loader',
                options: {
                    minimize: false,
                    removeComments: true,
                    collapseWhitespace: true
                }
            },
            {
                test: /\.min.css$/,
                loader: ExtractTextPlugin.extract({
                    use: 'css-loader'
                })
            },
            {
                test: /\.sass$/,
                loaders: ['raw-loader', 'sass-loader']
            },
        ]
    },
    plugins: [
        // This enables to put imports from node_modules into a separated chunk
        new CommonsChunkPlugin({
            name: 'vendor',
            chunks: ['main'],
            minChunks: module => /node_modules/.test(module.resource)
        }),
        // extracting the CSS in it's own file
        new ExtractTextPlugin('css/[chunkhash].[name].css'),
        new HtmlWebpackPlugin({
            inject: 'body',
            template: helpers.root('src/index.html'),
            filename: helpers.root('dist/index.html'),
            chunksSortMode: 'dependency'
        }),
        // type checker plugin for TypeScript
        new CheckerPlugin(),
        // clean the dist folder before building
        new CleanWebpackPlugin(['dist'], {
            root: helpers.root('.'),
        }),
        // cf. https://github.com/angular/angular/issues/14898
        new webpack.ContextReplacementPlugin(
            /angular(\\|\/)core(\\|\/)@angular/,
            helpers.root('./src'),
            {}
        ),
        // give modules names instaed of numeric IDs
        new webpack.NamedModulesPlugin()
    ]
};
