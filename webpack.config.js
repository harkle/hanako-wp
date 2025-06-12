const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const WebpackBar = require('webpackbar');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

const version = '1';
const isDevelopment = process.env.NODE_ENV !== 'production';

// Fonction pour extraire les licences CSS
function extractLicenceComments(file) {
  const content = fs.readFileSync(file, 'utf8');
  const comments = content.match(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g);
  const licenseComments = [];

  if (comments) {
    comments.forEach(comment => {
      licenseComments.push(comment);
    });

    // Écrire les commentaires de licence dans un fichier séparé
    fs.writeFileSync(`${file}.LICENSE.txt`, licenseComments.join('\r\n'));

    // Supprimer les commentaires du fichier CSS
    fs.writeFileSync(file, content.replace(/\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g, ''));
  }
}

// Plugin custom pour traiter les licences CSS
class CssLicenseExtractorPlugin {
  constructor(options = {}) {
    this.options = {
      // Pattern pour identifier les commentaires de licence
      licensePattern: /\/\*![^*]*\*+([^\/*][^*]*\*+)*\//g,
      // Extension pour les fichiers de licence
      licenseExtension: '.LICENSE.txt',
      // Seulement en production par défaut
      extractInDev: false,
      // Log des actions
      verbose: true,
      ...options
    };
  }

  apply(compiler) {
    compiler.hooks.afterEmit.tap('CssLicenseExtractorPlugin', (compilation) => {
      // Skip en développement si pas demandé
      if (isDevelopment && !this.options.extractInDev) {
        return;
      }

      Object.keys(compilation.assets).forEach(assetName => {
        if (assetName.endsWith('.css')) {
          const assetPath = path.join(compilation.outputOptions.path, assetName);

          if (fs.existsSync(assetPath)) {
            this.extractLicences(assetPath, assetName);
          }
        }
      });
    });
  }

  extractLicences(filePath, fileName) {
    try {
      const content = fs.readFileSync(filePath, 'utf8');
      const comments = content.match(this.options.licensePattern);

      if (comments && comments.length > 0) {
        const licenseFile = filePath + this.options.licenseExtension;

        // Écrire les licences
        fs.writeFileSync(licenseFile, comments.join('\r\n'));

        // Nettoyer le CSS
        const cleanedContent = content.replace(this.options.licensePattern, '');
        fs.writeFileSync(filePath, cleanedContent);

        if (this.options.verbose) {
          console.log(`📄 ${comments.length} licence(s) extraite(s) pour: ${fileName}`);
        }
      }
    } catch (error) {
      console.error(`❌ Erreur lors de l'extraction des licences pour ${fileName}:`, error);
    }
  }
}

module.exports = {
  mode: isDevelopment ? 'development' : 'production',

  entry: {
    main: './views/ts/site.ts',
    style: './views/scss/style.scss',
    'editor-style': './views/scss/editor-style.scss',
  },

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'js/[name]-v' + version + '.js',
    chunkFilename: 'js/[name]-v' + version + '.chunk.js',
    publicPath: '/wp-content/themes/your-theme-name/dist/',
    clean: true
  },

  // Source maps optimisés
  devtool: isDevelopment ? 'eval-cheap-module-source-map' : 'source-map',

  resolve: {
    extensions: ['.ts', '.js', '.scss', '.css'],
    alias: {
      '@': path.resolve(__dirname, 'views'),
      '@ts': path.resolve(__dirname, 'views/ts'),
      '@scss': path.resolve(__dirname, 'views/scss'),
      '@components': path.resolve(__dirname, 'views/ts/components'),
      '@modules': path.resolve(__dirname, 'views/ts/modules')
    }
  },

  module: {
    rules: [
      // TypeScript
      {
        test: /\.tsx?$/,
        use: [
          {
            loader: 'ts-loader',
            options: {
              transpileOnly: isDevelopment, // Plus rapide en dev
              configFile: 'tsconfig.json'
            }
          }
        ],
        exclude: /node_modules/
      },

      // SCSS/CSS avec optimisations
      {
        test: /\.(scss|sass|css)$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              importLoaders: 2
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              postcssOptions: {
                plugins: [
                  ['autoprefixer'],
                  ...(isDevelopment ? [] : [['cssnano', { preset: 'default' }]])
                ]
              }
            }
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
              sassOptions: {
                outputStyle: isDevelopment ? 'expanded' : 'compressed',
                includePaths: ['node_modules']
              }
            }
          }
        ]
      },

      // Images optimisées (Asset Modules)
      {
        test: /\.(png|jpe?g|gif|svg|webp)$/i,
        type: 'asset',
        generator: {
          filename: 'images/[name].[ext]'
        }
      },

      // Fonts
      {
        test: /\.(woff|woff2|eot|ttf|otf)$/i,
        type: 'asset/resource',
        generator: {
          filename: 'fonts/[name].[ext]'
        }
      }
    ]
  },

  plugins: [
    // Barre de progression stylée
    new WebpackBar({
      name: 'Hanako WP',
      color: '#667eea'
    }),

    // Copie des assets statiques
    new CopyWebpackPlugin({
      patterns: [
        {
          from: 'views/assets/',
          to: 'assets',
          noErrorOnMissing: true
        },
        // Ajoute d'autres dossiers d'assets si besoin
      ]
    }),

    // Extraction CSS
    new MiniCssExtractPlugin({
      filename: 'css/[name]-v' + version + '.css',
      chunkFilename: 'css/[name]-v' + version + '.chunk.css'
    }),

    // Extracteur de licences CSS custom
    new CssLicenseExtractorPlugin({
      extractInDev: true,
      verbose: true
    }),

    // Manifeste des assets
    new WebpackManifestPlugin({
      fileName: 'manifest.json',
      publicPath: '',
      writeToFileEmit: true
    }),

    // Nettoyage du dossier dist
    new CleanWebpackPlugin({
      cleanStaleWebpackAssets: false
    })
  ],

  optimization: {
    // Minimization
    minimize: !isDevelopment,
    minimizer: [
      new TerserPlugin({
        terserOptions: {
          compress: {
            drop_console: !isDevelopment
          }
        }
      }),
      new CssMinimizerPlugin()
    ],

    // Code splitting intelligent
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        // Vendors (node_modules)
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          chunks: 'all',
          priority: 20
        },
        // Bootstrap séparé
        bootstrap: {
          test: /[\\/]node_modules[\\/]bootstrap[\\/]/,
          name: 'bootstrap',
          chunks: 'all',
          priority: 30
        },
        // Hanako-ts séparé
        hanako: {
          test: /[\\/]node_modules[\\/]hanako-ts[\\/]/,
          name: 'hanako',
          chunks: 'all',
          priority: 40
        }
      }
    },

    // Runtime chunk séparé pour un meilleur cache
    runtimeChunk: {
      name: 'runtime'
    }
  },

  // Cache pour des builds plus rapides
  cache: {
    type: 'filesystem',
    buildDependencies: {
      config: [__filename]
    }
  },

  // Stats pour des logs plus propres
  stats: 'errors-only',
};
