/* eslint-env node */
const path = require('path');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

/* `process.env.NODE_ENV` is required for postcss.config.js to be able to read from its env variable */
const devMode = process.env.NODE_ENV !== 'production';

/**
 * 
 * @param {Object} m - The webpack Module
 * A function that calls itself until either 'false'
 * or the webpack Module name is extracted from the 'm' Object.
 * See https://webpack.js.org/plugins/mini-css-extract-plugin/#extracting-css-based-on-entry
 */
function recursiveIssuer(m) {
  if (m.issuer) {
    return recursiveIssuer(m.issuer);
  } else if (m.name) {
    return m.name;
  } else {
    return false;
  }
}

module.exports = {
  mode: devMode ? 'development' : 'production',
  entry: {
    bundle: path.resolve(__dirname, './src/js/entry.js'),
    style: path.resolve(__dirname, './src/scss/style.scss'),
    print: path.resolve(__dirname, './src/scss/print.scss'),
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        style: {
          name: 'style',
          test: (m, c, entry = 'style') =>
            m.constructor.name === 'CssModule' && recursiveIssuer(m) === entry,
          chunks: 'all',
          enforce: true,
        },
        print: {
          name: 'print',
          test: (m, c, entry = 'print') =>
            m.constructor.name === 'CssModule' && recursiveIssuer(m) === entry,
          chunks: 'all',
          enforce: true,
        },
      },
    },
  },
  output: {
    filename: 'js/[name].js',
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/themes/custom/numiko/dist'
  },
  devtool: devMode ? 'module-source-map' : false, // Cheap source maps don't work with UglifyJSPlugin
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            query: {
              plugins: ['lodash'],
              presets: ['@babel/preset-env']
            }
          },
          {
            loader: 'eslint-loader'
          }
        ],
      },
      {
        test: /\.scss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              hmr: devMode
            },
          },
          {
            loader: 'css-loader',
            options: { sourceMap: devMode }
          },
          {
            loader: 'postcss-loader',
            options: { sourceMap: devMode }
          },
          {
            loader: 'sass-loader',
            options: {
              includePaths: ['node_modules'],
              sourceMap: devMode
            }
          }
        ],
      },
      {
        test: /\.woff(2)?$/,
        use: [
          {
            loader: 'url-loader',
            options: {
              limit: 10000,
              name: '/font/[hash].[ext]',
              mimetype: 'application/font-woff'
            }
          }
        ]
      },
      {
        test: /\.(jpe?g|png|gif|svg)$/i,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '/img/[name].[ext]',
            }
          },
          'image-webpack-loader',
        ]
      },
    ]
  },
  plugins: [
    new UglifyJSPlugin(),

    /* 
    * Extract styles to CSS file
    */
    new MiniCssExtractPlugin({
      filename: './css/[name].css',
    }),

    /*
    * Linting
    */
    new StyleLintPlugin(),

    /*
    * SVG sprite map
    * Spritemap plugin will only include svg files prefixed with `sprite-` to ensure we are only exposing files we need
    */
    new SVGSpritemapPlugin('./src/img/svg/**/sprite-*.svg', {
      output: {
        filename: '/img/sprite/svg-symbols.svg',
      },
      sprite: {
        prefix: false,
        generate: {
          title: false
        }
      }
    }),
    new BundleAnalyzerPlugin({
      analyzerMode: 'disabled',
      generateStatsFile: true,
      statsOptions: { source: false }
    })
  ],
  resolve: {
    modules: [
      'node_modules',
      'js'
    ],
  },
  devServer: {
    overlay: true,
    port: 9000,
  },

  /*
  Important:
  In order to avoid multiple instances of jQuery loading into the page
  we are dictating that the whole site make use of the drupal jQuery.

  Without this certain pages would display inconsistencies, as they are
  unable to access Drupal properties.

  The key example of this is any page using views or other forms of pagination.

  The core jQuery is now included as a library dependency on the bundle js
  on the theme itself (included in crick.libraries.yml).
  */
  externals: {
    jquery: 'jQuery'
  }
};