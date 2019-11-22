const path = require('path');

// include the js minification plugin
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

// include the css extraction and minification plugins
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const WebpackAssetsManifest = require('webpack-assets-manifest');

module.exports = {
  entry: ['./src/ethomasweb/js/app.js', './src/ethomasweb/scss/app.scss'],
  output: {
    filename: '../wp-content/themes/ethomasweb/js/[name].[contenthash].js',
    path: path.resolve(__dirname)
  },
  module: {
    rules: [
      // perform js babelization on all .js files
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: {
          presets: ['babel-preset-env']
          }
        }
      },
			// compile all .scss files to plain old css
      {
        test: /\.(sass|scss)$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
      }
    ]
  },
	plugins: [
    // extract css into dedicated file
    new MiniCssExtractPlugin({
      filename: '../wp-content/themes/ethomasweb/css/[name].[contenthash].css'
    }),
		new WebpackAssetsManifest({
			  output: '../wp-content/themes/ethomasweb/manifest.json',
  			merge: true
		})
  ],
  optimization: {
    minimizer: [
      // enable the js minification plugin
      new UglifyJSPlugin({
        cache: true,
        parallel: true
      }),
			// enable the css minification plugin
      new OptimizeCSSAssetsPlugin({})
    ]
  }
};