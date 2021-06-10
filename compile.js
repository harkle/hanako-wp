const fs = require('fs');
const chokidar = require('chokidar');
const webpack = require('webpack');
const webpackConfig = require('./webpack.config.js');

try {
  fs.rmdirSync(basePath + 'dist', { recursive: true });
} catch (err) {
  console.error('Error while deleting /dist');
}

function buildImports() {
  console.log('> Refresh module list');

  let includes = {
    'scss': [],
    'ts': []
  }

  let paths = [
    'interface/components/',
    'interface/modules/',
    'interface/pages/archives/',
    'interface/pages/defaults/',
    'interface/pages/singles/',
    'interface/pages/templates/',
    'interface/partials/',
  ]

  const regex = /(export class )([A-Za-z]+)( extends)/;

  paths.forEach((path) => {
    fs.readdirSync(basePath + path).forEach(componentFolder => {
      if (fs.existsSync(basePath + path + componentFolder + '/index.scss')) {
        includes.scss.push({ path: path + componentFolder, name: componentFolder });
      }

      if (fs.existsSync(basePath + path + componentFolder + '/index.ts')) {
        const fileContent = fs.readFileSync(basePath + path + componentFolder + '/index.ts', { encoding: 'utf8', flag: 'r' });
        const found = fileContent.match(regex);
        const className = (found) ? found[2] : ''

        if (className) {
          includes.ts.push({ path: path + componentFolder, className: className });
        }
      }
    });
  });

  let scssCode = '';
  includes.scss.forEach(component => {
    scssCode += '@import \'../../' + component.path + '/index.scss\';\n';
  });

  fs.writeFile(basePath + '/src/scss/_components.scss', scssCode, function (err) { });

  let tsImportsCode = '';
  let tsInitCode = '';
  includes.ts.forEach(component => {
    tsImportsCode += 'import { ' + component.className + ' } from \'../../' + component.path + '\';\n';
    tsInitCode += '(new ' + component.className + '()).init();\n';
  });

  fs.writeFile(basePath + '/src/ts/site.ts', tsImportsCode + tsInitCode, function (err) { });
}

let basePath = './';

const watcher = chokidar.watch('interface', {
  ignored: /.twig/,
  persistent: true
});

let changeDebounce;

watcher.on('add', () => {
  clearTimeout(changeDebounce);
  changeDebounce = setTimeout(buildImports, 30000);
});

watcher.on('unlink', () => {
  clearTimeout(changeDebounce);
  changeDebounce = setTimeout(buildImports, 1000);
});

const compiler = webpack(webpackConfig);

compiler.watch({}, (err, stats) => {
  if (err) console.log(err);
  console.log(stats.toString('minimal'));
});
