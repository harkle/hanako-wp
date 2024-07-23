const fs = require('fs');
const path = require('path');

const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const EventHooksPlugin = require('event-hooks-webpack-plugin');

const version = '1';

let basePath = './';
let exclude = [];

let configBase = {
  mode: 'production',
  devtool: 'source-map',
  watch: true,
  stats: 'errors-only',
  performance: {
    hints: false,
  },
  optimization: {
    usedExports: true,
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js']
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        exclude: exclude,
        use: {
          loader: 'ts-loader',
          options: {
            transpileOnly: false
          }
        }
      }, {
        test: /\.(png|jpe?g|gif|svg)$/,
        exclude: exclude,
        type: 'asset/resource'
      }, {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        exclude: exclude,
        type: 'asset/resource'
      },
      {
        test: /\.(scss|css)$/,
        exclude: exclude,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              url: false,
            }
          }, {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: function () {
                  return [
                    require('autoprefixer')
                  ];
                }
              }
            }
          }, {
            loader: 'sass-loader',
            options: {
              api: 'modern-compiler',
              sourceMap: true,
              webpackImporter: false,
            }
          }
        ]
      }
    ]
  }
};

let configs = [];
console.log('> Entry points');
['views/ts/', 'views/scss/'].forEach((subFolder) => {
  fs.readdirSync(basePath + subFolder).forEach(element => {
    const extension = path.extname(element);
    const filename = element.replace(extension, '');

    if (extension == '.ts') {
      console.log(`– ${element}`);

      configs.push({
        ...configBase, ...{
          entry: basePath + subFolder + element,
          output: {
            filename: 'js/' + filename + '-v' + version + '.min.js',
            path: path.resolve(__dirname, basePath + '/dist')
          },
          plugins: [
            new ForkTsCheckerWebpackPlugin(),
          ]
        }
      });
    }

    if (extension == '.scss' && filename[0] != '_') {
      console.log(`– ${element}`);

      configs.push({
        ...configBase, ...{
          entry: basePath + subFolder + element,
          output: {
            filename: filename + '-v' + version + '.min.js',
            path: path.resolve(__dirname, basePath + '/dist'),
            assetModuleFilename: 'assets/[hash][ext][query]'
          },
          plugins: [
            new MiniCssExtractPlugin({
              filename: 'css/' + filename + '-v' + version + '.min.css',
            }),
            new CopyPlugin({
              patterns: [{ from: 'views/assets/', to: 'assets/' }]
            })
          ]
        }
      });
    }
  });
});
console.log('\r\n');

configs.forEach((config) => {
  if (config.plugins) {
    config.plugins.push(new EventHooksPlugin({
      'invalid': () => {
        console.log('\r\n');
        console.log(`> recompile: ${(new Date()).toLocaleTimeString()}`);
      },
      'done': (stats) => {
        const time = stats.compilation.endTime - stats.compilation.startTime;
        const filename = Object.keys(stats.compilation.assets)[0];

        console.log(`> \x1b[32m${time}ms\x1b[0m ${filename}`);

        try {
          // Remove all files in dist folder except index.html
          fs.readdirSync(basePath + 'dist/').forEach(element => {
            if (fs.lstatSync(basePath + 'dist/' + element).isFile() && element != 'index.html') fs.unlinkSync(basePath + 'dist/' + element);
          });

          // Check css files in dist folder and extract licence comments
          if (path.extname(filename) == '.css') extractLicenceComments(`${basePath}dist/${filename}`);
        } catch (err) {
          console.error(err);
        }
      }
    }));
  }
});

/*
 * A function to extract licence comments from minified css files
 */
function extractLicenceComments(file) {
  const content = fs.readFileSync(file, 'utf8');
  const comments = content.match(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g);
  const licenseComments = [];

  if (comments) {
    comments.forEach(comment => {
      licenseComments.push(comment);
    });

    // write licence comments to a separate file
    fs.writeFileSync(`${file}.LICENSE.txt`, licenseComments.join('\r\n'));

    // remove comments from css file
    fs.writeFileSync(file, content.replace(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g, ''));
  }
}

module.exports = configs;
