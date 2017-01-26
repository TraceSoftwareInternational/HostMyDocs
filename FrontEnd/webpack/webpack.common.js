const webpack = require('webpack');
const path    = require("path");
const helpers = require('./helpers');

const CleanWebpackPlugin = require('clean-webpack-plugin');
const { CheckerPlugin }  = require('awesome-typescript-loader');
const ExtractTextPlugin  = require("extract-text-webpack-plugin");
const HtmlWebpackPlugin  = require('html-webpack-plugin');
const TypedocWebpackPlugin = require('typedoc-webpack-plugin');

module.exports = {
    entry: {
        polyfills: helpers.root('src/ts/polyfills.ts'),
        vendor: helpers.root('src/ts/vendor.ts'),
        main: helpers.root('src/ts/main.ts')
    },
    output: {
        filename: 'js/[chunkhash].[name].js',
        path: helpers.root('dist'),
        sourceMapFilename: '[name].map'
    },
    resolve: {
        extensions: ['.ts', '.js', '.html', '.sass']
    },
    module: {
        loaders: [
            {
                test: /\.ts$/,
                loaders: ['angular2-template-loader', 'awesome-typescript-loader']
            },
            {
                test: /\.html$/,
                loader: 'html-loader'
            },
            {
                test: /\.min.css$/,
                loader: ExtractTextPlugin.extract({
                        loader: 'css-loader'
                })
            },
            // this loader will transform any SASS into CSS that will be put in a separate file thanks to ExtractTextPlugin
            {
                test: /\.sass$/,
                loaders: [
                    ExtractTextPlugin.extract({
                        loader: 'css-loader!sass-loader'
                    })
                ]
            },
            // this loader will replace file reference by the content of the SASS file
            {
                test: /\.sass$/,
                loader: 'raw-loader!sass-loader'
            }
        ]
    },
    plugins: [
        new webpack.optimize.CommonsChunkPlugin({
            names: ['main', 'vendor', 'polyfills']
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
        // cf. https://github.com/angular/angular/issues/11580
        new webpack.ContextReplacementPlugin(
            // The (\\|\/) piece accounts for path separators in *nix and Windows
            /angular(\\|\/)core(\\|\/)(esm(\\|\/)src|src)(\\|\/)linker/,
            helpers.root('src'), // location of your src
        { }
      ),
      new TypedocWebpackPlugin({
          name: 'HostMyDocs',
          target: 'es6',
          mode: 'file',
          out: helpers.root('dist/docs')
      }, helpers.root('src'))
    ]
};
